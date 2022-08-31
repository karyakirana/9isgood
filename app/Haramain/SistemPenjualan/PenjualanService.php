<?php namespace App\Haramain\SistemPenjualan;

use App\Haramain\SistemKeuangan\SubJurnal\JurnalTransaksiRepo;
use App\Haramain\SistemKeuangan\SubKasir\PiutangPenjualanRepo;
use App\Haramain\SistemKeuangan\SubNeraca\NeracaSaldoRepository;
use App\Haramain\SistemKeuangan\SubPersediaan\PersediaanTransaksiRepo;
use App\Haramain\SistemStock\StockKeluarRepository;
use App\Models\KonfigurasiJurnal;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PenjualanService
{
    use PenjualanServiceTrait;

    protected $penjualanRepository;
    protected $stockKeluarRepository;
    protected $persediaanTransaksiRepo;
    protected $piutangPenjualanRepo;
    protected $jurnalTransaksiRepo;
    protected $neracaSaldoRepository;

    protected $akunPiutangPenjualan;
    protected $akunPenjualan;
    protected $akunPPNPenjualan;
    protected $akunBiayaLainPenjualan;
    protected $akunHPP;
    protected $akunPersediaanKalimas;
    protected $akunPersediaanPerak;

    public function __construct()
    {
        $this->penjualanRepository = new PenjualanRepository();
        $this->stockKeluarRepository = new StockKeluarRepository();
        $this->persediaanTransaksiRepo = new PersediaanTransaksiRepo();
        $this->piutangPenjualanRepo = new PiutangPenjualanRepo();
        $this->jurnalTransaksiRepo = new JurnalTransaksiRepo();
        $this->neracaSaldoRepository = new NeracaSaldoRepository();

        // penjualan
        $this->akunPiutangPenjualan = KonfigurasiJurnal::query()->firstWhere('config', 'piutang_usaha')->akun_id;
        $this->akunPenjualan = KonfigurasiJurnal::query()->firstWhere('config', 'penjualan')->akun_id;
        $this->akunPPNPenjualan = KonfigurasiJurnal::query()->firstWhere('config', 'ppn_penjualan')->akun_id;
        $this->akunBiayaLainPenjualan = KonfigurasiJurnal::query()->firstWhere('config', 'biaya_penjualan')->akun_id;
        $this->akunHPP = KonfigurasiJurnal::query()->firstWhere('config', 'hpp_internal')->akun_id;
        $this->akunPersediaanKalimas = KonfigurasiJurnal::query()->firstWhere('config', 'persediaan_baik_kalimas')->akun_id;
        $this->akunPersediaanPerak = KonfigurasiJurnal::query()->firstWhere('config', 'persediaan_baik_perak')->akun_id;
    }

    public function handleGetData($penjualanId)
    {
        return $this->penjualanRepository->getDataById($penjualanId);
    }

    public function handleStore($data)
    {
        \DB::beginTransaction();
        try {
            // store penjualan
            $penjualan = $this->penjualanRepository->store($data);
            // store stock keluar dan stock inventory
            $stockKeluar = $this->stockKeluarRepository->store($data, $penjualan::class, $penjualan->id);
            // store persediaan transaksi
            $persediaanTransaksi = $this->persediaanTransaksiRepo->storeTransaksiKeluar($data, $penjualan::class, $penjualan->id);
            // store piutang penjualan
            $this->piutangPenjualanRepo->store($data, $penjualan::class, $penjualan->id);
            $this->jurnal($data, $penjualan, $persediaanTransaksi);
            \DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>$penjualan
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return (object) [
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
            $penjualan = $this->penjualanRepository->getDataById($data['penjualanId']);
            // rollback
            $this->rollback($penjualan);
            // update penjualan
            $this->penjualanRepository->update($data);
            // update stock keluar
            $this->stockKeluarRepository->update($data, $penjualan::class, $penjualan->id);
            // update persediaan transaksi
            $persediaanTransaksi = $this->persediaanTransaksiRepo->updateTransaksiKeluar($data, $penjualan::class, $penjualan->id);
            // update piutang penjualan
            $this->piutangPenjualanRepo->update($data, $penjualan::class, $penjualan->id);
            $this->jurnal($data, $penjualan, $persediaanTransaksi);
            \DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>$penjualan
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return (object) [
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }

    public function handleDestroy($penjualanId)
    {
        \DB::beginTransaction();
        try {
            // initiate
            $penjualan = $this->penjualanRepository->getDataById($penjualanId);
            // destroy stock keluar
            $this->stockKeluarRepository->rollback($penjualan::class, $penjualan->id);
            // destroy persediaan transaksi
            $this->persediaanTransaksiRepo->rollbackKeluar($penjualan::class, $penjualan->id);
            // destroy piutang penjualan
            $this->piutangPenjualanRepo->destroy($penjualan::class, $penjualan->id);
            // destroy penjualan
            $this->penjualanRepository->rollback($penjualanId);
            \DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>$penjualan
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return (object) [
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }

    protected function rollback($penjualan): void
    {
        // stock keluar
        $this->stockKeluarRepository->rollback($penjualan::class, $penjualan->id);
        // persediaan transaksi
        $this->persediaanTransaksiRepo->rollbackKeluar($penjualan::class, $penjualan->id);
        // piutang penjualan
        $this->piutangPenjualanRepo->rollback($penjualan::class, $penjualan->id);
        // penjualan
        $this->penjualanRepository->rollback($penjualan->id);
        // rollback jurnal
        $this->jurnalRollback($penjualan);
    }

    protected function jurnal($data, $penjualan, $persediaanTransaksi)
    {
        // piutang penjualan debet
        $this->jurnalTransaksiRepo->debet($penjualan::class, $penjualan->id, $this->akunPiutangPenjualan, $penjualan->total_bayar);
        $this->neracaSaldoRepository->debet($this->akunPiutangPenjualan, $penjualan->total_bayar);
        // penjualan kredit
        $this->jurnalTransaksiRepo->kredit($penjualan::class, $penjualan->id, $this->akunPenjualan, $data['totalPenjualan']);
        $this->neracaSaldoRepository->kredit($this->akunPenjualan, $data['totalPenjualan']);
        // ppn kredit
        if((int) $penjualan->ppn > 0){
            $this->jurnalTransaksiRepo->kredit($penjualan::class, $penjualan->id, $this->akunPPNPenjualan, $penjualan->ppn);
            $this->neracaSaldoRepository->kredit($this->akunPPNPenjualan, $penjualan->ppn);
        }
        // biaya lain kredit
        if((int) $penjualan->biaya_lain > 0){
            $this->jurnalTransaksiRepo->kredit($penjualan::class, $penjualan->id, $this->akunBiayaLainPenjualan, $penjualan->biaya_lain);
            $this->neracaSaldoRepository->kredit($this->akunBiayaLainPenjualan, $penjualan->biaya_lain);
        }
        // hpp debet berdasarkan persediaan keluar
        $this->jurnalTransaksiRepo->debet($penjualan::class, $penjualan->id, $this->akunHPP, $persediaanTransaksi->totalPersediaanKeluar);
        $this->neracaSaldoRepository->debet($this->akunHPP, $persediaanTransaksi->totalPersediaanKeluar);
        // persediaan by gudang kredit
        $akunGudang = ($penjualan->gudang_id == '1') ? $this->akunPersediaanKalimas : $this->akunPersediaanPerak;
        $this->jurnalTransaksiRepo->kredit($penjualan::class, $penjualan->id, $akunGudang, $persediaanTransaksi->totalPersediaanKeluar);
        $this->neracaSaldoRepository->kredit($akunGudang, $persediaanTransaksi->totalPersediaanKeluar);
    }
}
