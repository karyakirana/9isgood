<?php namespace App\Haramain\SistemStock;

use App\Haramain\SistemKeuangan\SubJurnal\JurnalTransaksiRepo;
use App\Haramain\SistemKeuangan\SubJurnal\JurnalTransaksiServiceTrait;
use App\Haramain\SistemKeuangan\SubNeraca\NeracaSaldoRepository;
use App\Haramain\SistemKeuangan\SubPersediaan\PersediaanMutasiRepo;
use App\Haramain\SistemKeuangan\SubPersediaan\PersediaanTransaksiRepository;
use App\Models\KonfigurasiJurnal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StockMutasiService
{
    use JurnalTransaksiServiceTrait;

    protected $stockMutasiRepo;
    protected $stockMasukRepo;
    protected $stockKeluarRepo;
    protected $persediaanMutasiRepo;
    protected $persediaanTransaksiRepo;
    protected $jurnalTransaksiRepo;
    protected $neracaSaldoRepo;

    // akun attributes
    protected $akunPersediaanKalimas;
    protected $akunPersediaanPerak;
    protected $akunPersediaanKalimasRusak;
    protected $akunPersediaanPerakRusak;

    public function __construct()
    {
        $this->stockMutasiRepo = new StockMutasiRepository();
        $this->stockMasukRepo = new StockMasukRepository();
        $this->stockKeluarRepo = new StockKeluarRepository();
        $this->persediaanMutasiRepo = new PersediaanMutasiRepo();
        $this->persediaanTransaksiRepo = new PersediaanTransaksiRepository();
        $this->jurnalTransaksiRepo = new JurnalTransaksiRepo();
        $this->neracaSaldoRepo = new NeracaSaldoRepository();

        $this->akunPersediaanKalimas = KonfigurasiJurnal::query()->firstWhere('config', 'persediaan_baik_kalimas')->akun_id;
        $this->akunPersediaanKalimasRusak = KonfigurasiJurnal::query()->firstWhere('config', 'persediaan_rusak_kalimas')->akun_id;
        $this->akunPersediaanPerak = KonfigurasiJurnal::query()->firstWhere('config', 'persediaan_baik_perak')->akun_id;
        $this->akunPersediaanPerakRusak = KonfigurasiJurnal::query()->firstWhere('config', 'persediaan_rusak_perak')->akun_id;
    }

    /**
     * ambil data berdasarkan parameter
     * @param $stockMutasiId
     * @return Builder|Model|object|null
     */
    public function handleGetData($stockMutasiId)
    {
        return $this->persediaanMutasiRepo->getDataById($stockMutasiId);
    }

    /**
     * ambil data berdasarkan form dengan urutan proses
     * menyimpan data stock mutasi dengan nilai pengembalian object untuk initiate
     * penyimpanan pada stock
     * mengambil data dari persediaan
     * data dari persediaan diproses pada persediaan transaksi
     * penyimpanan pada jurnal transaksi dan update neraca saldo sesuai dari akun akuntansi
     * @param $data
     * @return object
     */
    public function handleStore($data)
    {
        \DB::beginTransaction();
        try {
            // simpan dan initiate stock mutasi
            $stockMutasi = $this->stockMutasiRepo->store($data);
            $kondisiKeluar = \Str::of($data['jenisMutasi'])->before('_');
            $kondisiMasuk = \Str::of($data['jenisMutasi'])->after('_');
            // get data from persediaan
            $dataPersediaanOut = $this->persediaanTransaksiRepo->getPersediaanByDetailForOut($data['dataDetail'], $kondisiKeluar, $data['gudangAsalId']);
            // persediaan mutasi
            $persediaanMutasi = $this->persediaanMutasiRepo->store($data, $stockMutasi->id, $dataPersediaanOut);
            // stock keluar baik
            $stockKeluar = $this->stockKeluarRepo->store($data, $stockMutasi::class, $stockMutasi->id);
            // persediaan transaksi keluar
            $persediaanTransaksiKeluar = $this->persediaanTransaksiRepo->storeTransaksiKeluar($data, $dataPersediaanOut, $persediaanMutasi::class, $persediaanMutasi->id);
            // stock masuk baik
            $stockMasuk = $this->stockMasukRepo->store($data, $stockMutasi::class, $stockMutasi->id);
            // persediaanTransaksiMasuk
            $persediaanTransaksiMasuk = $this->persediaanTransaksiRepo->storeTransaksiMasuk($data, $persediaanMutasi::class, $persediaanMutasi->id, $dataPersediaanOut);

            // initiate jurnal
            $gudangAsalId = ucfirst($stockMutasi->gudangAsal->nama);
            $gudangTujuanId = ucfirst($stockMutasi->gudangTujuan->nama);
            $kondisiKeluar = ($kondisiKeluar == 'baik') ? null : 'Rusak';
            $kondisiMasuk = ($kondisiMasuk == 'baik') ? null : 'Rusak';

            // persediaan tujuan debet
            $this->jurnalTransaksiRepo->debet($stockMutasi::class, $stockMutasi->id, $this->{'akunPersediaan'.$gudangTujuanId.$kondisiMasuk}, $persediaanTransaksiKeluar->totalPersediaanKeluar);
            $this->jurnalTransaksiRepo->kredit($stockMutasi::class, $stockMutasi->id, $this->{'akunPersediaan'.$gudangAsalId.$kondisiKeluar}, $persediaanTransaksiKeluar->totalPersediaanKeluar);
            \DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>'data berhasil disimpan'
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return (object)[
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }

    /**
     * @param $data
     * @return object
     */
    public function handleUpdate($data)
    {
        \DB::beginTransaction();
        try {
            $stockMutasi = $this->stockMutasiRepo->getDataById($data['mutasiId']);
            // dd($stockMutasi);
            $kondisiKeluar = \Str::of($data['jenisMutasi'])->before('_');
            $kondisiMasuk = \Str::of($data['jenisMutasi'])->after('_');
            // rollback first
            $this->rollback($stockMutasi);
            // get data from persediaan
            $dataPersediaanOut = $this->persediaanTransaksiRepo->getPersediaanByDetailForOut($data['dataDetail'], $kondisiKeluar, $data['gudangAsalId']);
            // update stock mutasi
            $this->stockMutasiRepo->update($data);
            // update persediaan mutasi
            $persediaanMutasi = $this->persediaanMutasiRepo->update($data, $stockMutasi->id, $dataPersediaanOut);
            // update stock keluar
            $stockKeluar = $this->stockKeluarRepo->update($data, $stockMutasi::class, $stockMutasi->id);
            // update persediaan keluar
            $persediaanTransaksiKeluar = $this->persediaanTransaksiRepo->updateTransaksiKeluar($data, $dataPersediaanOut, $persediaanMutasi::class, $persediaanMutasi->id);
            // update stock masuk
            $stockMasuk = $this->stockMasukRepo->update($data, $stockMutasi::class, $stockMutasi->id);
            // update persediaan keluar
            $persediaanTransaksiMasuk = $this->persediaanTransaksiRepo->updateTransaksiMasuk($data, $persediaanMutasi::class, $persediaanMutasi->id, $dataPersediaanOut);

            // initiate jurnal
            $gudangAsalId = ucfirst($stockMutasi->gudangAsal->nama);
            $gudangTujuanId = ucfirst($stockMutasi->gudangTujuan->nama);
            $kondisiKeluar = ($kondisiKeluar == 'baik') ? null : 'Rusak';
            $kondisiMasuk = ($kondisiMasuk == 'baik') ? null : 'Rusak';

            // jurnal
            $this->jurnalTransaksiRepo->debet($stockMutasi::class, $stockMutasi->id, $this->{'akunPersediaan'.$gudangTujuanId.$kondisiMasuk}, $persediaanTransaksiKeluar->totalPersediaanKeluar);
            $this->jurnalTransaksiRepo->kredit($stockMutasi::class, $stockMutasi->id, $this->{'akunPersediaan'.$gudangAsalId.$kondisiKeluar}, $persediaanTransaksiKeluar->totalPersediaanKeluar);
            \DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>'data berhasil disimpan'
            ];
        }catch (ModelNotFoundException $e){
            \DB::rollBack();
            return (object)[
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }

    public function destroy($mutasiId)
    {
        \DB::beginTransaction();
        try {
            $stockMutasi = $this->stockMutasiRepo->getDataById($mutasiId);
            // delete stock keluar
            $stockKeluar = $this->stockKeluarRepo->destory($stockMutasi::class, $stockMutasi->id);
            // delete persediaan keluar (belum kelar)
            // delete stock masuk
            // delete persediaan masuk
            // rollback neraca saldo
            // delete jurnal
            // delete persediaan mutasi
            // delete mutasi
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

    protected function rollback($stockMutasi)
    {
        //dd($stockMutasi);
        // initiate persediaan mutasi
        $persediaanMutasi = $this->persediaanMutasiRepo->getDataById($stockMutasi->id);
        // rollback stock keluar
        $stockKeluar = $this->stockKeluarRepo->rollback($stockMutasi::class, $stockMutasi->id);
        // rollback persediaan keluar
        $this->persediaanTransaksiRepo->rollbackKeluar($persediaanMutasi::class, $persediaanMutasi->id);
        // rollback stock masuk
        $stockMasuk = $this->stockMasukRepo->rollback($stockMutasi::class, $stockMutasi->id);
        // rollback persediaan masuk
        $this->persediaanTransaksiRepo->rollbackMasuk($persediaanMutasi::class, $persediaanMutasi->id);
        // rollback persediaan mutasi
        $this->persediaanMutasiRepo->rollback($stockMutasi->id);
        // rollback mutasi
        $stockMutasiRollback = $this->stockMutasiRepo->rollback($stockMutasi->id);
        // rollback neraca saldo
        $this->rollbackJurnalAndSaldo($stockMutasi);
    }
}
