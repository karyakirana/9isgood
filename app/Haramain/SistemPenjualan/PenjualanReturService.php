<?php namespace App\Haramain\SistemPenjualan;

use App\Haramain\SistemKeuangan\SubJurnal\JurnalTransaksiRepo;
use App\Haramain\SistemKeuangan\SubNeraca\NeracaSaldoRepository;
use App\Haramain\SistemKeuangan\SubPersediaan\PersediaanTransaksiRepo;
use App\Haramain\SistemStock\StockMasukRepository;
use App\Models\KonfigurasiJurnal;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PenjualanReturService
{
    use PenjualanServiceTrait;

    protected $penjualanReturRepo;
    protected $stockMasukRepository;
    protected $persediaanTransaksiRepo;
    protected $piutangPenjualanRepo;
    protected $jurnalTransaksiRepo;
    protected $neracaSaldoRepository;

    protected $akunPiutangPenjualan;
    protected $akunPenjualanretur;
    protected $akunPPNPenjualan;
    protected $akunBiayaLainPenjualan;
    protected $akunHPP;
    protected $akunPersediaanKalimas;
    protected $akunPersediaanPerak;
    protected $akunPersediaanRusakKalimas;
    protected $akunPersediaanRusakPerak;

    public function __construct()
    {
        $this->penjualanReturRepo = new PenjualanReturRepository();
        $this->stockMasukRepository = new StockMasukRepository();
        $this->persediaanTransaksiRepo = new PersediaanTransaksiRepo();
        $this->jurnalTransaksiRepo = new JurnalTransaksiRepo();
        $this->neracaSaldoRepository = new NeracaSaldoRepository();

        // penjualan retur
        $this->akunPiutangPenjualan = KonfigurasiJurnal::query()->firstWhere('config', 'piutang_usaha')->akun_id;
        $this->akunPenjualanretur = KonfigurasiJurnal::query()->firstWhere('config', 'retur_penjualan')->akun_id;
        $this->akunPPNPenjualan = KonfigurasiJurnal::query()->firstWhere('config', 'ppn_penjualan')->akun_id;
        $this->akunBiayaLainPenjualan = KonfigurasiJurnal::query()->firstWhere('config', 'biaya_penjualan')->akun_id;
        $this->akunHPP = KonfigurasiJurnal::query()->firstWhere('config', 'hpp_internal')->akun_id;
        $this->akunPersediaanKalimas = KonfigurasiJurnal::query()->firstWhere('config', 'persediaan_baik_kalimas')->akun_id;
        $this->akunPersediaanPerak = KonfigurasiJurnal::query()->firstWhere('config', 'persediaan_baik_perak')->akun_id;
        $this->akunPersediaanRusakKalimas = KonfigurasiJurnal::query()->firstWhere('config', 'persediaan_rusak_kalimas')->akun_id;
        $this->akunPersediaanRusakPerak = KonfigurasiJurnal::query()->firstWhere('config', 'persediaan_rusak_perak')->akun_id;
    }

    public function handleGetData($penjualanReturId)
    {
        return $this->penjualanReturRepo->getDataById($penjualanReturId);
    }

    public function handleStore($data)
    {
        \DB::beginTransaction();
        try {
            \DB::commit();
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
        }
    }

    public function handleUpdate($data)
    {
        \DB::beginTransaction();
        try {
            \DB::commit();
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
        }
    }

    public function handleDestroy($penjualanReturId)
    {
        \DB::beginTransaction();
        try {
            \DB::commit();
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
        }
    }

    protected function rollback($penjualanRetur)
    {
        // stock masuk
        // persediaan transaksi
        // penjualan retur
    }

    protected function jurnal($data, $penjualanRetur, $persediaanTransaksi)
    {
        // penjualan retur debet
        // ppn debet
        // biaya lain debet
        // piutang penjualan kredit
        // persediaan debet
        // hpp kredit
    }
}
