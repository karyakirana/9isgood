<?php namespace App\Haramain\SistemKeuangan\SubKasir;

use App\Haramain\ServiceInterface;
use App\Haramain\SistemKeuangan\SubJurnal\JurnalKasRepository;
use App\Haramain\SistemKeuangan\SubJurnal\JurnalTransaksiRepo;
use App\Haramain\SistemKeuangan\SubJurnal\JurnalTransaksiServiceTrait;
use App\Haramain\SistemKeuangan\SubNeraca\NeracaSaldoRepository;
use App\Models\KonfigurasiJurnal;

class PengeluaranPembelianService implements ServiceInterface
{
    /**
     * scenario
     * mengeluarkan pembayaran atas hutang pembelian
     *
     * merubah hutang pembelian
     * merubah pembelian
     * merubah saldo hutang pembelian
     * membuat jurnal transaksi
     * merubah neraca saldo
     */

    use JurnalTransaksiServiceTrait;

    protected $pengeluaranPembelianRepo;
    protected $jurnalKasRepoisitory;
    protected $jurnalTransaksiRepo;
    protected $neracaSaldoRepo;

    protected $akunHutangPembelian;
    protected $akunHutangPembelianInternal;

    public function __construct()
    {
        $this->pengeluaranPembelianRepo = new PengeluaranPembelianRepository();
        $this->jurnalKasRepoisitory = new JurnalKasRepository();
        $this->jurnalTransaksiRepo = new JurnalTransaksiRepo();
        $this->neracaSaldoRepo = new NeracaSaldoRepository();

        $this->akunHutangPembelian = KonfigurasiJurnal::query()->firstWhere('config', 'hutang_dagang')->akun_id;
        $this->akunHutangPembelianInternal = KonfigurasiJurnal::query()->firstWhere('config', 'hutang_dagang_internal')->akun_id;
    }

    public function handleGetData($id)
    {
        return $this->pengeluaranPembelianRepo->getDataById($id);
    }

    public function handleStore($data)
    {
        \DB::beginTransaction();
        try {
            // store pengeluaran pembelian
            $pengeluaranPembelian = $this->pengeluaranPembelianRepo->store($data);
            // jurnal kas
            $jurnalKas = $this->jurnalKasRepoisitory->store($data, 'kredit', $pengeluaranPembelian::class, $pengeluaranPembelian->id);
            // jurnal
            $this->jurnal($pengeluaranPembelian, $jurnalKas);
            \DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>'Pengeluaran Pembelian Berhasil Disimpan'
            ];
        } catch (\Exception $e){
            \DB::rollBack();
            return (object)[
                'status'=>false,
                'keterangan'=>$e->getMessage()
            ];
        }
    }

    public function handleUpdate($data)
    {
        \DB::beginTransaction();
        try {
            $pengeluaranPembelian = $this->pengeluaranPembelianRepo->getDataById($data['pengeluaranPembelianId']);
            // rollback
            $this->rollback($pengeluaranPembelian);
            // update pengeluaran
            $pengeluaranPembelian = $this->pengeluaranPembelianRepo->update($data);
            // kas
            $jurnalKas = $this->jurnalKasRepoisitory->update($data, 'kredit', $pengeluaranPembelian::class, $pengeluaranPembelian->id);
            // jurnal
            $this->jurnal($pengeluaranPembelian, $jurnalKas);
            \DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>'Pengeluaran Pembelian Berhasil Disimpan'
            ];
        } catch (\Exception $e){
            \DB::rollBack();
            return (object)[
                'status'=>false,
                'keterangan'=>$e->getMessage()
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
                'keterangan'=>'Pengeluaran Pembelian Berhasil Disimpan'
            ];
        } catch (\Exception $e){
            \DB::rollBack();
            return (object)[
                'status'=>false,
                'keterangan'=>$e->getMessage()
            ];
        }
    }

    protected function jurnal($pengeluaranPembelian, $jurnalKas)
    {
        $jenis = $pengeluaranPembelian->jenis;
        if ($jenis === 'BLU'){
            $this->jurnalTransaksiRepo->debet($pengeluaranPembelian::class, $pengeluaranPembelian->id, $this->akunHutangPembelian, $jurnalKas->nominal_kredit);
            $this->neracaSaldoRepo->debet($this->akunHutangPembelian, $jurnalKas->nominal_kredit);
        } else {
            $this->jurnalTransaksiRepo->debet($pengeluaranPembelian::class, $pengeluaranPembelian->id, $this->akunHutangPembelianInternal, $jurnalKas->nominal_kredit);
            $this->neracaSaldoRepo->debet($this->akunHutangPembelianInternal, $jurnalKas->nominal_kredit);
        }
        $this->jurnalTransaksiRepo->debet($pengeluaranPembelian::class, $pengeluaranPembelian->id, $jurnalKas->akun_id, $jurnalKas->nominal_kredit);
        $this->neracaSaldoRepo->kredit($jurnalKas->akun_id, $jurnalKas->nominal_kredit);
    }

    protected function rollback($pengeluaranPembelian)
    {
        $this->pengeluaranPembelianRepo->rollback($pengeluaranPembelian->id);
        $this->jurnalKasRepoisitory->rollback($pengeluaranPembelian::class, $pengeluaranPembelian->id);
        $this->rollbackJurnalAndSaldo($pengeluaranPembelian);
    }
}
