<?php namespace App\Haramain\SistemPenjualan;

use App\Models\Penjualan\PenjualanRetur;

class PenjualanReturRepository
{
    protected $penjualanReturId;

    protected $kode;
    protected $activeCash;
    protected $jenisRetur;
    protected $customerId;
    protected $gudangId;
    protected $userId;
    protected $tglNota;
    protected $tglTempo;
    protected $statusBayar;
    protected $totalBarang;
    protected $ppn;
    protected $biayaLain;
    protected $totalBayar;
    protected $keterangan;

    protected $dataDetail;

    public function __construct($data)
    {
        $this->penjualanReturId = $data['penjualanReturId'];
        $this->kode = $this->kode($data['kondisi']);
        $this->activeCash = session('ClosedCash');
        $this->jenisRetur = $data['kondisi'];
        $this->customerId = $data['customerId'];
        $this->gudangId = $data['gudangId'];
        $this->userId = $data['userId'];
        $this->tglNota = $data['tglNota'];
        $this->tglTempo = ($data['statusBayar'] == 'tempo') ? $data['tglTempo'] : null;
        $this->statusBayar = 'belum';
        $this->ppn = $data['ppn'];
        $this->biayaLain = $data['biayaLain'];
        $this->totalBayar = $data['totalBayar'];
        $this->keterangan = $data['keterangan'];

        $this->dataDetail = $data['dataDetail'];
    }

    public static function build($data)
    {
        return new static($data);
    }

    protected function kode($kondisi)
    {
        // query
        $query = PenjualanRetur::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis_retur', $kondisi)
            ->latest('kode');

        $kode = ($kondisi == 'baik') ? 'RB' : 'RR';

        // check last num
        if ($query->doesntExist()){
            return "0001/$kode/".date('Y');
        }

        $num = (int)$query->first()->last_num_trans + 1 ;
        return sprintf("%04s", $num)."/$kode/".date('Y');
    }

    protected function getDataById()
    {
        return PenjualanRetur::query()->find($this->penjualanReturId);
    }

    public function store()
    {
        $penjualanRetur = PenjualanRetur::query()
            ->create([
                'kode'=>$this->kode,
                'active_cash'=>$this->activeCash,
                'jenis_retur'=>$this->jenisRetur,
                'customer_id'=>$this->customerId,
                'gudang_id'=>$this->gudangId,
                'user_id'=>$this->userId,
                'tgl_nota'=>$this->tglNota,
                'tgl_tempo'=>$this->tglTempo,
                'status_bayar'=>$this->statusBayar,
                'total_barang'=>$this->totalBarang,
                'ppn'=>$this->ppn,
                'biaya_lain'=>$this->biayaLain,
                'total_bayar'=>$this->totalBayar,
                'keterangan'=>$this->keterangan,
            ]);
        $penjualanRetur->returDetail()->createMany($this->storeDetail());
        return $penjualanRetur;
    }

    public function update()
    {
        $penjualanRetur = $this->getDataById();
        $penjualanRetur->update([
            'jenis_retur'=>$this->jenisRetur,
            'customer_id'=>$this->customerId,
            'gudang_id'=>$this->gudangId,
            'user_id'=>$this->userId,
            'tgl_nota'=>$this->tglNota,
            'tgl_tempo'=>$this->tglTempo,
            'status_bayar'=>$this->statusBayar,
            'total_barang'=>$this->totalBarang,
            'ppn'=>$this->ppn,
            'biaya_lain'=>$this->biayaLain,
            'total_bayar'=>$this->totalBayar,
            'keterangan'=>$this->keterangan,
        ]);
        $penjualanRetur = $penjualanRetur->refresh();
        $penjualanRetur->returDetail()->createMany($this->storeDetail());
        return $penjualanRetur;
    }

    protected function storeDetail()
    {
        $detail = [];
        foreach ($this->dataDetail as $item) {
            $detail[] = [
                'produk_id'=>$item['produk_id'],
                'harga'=>$item['harga'],
                'jumlah'=>$item['jumlah'],
                'diskon'=>$item['diskon'],
                'sub_total'=>$item['sub_total']
            ];
        }
        return $detail;
    }
}
