<?php namespace App\Haramain\SistemKeuangan\SubPersediaan;

use App\Models\Keuangan\Persediaan;

class PersediaanForOpname
{
    protected $persediaanId;
    protected $jenis;
    protected $tglInput;
    protected $gudangId;
    protected $produkId;
    protected $harga;
    protected $jumlah;
    protected $type; // increment or decrement

    public function __construct(array|null $data = null)
    {
        if($data){
            $this->persediaanId = $data['persediaanId'] ?? null;
            $this->jenis = $data['kondisi'];
            $this->tglInput = $data['tglInput'];
            $this->gudangId = $data['gudangId'];
            $this->produkId = $data['produkId'];
            $this->harga = $data['harga'];
            $this->jumlah = $data['jumlah'];
            $this->type = ($data['type'] == 'decrement') ? 'decrement' : 'increment';
        }
    }

    public static function build($data = null)
    {
        return new static($data);
    }

    protected function getById()
    {
        return Persediaan::find($this->persediaanId);
    }

    protected function baseQuery()
    {
        return Persediaan::where('active_cash', session('ClosedCash'))
            ->where('jenis', $this->jenis)
            ->where('gudang_id', $this->gudangId)
            ->where('produk_id', $this->produkId)
            ->where('harga', $this->harga);
    }

    public function store()
    {
        $query = $this->baseQuery();
        $type = ($this->type == 'decrement') ? 'decrement' : 'increment';
        if ($this->persediaanId){
            // update
            return $this->update($this->persediaanId, $this->jumlah, $type);
        }

        if ($query->exists()){
            $query->{$type}('stock_opname', $this->jumlah);
            $query->{$type}('stock_saldo', $this->jumlah);
            return $this->baseQuery()->first();
        }
        // create
        $jumlah = ($this->type == 'decrement') ? 0 - $this->jumlah : $this->jumlah;
        return Persediaan::create([
            'active_cash'=>session('ClosedCash'),
            'jenis'=>$this->jenis,// baik or buruk
            'tgl_input'=>$this->tglInput,
            'gudang_id'=>$this->gudangId,
            'produk_id'=>$this->produkId,
            'harga'=>$this->harga,
            'stock_opname'=>$jumlah,
            'stock_saldo'=>$jumlah,
        ]);
    }

    public static function updateData($persediaanId, $jumlah, $type)
    {
        return self::update($persediaanId, $jumlah, $type);
    }

    protected function update($persediaanId, $jumlah, $type)
    {
        $type = ($type == 'decrement') ? 'decrement' : 'increment';
        $persediaan = Persediaan::query()->findOrFail($persediaanId);
        $persediaan->{$type}('stock_opname', $jumlah);
        $persediaan->{$type}('stock_saldo', $jumlah);
        return $persediaan->refresh();
    }
}
