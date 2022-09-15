<?php namespace App\Haramain\SistemStock;

use App\Models\Stock\StockMutasi;
use App\Models\Stock\StockMutasiDetail;

class StockMutasiRepository
{
    protected $stockMutasiId;
    protected $activeCash;
    protected $kode;
    protected $jenisMutasi;
    protected $gudangAsalId;
    protected $gudangTujuanId;
    protected $tglMutasi;
    protected $userId;
    protected $keterangan;

    protected $dataDetail;

    public function __construct($data)
    {
        $this->stockMutasiId = $data['mutasiId'];
        $this->activeCash = session('ClosedCash');
        $this->kode = $this->kode($data['jenisMutasi']);
        $this->jenisMutasi = $data['jenisMutasi'];
        $this->gudangAsalId = $data['gudangAsalId'];
        $this->gudangTujuanId = $data['gudangTujuanId'];
        $this->tglMutasi = $data['tglMutasi'];
        $this->userId = auth()->id();
        $this->keterangan = $data['keterangan'];

        $this->dataDetail = $data['dataDetail'];
    }

    public static function build($data)
    {
        return new static($data);
    }

    protected function kode($jenisMutasi)
    {
        $query = StockMutasi::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis_mutasi', $jenisMutasi)
            ->latest('kode');

        $kodeKondisi = ($jenisMutasi == 'baik_baik') ? 'MBB' : 'MBR';
        $kodeKondisi = ($jenisMutasi == 'rusak_rusak') ? 'MRR' : $kodeKondisi;

        // check last num
        if ($query->doesntExist()){
            return "0001/{$kodeKondisi}/".date('Y');
        }

        $num = (int) $query->first()->last_num_trans + 1;
        return sprintf("%04s", $num)."/{$kodeKondisi}/".date('Y');
    }

    public function getDataById()
    {
        return StockMutasi::query()->find($this->stockMutasiId);
    }

    public function store()
    {
        $stockMutasi = StockMutasi::query()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'kode'=>$this->kode($this->jenisMutasi),
                'jenis_mutasi'=>$this->jenisMutasi,
                'gudang_asal_id'=>$this->gudangAsalId,
                'gudang_tujuan_id'=>$this->gudangTujuanId,
                'tgl_mutasi'=>$this->tglMutasi,
                'user_id'=>$this->userId,
                'keterangan'=>$this->keterangan,
            ]);
        $stockMutasi->stockMutasiDetail()->createMany($this->storeDetail());
        return $stockMutasi;
    }

    public function update()
    {
        $stockMutasi = $this->getDataById();
        $stockMutasi->update([
            'gudang_asal_id'=>$this->gudangAsalId,
            'gudang_tujuan_id'=>$this->gudangTujuanId,
            'tgl_mutasi'=>$this->tglMutasi,
            'user_id'=>$this->userId,
            'keterangan'=>$this->keterangan,
        ]);
        $stockMutasi->stockMutasiDetail()->createMany($this->storeDetail());
        return $stockMutasi->refresh();
    }

    protected function storeDetail()
    {
        $detail = [];
        foreach ($this->dataDetail as $item) {
            $detail[] = [
                    'produk_id'=>$item['produk_id'],
                    'jumlah'=>$item['jumlah'],
                ];
        }
        return $detail;
    }
}
