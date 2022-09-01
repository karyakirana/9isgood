<?php namespace App\Haramain\SistemPembelian;

use App\Haramain\ServiceInterface;
use App\Haramain\SistemKeuangan\SubJurnal\JurnalTransaksiRepo;
use App\Haramain\SistemKeuangan\SubJurnal\JurnalTransaksiServiceTrait;
use App\Haramain\SistemKeuangan\SubKasir\HutangPembelianRepo;
use App\Haramain\SistemKeuangan\SubNeraca\NeracaSaldoRepository;
use App\Haramain\SistemKeuangan\SubPersediaan\PersediaanTransaksiRepo;
use App\Haramain\SistemStock\StockMasukRepository;
use App\Models\KonfigurasiJurnal;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PembelianService implements ServiceInterface
{
    use JurnalTransaksiServiceTrait;

    protected $pembelianRepository;
    protected $stockMasukRepo;
    protected $hutangPembelianRepo;
    protected $persediaanTransaksiRepo;
    protected $jurnalTransaksiRepo;
    protected $neracaSaldoRepo;

    protected $akunHutangPembelian;
    protected $akunPersediaanKalimas;
    protected $akunPersediaanPerak;
    protected $akunPPNPembelian;
    protected $akunBiayaLainPembelian;

    public function __construct()
    {
        $this->pembelianRepository = new PembelianRepository();
        $this->stockMasukRepo = new StockMasukRepository();
        $this->hutangPembelianRepo = new HutangPembelianRepo();
        $this->persediaanTransaksiRepo = new PersediaanTransaksiRepo();
        $this->jurnalTransaksiRepo = new JurnalTransaksiRepo();
        $this->neracaSaldoRepo = new NeracaSaldoRepository();

        // akun pembelian
        $this->akunHutangPembelian = KonfigurasiJurnal::query()->firstWhere('config', 'hutang_dagang')->akun_id;
        $this->akunPersediaanKalimas = KonfigurasiJurnal::query()->firstWhere('config', 'persediaan_baik_kalimas')->akun_id;
        $this->akunPersediaanPerak = KonfigurasiJurnal::query()->firstWhere('config', 'persediaan_baik_perak')->akun_id;
        $this->akunPPNPembelian = KonfigurasiJurnal::query()->firstWhere('config', 'ppn_pembelian')->akun_id;
        $this->akunBiayaLainPembelian = KonfigurasiJurnal::query()->firstWhere('config', 'biaya_pembelian')->akun_id;
    }

    public function handleStore($data)
    {
        \DB::beginTransaction();
        try {
            // store pembelian
            $pembelian = $this->pembelianRepository->store($data);
            // store stock masuk
            $stockMasuk = $this->stockMasukRepo->store($data, $pembelian::class, $pembelian->id);
            // store hutang pembelian
            $hutangPembelian = $this->hutangPembelianRepo->store($data, $pembelian::class, $pembelian->id);
            // store persediaan transaksi
            $persediaanTransaksi = $this->persediaanTransaksiRepo->storeTransaksiMasuk($data, $pembelian::class, $pembelian->id);
            // store jurnal
            $this->jurnal($pembelian, $pembelian->gudang_id);
            \DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>'Sukses di simpan'
            ];
        }catch (ModelNotFoundException $e){
            \DB::rollBack();
            return (object)[
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }

    public function handleUpdate($data)
    {
        \DB::beginTransaction();
        try {
            // initiate
            $pembelian = $this->pembelianRepository->getDataById($data['pembelianId']);
            // rollback
            $this->rollback($pembelian);
            // update pembelian
            $pembelian = $this->pembelianRepository->update($data);
            // update stock masuk
            $stockMasuk = $this->stockMasukRepo->update($data, $pembelian::class, $pembelian->id);
            // update hutang pembelian
            $hutangPembelian = $this->hutangPembelianRepo->update($data, $pembelian::class, $pembelian->id);
            // update persediaan transaksi
            $persediaanTransaksi = $this->persediaanTransaksiRepo->updateTransaksiMasuk($data, $pembelian::class, $pembelian->id);
            // store jurnal transaksi dan neraca saldo
            $this->jurnal($pembelian, $pembelian->gudang_id);
            \DB::commit();
            return (object)[
                'status'=>true
            ];
        }catch (ModelNotFoundException $e){
            \DB::rollBack();
            return (object)[
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }

    public function handleGetData($id)
    {
        return $this->pembelianRepository->getDataById($id);
    }

    public function handleDestroy($id)
    {
        \DB::beginTransaction();
        try {
            \DB::commit();
            return (object)[
                'status'=>true
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return (object)[
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }

    protected function rollback($pembelian)
    {
        // rollback stock masuk
        $stockMasuk = $this->stockMasukRepo->rollback($pembelian::class, $pembelian->id);
        // rollback hutang pembelian
        $hutangPembelian = $this->hutangPembelianRepo->rollback($pembelian::class, $pembelian->id);
        // rollback persediaan transaksi
        $persediaanTransaksi = $this->persediaanTransaksiRepo->rollbackMasuk($pembelian::class, $pembelian->id);
        // rollback pembelian
        $rollbackPembelian = $this->pembelianRepository->rollback($pembelian->id);
        // rollback jurnal dan neraca saldo
        $this->rollbackJurnal($pembelian);
    }

    protected function jurnal($pembelian, $gudangId)
    {
        $akunGudang = ($pembelian->gudang_id == '1') ? $this->akunPersediaanKalimas : $this->akunPersediaanPerak;
        $persediaan = $pembelian->totalBayar - (int)$pembelian->ppn - (int)$pembelian->biaya_lain;
        // persediaan debet
        $this->jurnalTransaksiRepo->debet($pembelian::class, $pembelian->id, $akunGudang, $persediaan);
        $this->neracaSaldoRepo->debet($akunGudang, $persediaan);
        // biaya lain debet
        if ((int)$pembelian->biaya_lain){
            $this->jurnalTransaksiRepo->debet($pembelian::class, $pembelian->id, $this->akunBiayaLainPembelian, $pembelian->biaya_lain);
            $this->neracaSaldoRepo->debet($this->akunBiayaLainPembelian, $pembelian->biaya_lain);
        }
        // ppn debet
        if ((int)$pembelian->ppn){
            $this->jurnalTransaksiRepo->debet($pembelian::class, $pembelian->id, $this->akunPPNPembelian, $pembelian->ppn);
            $this->neracaSaldoRepo->debet($this->akunPPNPembelian, $pembelian->ppn);
        }
        // hutang pembelian debet
        $this->jurnalTransaksiRepo->kredit($pembelian::class, $pembelian->id, $this->akunHutangPembelian, $pembelian->total_bayar);
        $this->neracaSaldoRepo->kredit($this->akunHutangPembelian, $pembelian->total_bayar);
    }

    protected function rollbackJurnal($pembelian)
    {
        return $this->rollbackJurnalAndSaldo($pembelian);
    }
}
