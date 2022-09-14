<?php namespace App\Haramain\SistemPenjualan;

use App\Models\Penjualan\Penjualan;
use App\Models\Penjualan\PenjualanDetail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PenjualanRepository
{
    protected $penjualanId;

    protected $kode;
    protected $activeCash;
    protected $customerId;
    protected $gudangId;
    protected $userId;
    protected $tglNota;
    protected $tglTempo;
    protected $jenisBayar;
    protected $statusBayar;
    protected $totalBarang;
    protected $ppn;
    protected $biayaLain;
    protected $totalBayar;
    protected $keterangan;
    protected $print;

    protected $dataDetail;

    public function __construct($data)
    {
        $this->penjualanId = $data['penjualanId'];

        $this->activeCash = session('ClosedCash');
        $this->kode = $this->kode();
        $this->customerId = $data['customerId'];
        $this->gudangId = $data['gudangId'];
        $this->userId = auth()->id();
        $this->tglNota = $data['tglNota'];
        $this->jenisBayar = $data['jenisBayar'];
        $this->tglTempo = ($data['jenisBayar'] == 'tempo') ? $data['tglTempo'] : null;
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

    protected function kode()
    {
        $query = Penjualan::query()
            ->where('active_cash', session('ClosedCash'))
            ->latest('kode');

        // check last num
        if ($query->doesntExist()){
            return '0001/PJ/'.date('Y');
        }

        $num = (int)$query->first()->last_num_trans + 1 ;
        return sprintf("%04s", $num)."/PJ/".date('Y');
    }

    public function getDataById()
    {
        return Penjualan::query()->findOrFail($this->penjualanId);
    }

    public function getDataAll(bool $closedCash = true)
    {
        $query = Penjualan::query();
        if ($closedCash){
            $query = $query->where('active_cash', session('ClosedCash'));
        }
        return $query;
    }

    /**
     * Simpan data penjualan
     * simpan data detail
     * return nilai penjualan
     */
    public function store()
    {
        $detail = $this->storeDetail();
        $penjualan = Penjualan::query()
            ->create([
                'kode'=>$this->kode,
                'active_cash'=>$this->activeCash,
                'customer_id'=>$this->customerId,
                'gudang_id'=>$this->gudangId,
                'user_id'=>$this->userId,
                'tgl_nota'=>$this->tglNota,
                'tgl_tempo'=>$this->tglTempo,
                'jenis_bayar'=>$this->jenisBayar,
                'status_bayar'=>$this->statusBayar,
                'total_barang'=>$this->totalBarang,
                'ppn'=>$this->ppn,
                'biaya_lain'=>$this->biayaLain,
                'total_bayar'=>$this->totalBayar,
                'keterangan'=>$this->keterangan,
                'print'=>1,
            ]);
        $penjualan->penjualanDetail()->createMany($detail);
        return $penjualan;
    }

    public function update()
    {
        $detail = $this->storeDetail();
        $penjualan = $this->getDataById();
        $penjualan->update([
            'customer_id'=>$this->customerId,
            'gudang_id'=>$this->gudangId,
            'user_id'=>$this->userId,
            'tgl_nota'=>$this->tglNota,
            'tgl_tempo'=>$this->tglTempo,
            'jenis_bayar'=>$this->jenisBayar,
            'status_bayar'=>$this->statusBayar,
            'total_barang'=>$this->totalBarang,
            'ppn'=>$this->ppn,
            'biaya_lain'=>$this->biayaLain,
            'total_bayar'=>$this->totalBayar,
            'keterangan'=>$this->keterangan,
        ]);
        $penjualan->increment('print');
        $penjualan->refresh();
        $penjualan->penjualanDetail()->createMany($detail);
        return $penjualan;
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
                'sub_total'=>$item['sub_total'],
            ];
        }
        $this->totalBarang = array_sum(array_column($detail, 'jumlah'));
        return $detail;
    }
}
