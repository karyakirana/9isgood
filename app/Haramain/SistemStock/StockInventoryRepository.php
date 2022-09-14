<?php namespace App\Haramain\SistemStock;

use App\Models\Stock\StockInventory;
use Illuminate\Database\RecordsNotFoundException;

class StockInventoryRepository
{
    protected $kondisi;
    protected $gudangId;
    protected $field;
    protected $produk_id;
    protected $jumlah;

    public function __construct($kondisi, $gudangId, $dataItem)
    {
        $this->kondisi = $kondisi;
        $this->gudangId = $gudangId;
        (is_array($dataItem)) ? $this->setFromArray($dataItem) : $this->setFromObject($dataItem);
    }

    public static function build($kondisi, $gudangId, $dataItem)
    {
        return new static($kondisi, $gudangId, $dataItem);
    }

    protected function setFromArray($dataItem)
    {
        $this->produk_id = $dataItem['produk_id'];
        $this->jumlah = $dataItem['jumlah'];
    }

    protected function setFromObject($dataItem)
    {
        $this->produk_id = $dataItem->produk_id;
        $this->jumlah = $dataItem->jumlah;
    }

    public function update($field)
    {
        $this->field = $field;
        $stock = $this->query();
        if ($stock->doesntExist()){
            return $this->create();
        }
        $stock->increment($this->field, $this->jumlah);
        if ($this->field == 'stock_keluar'){
            return $stock->decrement('stock_saldo', $this->jumlah);
        }
        return $stock->increment('stock_saldo', $this->jumlah);
    }

    public function rollback($field)
    {
        $this->field = $field;
        $query = $this->query();
        if ($query->doesntExist()){
            throw new RecordsNotFoundException('Data Stock Belum Diinputkan sebelumnya');
        }
        $query->decrement($this->field, $this->jumlah);
        if ($this->field == 'stock_keluar'){
            return $query->increment('stock_saldo', $this->jumlah);
        }
        return $query->decrement('stock_saldo', $this->jumlah);
    }

    protected function create()
    {
        return StockInventory::query()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'jenis'=>$this->kondisi,
                'gudang_id'=>$this->gudangId,
                'produk_id'=>$this->produk_id,
                $this->field=>$this->jumlah,
                'stock_saldo'=>($this->field == 'stock_keluar') ? 0 - $this->jumlah : $this->jumlah,
            ]);
    }

    protected function query()
    {
        return StockInventory::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis', $this->kondisi)
            ->where('gudang_id', $this->gudangId)
            ->where('produk_id', $this->produk_id);
    }
}
