<?php /** @noinspection PhpClassNamingConventionInspection */

namespace App\Haramain\SistemKeuangan\SubPersediaan;

use App\Models\Keuangan\Persediaan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PersediaanKeluarUpdate
{
    protected $persediaanId;
    protected $jumlah;

    /**
     * @param $persediaanId
     * @param $jumlah
     */
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

    /**
     * update persediaan keluar
     * harus menggunakan exception ModelNotFoundException
     *
     */
    public function updateData()
    {
        $persediaan = $this->getDataById();
        // increment stock_keluar
        $persediaan->increment('stock_keluar', $this->jumlah);
        $persediaan->decrement('stock_saldo', $this->jumlah);
        return $persediaan->refresh();
    }

    /**
     * rollback persediaan keluar
     * harus menggunakan exception ModelNotFoundException
     *
     * @return bool|int
     */
    public function rollbackData(): bool|int
    {
        $persediaan = $this->getDataById();
        // increment stock_keluar
        $persediaan->decrement('stock_keluar', $this->jumlah);
        return $persediaan->increment('stock_saldo', $this->jumlah);
    }

    /**
     * harus menggunakan exception ModelNotFoundException
     *
     */
    public function getDataById()
    {
        return Persediaan::query()->findOrFail($this->persediaanId);
    }
}
