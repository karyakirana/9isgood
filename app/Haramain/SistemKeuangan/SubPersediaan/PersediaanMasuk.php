<?php namespace App\Haramain\SistemKeuangan\SubPersediaan;

use App\Models\Keuangan\Persediaan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PersediaanMasuk extends PersediaanRepository
{
    protected $gudangId;
    protected $kondisi;
    protected $tglInput;
    protected $produkId;
    protected $harga;
    protected $jumlah;

    public function __construct($gudangId, $kondisi, $tglInput, $produkId, $harga, $jumlah)
    {
        $this->gudangId = $gudangId;
        $this->kondisi = $kondisi;
        $this->tglInput = $tglInput;
        $this->produkId = $produkId;
        $this->harga = $harga;
        $this->jumlah = $jumlah;
    }

    /**
     * @param mixed ...$params
     * @return static
     * @noinspection PhpMethodNamingConventionInspection
     */
    public static function set(...$params): static
    {
        return new static(...$params);
    }

    protected function baseQuery()
    {
        // TODO : Query find Persediaan by (session, gudang, kondisi, produk)
        return Persediaan::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('gudang_id', $this->gudangId)
            ->where('jenis', $this->kondisi)
            ->where('produk_id', $this->produkId);
    }

    protected function store()
    {
        // TODO : save new data
        return Persediaan::query()->create([
            'active_cash'=>session('ClosedCash'),
            'jenis'=>$this->kondisi,// baik or buruk
            'tgl_input'=>$this->tglInput,
            'gudang_id'=>$this->gudangId,
            'produk_id'=>$this->produkId,
            'harga'=>$this->harga,
            'stock_masuk' =>$this->jumlah,
            'stock_saldo'=>$this->jumlah,
        ]);
    }

    public function update()
    {
        $query = $this->baseQuery();
        $queryPersediaanTerakhir = $query->latest('tgl_input')->first();
        // TODO : check query
        if ($query->doesntExist() || is_null($queryPersediaanTerakhir) || $queryPersediaanTerakhir->harga != $this->harga ){
            return $this->store();
        }
        $queryPersediaanTerakhir->increment('stock_masuk', $this->jumlah);
        $queryPersediaanTerakhir->increment('stock_saldo', $this->jumlah);
        return $queryPersediaanTerakhir->refresh();
    }
}
