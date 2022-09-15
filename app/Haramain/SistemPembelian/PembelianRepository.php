<?php namespace App\Haramain\SistemPembelian;

use App\Models\Purchase\Pembelian;

class PembelianRepository
{
    protected $pembelianId;

    protected $kode;
    protected $nomorNota;
    protected $nomorSuratJalan;
    protected $jenis;
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
    protected $print;

    protected $dataDetail;

    public function __construct($data)
    {
        //dd($data);
        $this->pembelianId = $data['pembelianId'];
        $this->kode = $this->kode($data['jenis']);
        $this->nomorNota = $data['nomorNota'];
        $this->nomorSuratJalan = $data['suratJalan'];
        $this->jenis = $data['jenis'];
        $this->activeCash = session('ClosedCash');
        $this->supplierId = $data['supplierId'];
        $this->gudangId = $data['gudangId'];
        $this->userId = $data['userId'];
        $this->tglNota = $data['tglNota'];
        $this->tglTempo = ($data['jenisBayar'] == 'tempo') ? $data['tglTempo'] : null;
        $this->jenisBayar = $data['jenisBayar'];
        $this->statusBayar = 'belum';
        $this->totalBarang = 0;
        $this->ppn = $data['ppn'];
        $this->biayaLain = $data['biayaLain'];
        $this->totalBayar = $data['totalBayar'];
        $this->keterangan = $data['keterangan'];
        $this->print = 1;

        $this->dataDetail = $data['dataDetail'];
    }

    public static function build($data)
    {
        return new static($data);
    }

    private function kode($jenisPembelian)
    {
        $query = Pembelian::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis', $jenisPembelian)
            ->latest('kode');

        $kode = ($jenisPembelian == 'INTERNAL') ? 'PBI' : 'PB';

        // check last num
        if ($query->doesntExist()) {
            return '0001/' .$kode.'/'. date('Y');
        }

        $num = (int)$query->first()->last_num_trans + 1 ;
        return sprintf("%04s", $num) .'/'.$kode.'/'. date('Y');
    }

    public function getDataById()
    {
        return Pembelian::query()->find($this->pembelianId);
    }

    public function store()
    {
        $pembelian = Pembelian::query()->create([
            'kode'=>$this->kode,
            'nomor_nota'=>$this->nomorNota,
            'nomor_surat_jalan'=>$this->nomorSuratJalan,
            'jenis'=>$this->jenis,
            'active_cash'=>$this->activeCash,
            'supplier_id'=>$this->supplierId,
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
            'print'=>$this->print,
        ]);
        $pembelian->pembelianDetail()->createMany($this->storeDetail());
        return $pembelian;
    }

    public function update()
    {
        $pembelian = $this->getDataById();
        $pembelian->update([
            'nomor_nota'=>$this->nomorNota,
            'nomor_surat_jalan'=>$this->nomorSuratJalan,
            'jenis'=>$this->jenis,
            'supplier_id'=>$this->supplierId,
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
        $pembelian->refresh();
        $pembelian->pembelianDetail()->createMany($this->storeDetail());
        return $this->getDataById();
    }

    private function storeDetail()
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
        return $detail;
    }
}
