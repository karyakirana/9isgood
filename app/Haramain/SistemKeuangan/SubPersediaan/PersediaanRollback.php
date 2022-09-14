<?php namespace App\Haramain\SistemKeuangan\SubPersediaan;

use App\Models\Keuangan\Persediaan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PersediaanRollback
{
    protected $persediaanId;
    protected $jumlah;
    protected $field;

    public function __construct($persediaanId, $jumlah)
    {
        $this->persediaanId = $persediaanId;
        $this->jumlah = $jumlah;
    }

    /**
     * @param ...$params
     * @return static
     * @noinspection PhpMethodNamingConventionInspection
     */
    public static function set(...$params): static
    {
        return new static(...$params);
    }

    protected function getById(): Model|Collection|Builder|array|null
    {
        return Persediaan::query()->findOrFail($this->persediaanId);
    }

    protected function setField($field): void
    {
        $this->field = $field;
    }

    protected function rollback(): bool|int
    {
        $persediaan = $this->getById();
        $persediaan->decrement($this->field, $this->jumlah);
        return ($this->field == 'stock_keluar')
            ? $persediaan->increment('stock_saldo', $this->jumlah)
            : $persediaan->decrement('stock_saldo', $this->jumlah);
    }

    public function rollbackStockMasuk(): bool|int
    {
        $this->setField('stock_masuk');
        return $this->rollback();
    }

    public function rollbackStockKeluar(): bool|int
    {
        $this->setField('stock_keluar');
        return $this->rollback();
    }

    public function rollbackStockOpname(): bool|int
    {
        $this->setField('stock_opname');
        return $this->rollback();
    }
}
