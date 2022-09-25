<?php namespace App\Haramain\SistemKeuangan\SubKasir;

use App\Haramain\ServiceInterface;
use App\Haramain\SistemKeuangan\SubJurnal\JurnalKasRepository;
use App\Haramain\SistemKeuangan\SubJurnal\JurnalTransaksiRepo;
use App\Haramain\SistemKeuangan\SubJurnal\JurnalTransaksiServiceTrait;
use App\Haramain\SistemKeuangan\SubNeraca\NeracaSaldoRepository;
use App\Haramain\SistemKeuangan\SubOther\KonfigurasiJurnalRepository;
use App\Models\Keuangan\PengeluaranPembelian;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PengeluaranPembelianService implements ServiceInterface
{
    use JurnalTransaksiServiceTrait;

    protected $pengeluaranPembelianRepo;
    protected $jurnalKasRepoisitory;
    protected $jurnalTransaksiRepo;
    protected $neracaSaldoRepo;

    protected $akunHutangPembelian;
    protected $akunHutangPembelianInternal;

    public function __construct()
    {
        $this->akunHutangPembelian = KonfigurasiJurnalRepository::build('hutang_dagang')->getAkun();
        $this->akunHutangPembelianInternal = KonfigurasiJurnalRepository::build('hutang_dagang_internal')->getAkun();
    }

    public function handleGetData($id)
    {
        return PengeluaranPembelianRepository::getDataById($id);
    }

    public function handleStore($data)
    {
        DB::beginTransaction();
        try {
            // store pengeluaran pembelian
            $pengeluaranPembelian = PengeluaranPembelianRepository::store($data);
            // jurnal kas
            JurnalKasRepository::storeForPengeluaranPembelian($pengeluaranPembelian);
            // jurnal
            $this->jurnal($pengeluaranPembelian);
            DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>'Pengeluaran Pembelian Berhasil Disimpan'
            ];
        } catch (ModelNotFoundException $e){
            DB::rollBack();
            return (object)[
                'status'=>false,
                'keterangan'=>$e->getMessage()
            ];
        }
    }

    public function handleUpdate($data)
    {
        DB::beginTransaction();
        try {
            $pengeluaranPembelian = PengeluaranPembelianRepository::getDataById($data['pengeluaran_pembelian_id']);
            // rollback
            $this->rollback($pengeluaranPembelian);
            // update pengeluaran
            $pengeluaranPembelian = PengeluaranPembelianRepository::update($data);
            // kas
            JurnalKasRepository::storeForPengeluaranPembelian($pengeluaranPembelian);
            // jurnal
            $this->jurnal($pengeluaranPembelian);
            DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>'Pengeluaran Pembelian Berhasil Disimpan'
            ];
        } catch (ModelNotFoundException $e){
            DB::rollBack();
            return (object)[
                'status'=>false,
                'keterangan'=>$e->getMessage()
            ];
        }
    }

    public function handleDestroy($id)
    {
        DB::beginTransaction();
        try {
            $pengeluaranPembelian = PengeluaranPembelianRepository::getDataById($id);
            $this->rollback($pengeluaranPembelian);
            $pengeluaranPembelian->delete();
            DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>'Pengeluaran Pembelian Berhasil dihapus'
            ];
        } catch (ModelNotFoundException $e){
            DB::rollBack();
            return (object)[
                'status'=>false,
                'keterangan'=>$e->getMessage()
            ];
        }
    }

    protected function jurnal(PengeluaranPembelian $pengeluaranPembelian)
    {
        $jenis = $pengeluaranPembelian->jenis;
        $jurnalTransaksi = JurnalTransaksiRepo::build($pengeluaranPembelian);
        // Jurnal transaksi debet
        if ($jenis === 'BLU'){
            $jurnalTransaksi->debet($this->akunHutangPembelian, $pengeluaranPembelian->total_pengeluaran);
            NeracaSaldoRepository::debet($this->akunHutangPembelian, $pengeluaranPembelian->total_pengeluaran);
        } else {
            $jurnalTransaksi->debet($this->akunHutangPembelianInternal, $pengeluaranPembelian->total_pengeluaran);
            NeracaSaldoRepository::debet($this->akunHutangPembelianInternal, $pengeluaranPembelian->total_pengeluaran);
        }
        // jurnal transaksi kredit
        foreach ($pengeluaranPembelian->paymentable as $payment) {
            $jurnalTransaksi->kredit($payment->akun_id, $payment->nominal);
            NeracaSaldoRepository::kredit($payment->akun_id, $payment->nominal);
        }
    }

    protected function rollback(PengeluaranPembelian $pengeluaranPembelian)
    {
        PengeluaranPembelianRepository::rollback($pengeluaranPembelian->id);
        JurnalKasRepository::rollbackForPengeluaranPembelian($pengeluaranPembelian);
        $this->rollbackJurnalAndSaldo($pengeluaranPembelian);
    }
}
