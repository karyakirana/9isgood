<?php namespace App\Haramain\Repository\Persediaan;

use App\Models\Keuangan\PersediaanTransaksi;

class PersediaanTransaksiRepo
{
    public function kode()
    {
        $query = PersediaanTransaksi::query()
            ->where('active_cash', session('ClosedCash'));

        if ($query->doesntExist()){
            return '0001/PD/'.date('Y');
        }

        $num = (int)$query->first()->last_num_trans + 1 ;
        return sprintf("%04s", $num)."/PD/".date('Y');
    }

    public function store($data)
    {
        $persediaan = PersediaanTransaksi::query()->create([
            'active_cash'=>session('ClosedCash'),
            'kode'=>$this->kode(),
            'jenis'=>$data->jenis, // masuk atau keluar
            'kondisi'=>$data->kondisi, // baik atau rusak
            'gudang_id'=>$data->gudang_id,
            'persediaan_type',
            'persediaan_id',
            'debet',
            'kredit',
        ]);

        foreach ($data->data_detail as $item) {
            $persediaan->persediaan_transaksi_detail()->create([
                'produk_id'=>$item['produk_id'],
                'harga'=>$item['harga'],
                'jumlah'=>$item['jumlah'],
                'sub_total'=>$item['sub_total'] ?? ($item['harga'] * $item['jumlah']),
            ]);
        }
    }
}
