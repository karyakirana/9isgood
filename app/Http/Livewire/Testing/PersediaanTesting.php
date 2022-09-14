<?php

/** @noinspection PhpPropertyNamingConventionInspection */

/** @noinspection PhpClassNamingConventionInspection */

namespace App\Http\Livewire\Testing;

use App\Models\Keuangan\Persediaan;
use Livewire\Component;

/**
 *
 */
class PersediaanTesting extends Component
{
    protected $persediaan;

    public $produk_id;
    public $jumlah;
    public $gudangId;
    public $kondisi;
    public $harga;

    public $dataDetail = [];

    public $hasil;

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->persediaan = new Persediaan();
    }

    public function hasil()
    {
        $persediaan = $this->persediaan->newQuery()
            ->where('active_cash', session('ClosedCash'))
            ->where('gudang_id', $this->gudangId)
            ->where('jenis', $this->kondisi)
            ->where('produk_id', $this->produk_id)
            ->where('harga', $this->harga);
    }

    public function render()
    {
        return view('livewire.testing.persediaan-testing');
    }
}
