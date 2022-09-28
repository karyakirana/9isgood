<?php namespace App\Haramain\SistemKeuangan\SubKasir;

use App\Models\Keuangan\HutangPembelian;
use App\Models\Keuangan\PengeluaranPembelian;

class PengeluaranPembelianRepository
{
    /** @noinspection PhpUndefinedFieldInspection */
    public static function kode()
    {
        $query = PengeluaranPembelian::where('active_cash', session('ClosedCash'))
            ->latest('kode');
        if ($query->doesntExist()) {
            return '00001/KP/'. date('Y');
        }
        $num = (int)$query->first()->last_num_char + 1 ?? 1;
        return sprintf("%05s", $num) . "/KP/" . date('Y');
    }

    public static function getDataById($pengeluaranPembelianId)
    {
        return PengeluaranPembelian::find($pengeluaranPembelianId);
    }

    public static function store(array $data)
    {
        $data['kode'] = self::kode();
        $data['active_cash'] = session('ClosedCash');
        $pengeluaranPembelian = PengeluaranPembelian::create($data);
        $pengeluaranPembelian->pengeluaranPembelianDetail()->createMany($data['dataDetail']);
        $pengeluaranPembelian->paymentable()->createMany($data['dataPayment']);
        return $pengeluaranPembelian;
    }

    public static function update(array $data)
    {
        $pengeluaranPembelian = PengeluaranPembelian::find($data['pengeluaran_pembelian_id']);
        $pengeluaranPembelian->update($data);
        $pengeluaranPembelian->pengeluaranPembelianDetail()->createMany($data['dataDetail']);
        $pengeluaranPembelian->paymentable()->createMany($data['dataPayment']);
        return $pengeluaranPembelian->refresh();
    }

    protected static function updateHutangPembelian(array $dataPengeluaranPembelianDetail)
    {
        foreach ($dataPengeluaranPembelianDetail as $item) {
            // update status hutang pembelian
            $hutangPembelian = HutangPembelian::find($item['hutang_pembelian_id']);
            $statusBayar = ($item['kurang_bayar'] == 0) ? 'lunas' : 'kurang';
            $hutangPembelian->update([
                'status_bayar' => $statusBayar,
                'kurang_bayar' => $item['kurang_bayar']
            ]);
            $hutangPembelian->hutangablePembelian()->update(['status_bayar'=>$statusBayar]);
        }
    }

    public static function rollback($pengeluaranPembelianId)
    {
        $pengeluaranPembelian = PengeluaranPembelian::find($pengeluaranPembelianId);
        $pengeluaranPembelian->paymentable()->delete();
        foreach ($pengeluaranPembelian->pengeluaranPembelianDetail as $item) {
            HutangPembelianRollback::fromPengeluaranPembelian($item);
        }
        $pengeluaranPembelian->pengeluaranPembelianDetail()->delete();
        return $pengeluaranPembelian;
    }

    public static function destroy(PengeluaranPembelian $pengeluaranPembelian)
    {
        return self::rollback($pengeluaranPembelian)->delete();
    }
}
