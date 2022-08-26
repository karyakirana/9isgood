<?php namespace App\Haramain\Repository\Persediaan;

use App\Haramain\Repository\Neraca\NeracaSaldoRepository;
use App\Models\Keuangan\PersediaanOpname;
use App\Models\Keuangan\PersediaanOpnameDetail;

class PersediaanOpnameRepository
{
    protected $persediaanOpname;
    protected $persediaanOpnameDetail;

    public function __construct()
    {
        $this->persediaanOpname = new PersediaanOpname();
        $this->persediaanOpnameDetail = new PersediaanOpnameDetail();
    }

    public function getDataAll()
    {
        return $this->persediaanOpname->newQuery()
            ->where('active_cash', session('ClosedCash'))
            ->get();
    }

    public function kode($kondisi): string
    {
        $query = PersediaanOpname::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('kondisi', $kondisi)
            ->latest('kode');

        $kode = ($kondisi == 'baik') ? 'POB' : 'POR';

        // check last num
        if ($query->doesntExist()){
            return "0001/{$kode}/".date('Y');
        }

        $num = (int)$query->first()->last_num_trans + 1 ;
        return sprintf("%04s", $num)."/{$kode}/".date('Y');
    }

    public function store($data)
    {
        // initiate
        $field = 'stock_opname';
        $neracaSaldoRepo = new NeracaSaldoRepository();
        // simpan persediaan opname
        $persediaanOpname = PersediaanOpname::query()->create([
            'kode'=>$this->kode($data->kondisi),
            'active_cash'=>session('ClosedCash'),
            'kondisi'=>$data->kondisi,
            'gudang_id'=>$data->gudang_id,
            'stock_opname_id'=>$data->stock_opname_id ?? null,
            'user_id'=>\Auth::id(),
            'keterangan'=>$data->keterangan,
        ]);

        // foreach for detail
        return $this->storeForDetail($data, $persediaanOpname, $field);
    }

    public function update($data)
    {
        // initiate
        $field = 'stock_opname';
        $persediaanOpname = PersediaanOpname::query()->find($data->persediaan_opname_id);
        $jurnalTransaksi = $persediaanOpname->jurnal_transaksi();

        // rollback neraca saldo and data rollback from jurnal transaksi
        foreach($persediaanOpname->jurnal_transaksi as $row){
            (new NeracaSaldoRepository())->rollback($row);
        }

        // rollback
        foreach ($persediaanOpname->persediaan_opname_detail as $row){
            (new PersediaanRepository())->rollbackObject($persediaanOpname, $row, $field);
            (new PersediaanAwalTempRepo())->increment($persediaanOpname, $row);
        }

        // delete jurnal transaksi
        $jurnalTransaksi->delete();

        // delete persediaan_opname_detail
        $persediaanOpnameDetail = $persediaanOpname->persediaan_opname_detail();
        $persediaanOpnameDetail->delete();

        /**
         * update
         */
        $persediaanOpname->update([
            'gudang_id'=>$data->gudang_id,
            'stock_opname_id'=>$data->stock_opname_id ?? null,
            'user_id'=>\Auth::id(),
            'keterangan'=>$data->keterangan,
        ]);

        // foreach for detail
        return $this->storeForDetail($data, $persediaanOpname, $field);

    }

    /**
     * @param $data
     * @param \Illuminate\Database\Eloquent\Model|array|null $persediaanOpname
     * @param string $field
     * @return mixed
     */
    protected function storeForDetail($data, \Illuminate\Database\Eloquent\Model|array|null $persediaanOpname, string $field): mixed
    {
        foreach ($data->data_detail as $row) {
            // simpan persediaan opname detail
            $persediaanOpname->persediaan_opname_detail()->create([
                'produk_id' => $row['produk_id'],
                'jumlah' => $row['jumlah'],
                'harga' => $row['harga'],
                'sub_total' => $row['sub_total'],
            ]);
            // update persediaan untuk masing-masing detail
            (new PersediaanRepository())->update($persediaanOpname, $row, $field);
            (new PersediaanAwalTempRepo())->decrement($persediaanOpname, $row);
        }
        // buat jurnal transaksi
        $jurnalTransaksi = $persediaanOpname->jurnal_transaksi();
        if ($data->gudang_id == '1'){
            // debet pada persediaan-awal (konfigurasi akun persediaan awal)
            $jurnalTransaksi->create([
                'active_cash' => session('ClosedCash'),
                'akun_id' => $data->akun_persediaan_awal_kalimas,
                'nominal_debet' => $data->total_persediaan,
                'nominal_kredit' => null,
                'keterangan' => $data->keterangan
            ]);
        }

        if ($data->gudang_id == '2'){
            // debet pada persediaan-awal (konfigurasi akun persediaan awal)
            $jurnalTransaksi->create([
                'active_cash' => session('ClosedCash'),
                'akun_id' => $data->akun_persediaan_awal_perak,
                'nominal_debet' => $data->total_persediaan,
                'nominal_kredit' => null,
                'keterangan' => $data->keterangan
            ]);
        }

        // kredit pada modal pemilik (konfigurasi akun modal pemilik)
        $jurnalTransaksi->create([
            'active_cash' => session('ClosedCash'),
            'akun_id' => $data->prive_modal_awal,
            'nominal_debet' => null,
            'nominal_kredit' => $data->total_persediaan,
            'keterangan' => $data->keterangan
        ]);
        if($persediaanOpname->gudang->nama == 'kalimas'){
            // update neraca saldo awal debet
            (new NeracaSaldoRepository())->updateOneRow($data->akun_persediaan_awal_kalimas, $data->total_persediaan, null);
        } elseif ($persediaanOpname->gudang->nama == 'perak'){
            // update neraca saldo awal debet
            (new NeracaSaldoRepository())->updateOneRow($data->akun_persediaan_awal_perak, $data->total_persediaan, null);
        }
        // update neraca saldo awal kredit
        (new NeracaSaldoRepository())->updateOneRow($data->prive_modal_awal, null, $data->total_persediaan);

        return $persediaanOpname->id;
    }
}
