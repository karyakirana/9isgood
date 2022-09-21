<?php namespace App\Haramain\SistemKeuangan\SubKasir;

use App\Haramain\ServiceInterface;
use App\Haramain\SistemKeuangan\SubJurnal\JurnalKasRepository;
use App\Haramain\SistemKeuangan\SubJurnal\JurnalTransaksiRepo;
use App\Haramain\SistemKeuangan\SubJurnal\JurnalTransaksiServiceTrait;
use App\Haramain\SistemKeuangan\SubNeraca\NeracaSaldoRepository;
use App\Models\KonfigurasiJurnal;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PenerimaanPenjualanService implements ServiceInterface
{
    /**
     * scenario
     * menerima pembayaran atas piutang penjualan
     *
     * post condition :
     * merubah piutang penjualan
     * merubah penjualan
     * merubah saldo piutang penjualan
     * meembuat jurnal transaksi
     * merubah neraca saldo
     */

    use JurnalTransaksiServiceTrait;

    protected $penerimaanPenjualanRepo;
    protected $jurnalKasRepository;
    protected $jurnalTransaksiRepo;
    protected $neracaSaldoRepo;

    // akun piutang penjualan
    protected $akunPiutangPenjualan;

    public function __construct()
    {
        $this->penerimaanPenjualanRepo = new PenerimaanPenjualanRepo();
        $this->jurnalKasRepository = new JurnalKasRepository();
        $this->jurnalTransaksiRepo = new JurnalTransaksiRepo();
        $this->neracaSaldoRepo = new NeracaSaldoRepository();

        $this->akunPiutangPenjualan = KonfigurasiJurnal::query()->firstWhere('config', 'piutang_usaha')->akun_id;
    }

    public function handleGetData($id)
    {
        return $this->penerimaanPenjualanRepo->getDataById($id);
    }

    public function handleStore($data)
    {
        \DB::beginTransaction();
        try {
            // store penerimaan penjualan
            $penerimaanPenjualan = PenerimaanPenjualanRepository::buid($data)->updateOrCreate();
            // store jurnal kas
            $jurnalKas = JurnalKasRepository::build($penerimaanPenjualan)->store();
            // jurnal transaksi
            $this->jurnal($penerimaanPenjualan, $jurnalKas);
            \DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>'data sudah tersimpan'
            ];
        } catch (ModelNotFoundException $e){
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
            $penerimaan = PenerimaanPenjualanRepository::buid($data)->updateOrCreate();
            // rollback
            JurnalKasRepository::rollback($penerimaan);
            // update penerimaan
            $penerimaan = JurnalKasRepository::build($penerimaan)->update();
            // update kas
            $jurnalKas = JurnalKasRepository::build($penerimaan)->store();
            // jurnal transaksi
            $this->jurnal($penerimaan, $jurnalKas);
            \DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>'data sudah tersimpan'
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return (object)[
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }

    public function handleDestroy($id)
    {
        \DB::beginTransaction();
        try {
            \DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>'data sudah tersimpan'
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return (object)[
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }

    protected function jurnal($penerimaan, $jurnalKas)
    {
        // jurnal transaksi
        $this->jurnalTransaksiRepo->debet($penerimaan::class, $penerimaan->id, $jurnalKas->akun_id, $jurnalKas->nominal_debet);
        $this->jurnalTransaksiRepo->kredit($penerimaan::class, $penerimaan->id, $this->akunPiutangPenjualan, $jurnalKas->nominal_debet);
        // update neraca saldo
        $this->neracaSaldoRepo->debet($jurnalKas->akun_id, $jurnalKas->nominal_debet);
        $this->neracaSaldoRepo->kredit($this->akunPiutangPenjualan, $jurnalKas->nominal_debet);
    }

    /**
     * @throws \Exception
     */
    protected function rollback($penerimaanPenjualan)
    {
        $this->penerimaanPenjualanRepo->rollback($penerimaanPenjualan->id);
        $this->jurnalKasRepository->rollback($penerimaanPenjualan::class, $penerimaanPenjualan->id);
        $this->rollbackJurnalAndSaldo($penerimaanPenjualan);
    }
}
