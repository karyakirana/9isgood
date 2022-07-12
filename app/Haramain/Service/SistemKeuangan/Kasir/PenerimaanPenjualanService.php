<?php namespace App\Haramain\Service\SistemKeuangan\Kasir;

use App\Haramain\Service\SistemKeuangan\Jurnal\JurnalTransaksiRepo;
use App\Haramain\Service\SistemKeuangan\Jurnal\KasRepo;
use App\Models\Keuangan\PenerimaanPenjualan;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PenerimaanPenjualanService
{
    // dependency injection
    protected PenerimaanPenjualanRepo $penerimaanPenjualanRepo;
    protected PiutangPenjualanRepo $piutangPenjualanRepo;
    protected JurnalTransaksiRepo $jurnalTransaksiRepo;
    protected KasRepo $kasRepo;

    public function handleRulesValidation():array
    {
        return [];
    }

    public function handleMessagesValidation(): array
    {
        return [];
    }

    public function handleStore($data): object
    {
        \DB::beginTransaction();
        try {
            // create penerimaan penjualan
            $penerimaanPenjualan = $this->penerimaanPenjualanRepo->store($data);
            // update piutang penjualan and status penjualan or penjualan_retur
            foreach ($data->detail as $item) {
                $this->piutangPenjualanRepo->updateStatusPenjualan($item->piutang_penjualan_id, $item->status, $item->kurang_bayar);
            }
            // create jurnal transaksi
            $this->jurnalTransaksiRepo->createDebet($data->akunDebet, PenerimaanPenjualan::class, $penerimaanPenjualan->id, $data->nominal);
            $this->jurnalTransaksiRepo->createKredit($data->akunKredit, PenerimaanPenjualan::class, $penerimaanPenjualan->id, $data->nominal);
            // create kas debet (update saldo)
            $this->kasRepo->store(PenerimaanPenjualan::class, $penerimaanPenjualan->id, $data);
            // return id kas masuk
            \DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>$penerimaanPenjualan
            ];
        } catch (ModelNotFoundException $e)
        {
            \DB::rollBack();
            return (object)[
                'status'=>false,
                'keterangan'=>$e
            ];
        }

    }

    public function handleUpdate()
    {
        // rollback
        // update
    }

    public function handleDestroy()
    {
        //
    }

    public function handleGetData()
    {
        //
    }

    public function handleInitiate()
    {
        //
    }
}
