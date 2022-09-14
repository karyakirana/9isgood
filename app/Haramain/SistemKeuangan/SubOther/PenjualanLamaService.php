<?php namespace App\Haramain\SistemKeuangan\SubOther;

use App\Haramain\ServiceInterface;
use App\Models\KonfigurasiJurnal;
use App\Haramain\SistemKeuangan\SubJurnal\{JurnalTransaksiRepo, JurnalTransaksiServiceTrait};
use App\Haramain\SistemKeuangan\SubNeraca\NeracaSaldoRepository;

class PenjualanLamaService implements ServiceInterface
{
    use JurnalTransaksiServiceTrait;

    protected $penjualanLamaRepo;
    protected $jurnalTransaksiRepo;
    protected $neracaSaldoRepo;

    protected $akunPiutangId;
    protected $akunModalAwal;

    public function __construct()
    {
        $this->penjualanLamaRepo = new PenjualanLamaRepo();
        $this->jurnalTransaksiRepo = new JurnalTransaksiRepo();
        $this->neracaSaldoRepo = new NeracaSaldoRepository();

        $this->akunPiutangId = KonfigurasiJurnal::query()->firstWhere('config', 'piutang_usaha')->akun_id;
        $this->akunModalAwal = KonfigurasiJurnal::query()->firstWhere('config', 'prive_modal_awal')->akun_id;
    }

    public function handleGetData($id)
    {
        return $this->penjualanLamaRepo->getById($id);
    }

    public function handleStore($data)
    {
        \DB::beginTransaction();
        try {
            $penjualanLama = $this->penjualanLamaRepo->store($data);
            $this->jurnal($penjualanLama);
            \DB::commit();
            return (object) [
                'status'=>true,
                'keterangan'=>'Penjualan Lama Berhasil Disimpan'
            ];
        } catch (\Exception $e){
            \DB::rollBack();
            return (object) [
                'status'=>true,
                'keterangan'=>$e->getMessage()
            ];
        }
    }

    public function handleUpdate($data)
    {
        \DB::beginTransaction();
        try {
            $this->rollback($data['penjualanLamaId']);
            $penjualanLama = $this->penjualanLamaRepo->update($data);
            $this->jurnal($penjualanLama);
            \DB::commit();
            return (object) [
                'status'=>true,
                'keterangan'=>'Penjualan Lama Berhasil Disimpan'
            ];
        } catch (\Exception $e){
            \DB::rollBack();
            return (object) [
                'status'=>true,
                'keterangan'=>$e->getMessage()
            ];
        }
    }

    public function handleDestroy($id)
    {
        \DB::beginTransaction();
        try {
            \DB::commit();
            return (object) [
                'status'=>true,
                'keterangan'=>'Penjualan Lama Berhasil Disimpan'
            ];
        } catch (\Exception $e){
            \DB::rollBack();
            return (object) [
                'status'=>true,
                'keterangan'=>$e->getMessage()
            ];
        }
    }

    protected function rollback($penjualanLamaId)
    {
        $penjualanLama = $this->handleGetData($penjualanLamaId);
        // rollback penjualan lama
        $this->penjualanLamaRepo->rollback($penjualanLamaId);
        // rollback jurnal transaksi
        $this->rollbackJurnalAndSaldo($penjualanLama);
    }

    protected function jurnal($penjualanLama)
    {
        // piutang debet
        $this->jurnalTransaksiRepo->debet($penjualanLama::class, $penjualanLama->id, $this->akunPiutangId, $penjualanLama->total_piutang);
        $this->neracaSaldoRepo->debet($this->akunPiutangId, $penjualanLama->total_piutang);
        // modal piutang kredit
        $this->jurnalTransaksiRepo->kredit($penjualanLama::class, $penjualanLama->id, $this->akunModalAwal, $penjualanLama->total_piutang);
        $this->neracaSaldoRepo->kredit($this->akunModalAwal, $penjualanLama->total_piutang);
    }
}
