<?php namespace App\Haramain\SistemKeuangan\SubKasir;

use App\Models\Keuangan\PengeluaranPembelian;

class PengeluaranPembelianRepository
{
    public static function kode()
    {
        return null;
    }

    public static function store(array $data)
    {
        $data['kode'] = self::kode();
        $data['active_cash'] = session('ClosedCash');
        $pengeluaranPembelian = PengeluaranPembelian::create($data);
        $pengeluaranPembelian->pengeluaranPembelianDetail()->createMany($data['dataDetail']);
        $pengeluaranPembelian->payementable($data['dataPayment']);
    }

    public static function update(array $data)
    {
        $pengeluaranPembelian = PengeluaranPembelian::find($data['pengeluaran_pembelian_id']);
        $pengeluaranPembelian->update($data);
        $pengeluaranPembelian->pengeluaranPembelianDetail()->createMany($data['dataDetail']);
        $pengeluaranPembelian->payementable($data['dataPayment']);
    }

    protected static function updateHutangPembelian(array $dataPengeluaranPembelianDetail)
    {
        foreach ($dataPengeluaranPembelianDetail as $item) {
            // todo update status hutang pembelian
            // todo update
        }
    }

    public static function rollback(PengeluaranPembelian $pengeluaranPembelian)
    {
        foreach ($pengeluaranPembelian->pengeluaranPembelianDetail() as $item) {
            // todo rollback hutang pembelian
        }
        $pengeluaranPembelian->pengeluaranPembelianDetail()->delete();
        return $pengeluaranPembelian;
    }

    public static function destroy(PengeluaranPembelian $pengeluaranPembelian)
    {
        return self::rollback($pengeluaranPembelian)->delete();
    }
}
