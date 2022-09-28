<?php namespace App\Haramain\SistemKeuangan\SubKasir;

use App\Haramain\ServiceInterface;
use App\Haramain\SistemKeuangan\SubJurnal\JurnalKasRepository;
use App\Haramain\SistemKeuangan\SubJurnal\JurnalTransaksiRepo;
use App\Haramain\SistemKeuangan\SubJurnal\JurnalTransaksiServiceTrait;
use App\Haramain\SistemKeuangan\SubNeraca\NeracaSaldoRepository;
use App\Haramain\SistemKeuangan\SubOther\KonfigurasiJurnalRepository;
use App\Models\Keuangan\PenerimaanPenjualan;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PenerimaanPenjualanService implements ServiceInterface
{
    use JurnalTransaksiServiceTrait;

    // akun piutang penjualan
    protected $akunPiutangPenjualan;

    public function __construct()
    {
         $this->akunPiutangPenjualan = KonfigurasiJurnalRepository::build('piutang_usaha')->getAkun();
    }

    public function handleGetData($id)
    {
        return PenerimaanPenjualan::findOrFail($id);
    }

    public function handleStore($data)
    {
        DB::beginTransaction();
        try {
            // store penerimaan penjualan
            $penerimaanPenjualan = PenerimaanPenjualanRepository::store($data);
            // store jurnal kas
            JurnalKasRepository::storeForPenerimaanPenjualan($penerimaanPenjualan);
            // jurnal transaksi
            $this->jurnal($penerimaanPenjualan);
            DB::commit();
            return [
                'status'=>true,
                'keterangan'=>'data sudah tersimpan'
            ];
        } catch (ModelNotFoundException $e){
            DB::rollBack();
            return [
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }

    public function handleUpdate($data)
    {
        DB::beginTransaction();
        try {
            // initiate
            $penerimaanPenjualan = PenerimaanPenjualan::find($data['penerimaan_penjualan_id']);
            // rollback
            $this->rollback($penerimaanPenjualan);
            // update penerimaan
            $penerimaanPenjualan = PenerimaanPenjualanRepository::update($data);
            // update kas
            JurnalKasRepository::storeForPenerimaanPenjualan($penerimaanPenjualan);
            // jurnal transaksi
            $this->jurnal($penerimaanPenjualan);
            DB::commit();
            return [
                'status'=>true,
                'keterangan'=>'data sudah tersimpan'
            ];
        } catch (ModelNotFoundException $e){
            DB::rollBack();
            return [
                'status'=>false,
                'keterangan'=>$e->getMessage()
            ];
        }
    }

    public function handleDestroy($id)
    {
        DB::beginTransaction();
        try {
            $penerimaanPenjualan = PenerimaanPenjualan::find($id);
            $this->rollback($penerimaanPenjualan);
            $penerimaanPenjualan->delete();
            DB::commit();
            return [
                'status'=>true,
                'keterangan'=>'data sudah tersimpan'
            ];
        } catch (ModelNotFoundException $e){
            DB::rollBack();
            return [
                'status'=>false,
                'keterangan'=>$e->getMessage()
            ];
        }
    }

    protected function jurnal(PenerimaanPenjualan $penerimaanPenjualan)
    {
        $jurnalTransaksi = JurnalTransaksiRepo::build($penerimaanPenjualan);
        // kas masuk
        foreach ($penerimaanPenjualan->jurnalKas as $item) {
            $jurnalTransaksi->debet($item->akun_id, $item->nominal_debet);
            NeracaSaldoRepository::debet($item->akun_id, $item->nominal_debet);
        }
        $jurnalTransaksi->kredit($this->akunPiutangPenjualan, $penerimaanPenjualan->total_penerimaan);
        NeracaSaldoRepository::kredit($this->akunPiutangPenjualan, $penerimaanPenjualan->total_penerimaan);
    }

    protected function rollback(PenerimaanPenjualan $penerimaanPenjualan)
    {
        $this->rollbackJurnalAndSaldo($penerimaanPenjualan);
        JurnalKasRepository::rollbackForPenerimaanPenjualan($penerimaanPenjualan);
        PenerimaanPenjualanRepository::rollback($penerimaanPenjualan->id);
    }
}
