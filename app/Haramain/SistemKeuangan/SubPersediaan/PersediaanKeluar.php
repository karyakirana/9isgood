<?php /** @noinspection PhpClassNamingConventionInspection */

namespace App\Haramain\SistemKeuangan\SubPersediaan;

use App\Models\Keuangan\Persediaan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\RecordsNotFoundException;

/**
 * Class PersediaanKeluar
 * @extends PersediaanRepository
 * @package App\Haramain\SistemKeuangan\SubPersediaan
 */
class PersediaanKeluar
{
    protected $kondisi;
    protected $gudangId;
    protected $produkId;
    protected $jumlah;

    protected $persediaan;
    protected $saldoPersediaan;
    protected $countPersediaan;
    protected $dataPersediaan;

    /**
     * @param $kondisi
     * @param $gudangId
     * @param $produkId
     * @param $jumlah
     */
    public function __construct($kondisi, $gudangId, $produkId, $jumlah)
    {
        $this->kondisi = $kondisi;
        $this->gudangId = $gudangId;
        $this->produkId = $produkId;
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
     * @return array
     */
    public function getData(): array
    {
        $persediaan = $this->baseQuery();
        $this->saldoPersediaan = $persediaan->sum('stock_saldo');
        $this->countPersediaan = $persediaan->count();
        $this->dataPersediaan = $persediaan->get();

        if ($this->saldoPersediaan > $this->jumlah){
            return $this->getPersediaanSaldoLebihBesar();
        }
        return $this->getPersediaanSaldoLebihKecil();
    }

    /**
     * @return Builder
     */
    protected function baseQuery(): Builder
    {
        $persediaan = Persediaan::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis', $this->kondisi)
            ->where('gudang_id', $this->gudangId)
            ->where('produk_id', $this->produkId)
            ->where(function ($query){
                // TODO: stock saldo >|< 0
                $query->where('stock_saldo', '>', 0)
                    ->orWhere('stock_saldo', '<', 0);
            });
        if ($persediaan->doesntExist()) {
            // jika tidak ada data Throw Exception
            throw new ModelNotFoundException('Data Persediaan Tidak Ditemukan');
        }
        return $persediaan;
    }

    /**
     * @return array
     * @noinspection PhpMethodNamingConventionInspection
     */
    protected function getPersediaanSaldoLebihBesar(): array
    {
        // TODO : Get Persediaan jumlah barang diminta lebih besar dari stock saldo
        $setPersediaan = [];
        for ($x = 0; $x <= ($this->countPersediaan -1); $x++){
            $stockSaldo = $this->dataPersediaan[$x]->stock_saldo;
            $jumlah = ($this->jumlah < $stockSaldo) ? $this->jumlah : $stockSaldo;
            $setPersediaan = $this->setPersediaan($x, $jumlah, $setPersediaan, $jumlah);
            if ($this->jumlah == 0)
                break;
        }
        return $setPersediaan;
    }

    /**
     * @return array
     * @noinspection PhpMethodNamingConventionInspection
     */
    protected function getPersediaanSaldoLebihKecil(): array
    {
        // TODO : get Persediaan jumlah barang diminta lebih kecil dari stock saldo
        $setPersediaan = [];
        for ($x = 0; $x <= ($this->countPersediaan -1); $x++){
            $stockSaldo = $this->dataPersediaan[$x]->stock_saldo;
            $jumlah = ($x == $this->countPersediaan -1) ? $this->jumlah : $stockSaldo;
            $setPersediaan = $this->setPersediaan($x, $jumlah, $setPersediaan, $stockSaldo);
        }
        return $setPersediaan;
    }

    /**
     * @param int $x
     * @param mixed $jumlah
     * @param array $setPersediaan
     * @param mixed $stockSaldo
     * @return array
     */
    protected function setPersediaan(int $x, mixed $jumlah, array $setPersediaan, mixed $stockSaldo): array
    {
        $setPersediaan[] = [
            'persediaan_id' => $this->dataPersediaan[$x]->id,
            'produk_id' => $this->dataPersediaan[$x]->produk_id,
            'gudang_id' => $this->dataPersediaan[$x]->gudang_id,
            'kondisi' => $this->dataPersediaan[$x]->jenis,
            'jumlah' => $jumlah,
            'harga' => $this->dataPersediaan[$x]->harga,
            'sub_total' => $jumlah * $this->dataPersediaan[$x]->harga,
        ];
        $this->jumlah -= $stockSaldo;
        return $setPersediaan;
    }
}
