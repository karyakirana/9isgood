<?php namespace App\Haramain\Repository\Jurnal;

use App\Models\Keuangan\PenerimaanPenjualan;
use App\Models\Keuangan\PenerimaanPenjualanDetail;

class PenerimaanPenjualanRepo
{
    protected $penerimaanPenjualan;
    protected $penerimaanPenjualanDetail;

    public function __construct()
    {
        $this->penerimaanPenjualan = new PenerimaanPenjualan();
        $this->penerimaanPenjualanDetail = new PenerimaanPenjualanDetail();
    }

    protected function kode()
    {
        return null;
    }

    public function store($data)
    {
        $penerimaan = $this->penerimaanPenjualan->newQuery()
            ->create([
                'active_cash',
                'kode'=>$this->kode(),
                'tgl_penerimaan'=>tanggalan_database_format($data['tglPenerimaan'], 'd-M-Y'),
                'customer_id'=>$data['customerId'],
                'akun_kas_id'=>$data['akunKasId'],
                'nominal_kas'=>$data['nominal'],
                'akun_piutang_id'=>$data['akunPiutangId'],
                'nominal_piutang'=>$data['nominal'],
            ]);
        $this->storeDetail($data['dataDetail'], $penerimaan->id);
        return $penerimaan;
    }

    protected function storeDetail($dataDetail, $penerimaanId)
    {
        foreach ($dataDetail as $item) {
            $this->penerimaanPenjualanDetail->newQuery()->create([
                'penerimaan_penjualan_id'=>$penerimaanId,
                'piutang_penjualan_id'=>$item['piutangPenjualanId'],
                'nominal_dibayar'=>$item['nominalDibayar'],
                'kurang_bayar'=>$item['kurangBayar'],
            ]);
        }
    }

    public function update($data)
    {
        $penerimaan = $this->penerimaanPenjualan->newQuery()->find($data['penerimaanId']);
        $penerimaan->update([
            'tgl_penerimaan'=>tanggalan_database_format($data['tglPenerimaan'], 'd-M-Y'),
            'customer_id'=>$data['customerId'],
            'akun_kas_id'=>$data['akunKasId'],
            'nominal_kas'=>$data['nominal'],
            'akun_piutang_id'=>$data['akunPiutangId'],
            'nominal_piutang'=>$data['nominal'],
        ]);
        $this->storeDetail($data['dataDetail'], $penerimaan->id);
        return $penerimaan;
    }

    public function rollback($penerimaanId)
    {
        $penerimaan = $this->penerimaanPenjualan->newQuery()->find($penerimaanId);
        $this->penerimaanPenjualanDetail->newQuery()->where('penerimaan_penjualan_id', $penerimaanId)->delete();
        return $penerimaan;
    }

    public function destroy($penerimaanId)
    {
        return $this->rollback($penerimaanId)->delete();
    }
}
