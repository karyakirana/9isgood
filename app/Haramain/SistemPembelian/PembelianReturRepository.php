<?php namespace App\Haramain\SistemPembelian;

use App\Models\Purchase\PembelianRetur;
use Auth;

class PembelianReturRepository
{
    protected $pembelianReturId;
    protected $kode;
    protected $jenis;
    protected $kondisi;
    protected $activeCash;
    protected $supplierId;
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

    protected $dataDetail;

    public function __construct($data)
    {
        $this->pembelianReturId = $data['pembelianReturId'];
        $this->jenis = $data['jenis'];
        $this->kondisi = $data['kondisi'];
        $this->kode = $this->kode($data['kondisi']);
        $this->activeCash = session('ClosedCash');
        $this->gudangId = $data['gudangId'];
        $this->userId = Auth::id();
        $this->tglNota = $data['tglNota'];
        $this->tglTempo = ($data['jenisBayar'] == 'tempo') ? $data('tglTempo') : null;
        $this->jenisBayar = $data['jenisBayar'];
        $this->statusBayar = $data['statusBayar'];
        $this->totalBarang = $data['totalBarang'];
        $this->ppn = $data['ppn'];
        $this->biayaLain = $data['biayaLain'];
        $this->keterangan = $data['keterangan'];

        $this->dataDetail = $data['dataDetail'];
    }

    public static function build(...$params)
    {
        return new static(...$params);
    }

    protected function kode($jenis)
    {
        // query
        $query = PembelianRetur::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis', $jenis)
            ->latest('kode');

        $kodeJenis = ($jenis == 'INTERNAL') ? 'PBRI' : 'PBRE';

        // check last num
        if ($query->doesntExist()) {
            return '0001/'.$kodeJenis.'/' . date('Y');
        }

        $num = (int)$query->first()->last_num_trans + 1 ;
        return sprintf("%04s", $num) . "/".$kodeJenis."/" . date('Y');
    }

    public function getDataById()
    {
        return PembelianRetur::query()->find($this->pembelianReturId);
    }

    public function store()
    {
        $pembelianRetur = PembelianRetur::query()
            ->create([
                'kode'=>$this->kode,
                'active_cash'=>$this->activeCash,
                'supplier_id'=>$this->supplierId,
                'gudang_id'=>$this->gudangId,
                'user_id'=>$this->userId,
                'tgl_nota'=>$this->tglNota,
                'tgl_tempo'=>($this->jenisBayar == 'tempo') ? $this->tglTempo : null,
                'jenis_bayar'=>$this->jenisBayar,
                'status_bayar'=>$this->statusBayar,
                'total_barang'=>$this->totalBarang,
                'ppn'=>$this->ppn,
                'biaya_lain'=>$this->biayaLain,
                'total_bayar'=>$this->totalBayar,
                'keterangan'=>$this->keterangan,
                'print'=>1,
            ]);
        $pembelianRetur->returDetail()->createMany($this->storeDetail());
        return $pembelianRetur;
    }

    public function update()
    {
        $pembelianRetur = $this->getDataById();
        $pembelianRetur->update([
            'supplier_id'=>$this->supplierId,
            'gudang_id'=>$this->gudangId,
            'user_id'=>$this->userId,
            'tgl_nota'=>$this->tglNota,
            'tgl_tempo'=>($this->jenisBayar == 'tempo') ? $this->tglTempo : null,
            'jenis_bayar'=>$this->jenisBayar,
            'status_bayar'=>'belum',
            'total_barang'=>$this->totalBarang,
            'ppn'=>$this->ppn,
            'biaya_lain'=>$this->biayaLain,
            'total_bayar'=>$this->totalBayar,
            'keterangan'=>$this->keterangan,
        ]);
        $pembelianRetur->increment('print');
        $pembelianRetur->returDetail()->createMany($this->storeDetail());
        return $pembelianRetur;
    }

    protected function storeDetail()
    {
        $detail = [];
        foreach ($this->dataDetail as $item) {
            $detail[]= [
                'produk_id'=>$item->produk_id,
                'harga'=>$item->harga,
                'jumlah'=>$item->jumlah,
                'diskon'=>$item->diskon,
                'sub_total'=>$item->sub_total,
            ];
        }
        return $detail;
    }
}
