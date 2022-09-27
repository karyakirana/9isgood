<?php namespace App\Haramain\SistemKeuangan\SubKasir;

use App\Models\Keuangan\PiutangInternal;
use App\Models\Keuangan\SaldoPegawai;

class PiutangInternalRepository
{
    public static function kode($jenis_piutang)
    {
        $numJenisPiutang = ($jenis_piutang == 'penerimaan') ? 'PIM' : 'PIK';
        $query = PiutangInternal::where('jenis_piutang', $jenis_piutang);
        $lastKode = ($query->exists()) ? $query->first()->last_num_trans : 0;
        $num = $lastKode + 1;
        return sprintf("%04s", $num)."/$numJenisPiutang/".date('Y');
    }

    protected static function saldoPegawai($pegawai_id, $nominal, $type)
    {
        $nominal = ($type == 'increment') ? $nominal : 0 - $nominal;
        $query = SaldoPegawai::find($pegawai_id);
        return $query ? $query->increment('saldo', $nominal) : SaldoPegawai::create([
            'pegawai_id' => $pegawai_id,
            'saldo' => $nominal
        ]);
    }

    protected static function saldoPegawaiRollback($pegawai_id, $nominal, $type)
    {
        $nominal = ($type == 'increment') ? $nominal : 0 - $nominal;
        $query = SaldoPegawai::find($pegawai_id);
        return $query->decrement('saldo', $nominal);
    }

    public static function getById($piutang_internal_id)
    {
        return PiutangInternal::find($piutang_internal_id);
    }

    public static function store(array $data)
    {
        $data['active_cash'] = session('ClosedCash');
        $data['kode'] = self::kode($data['jenis_piutang']);
        $piutangInternal = PiutangInternal::create($data);
        // update saldo pegawai
        $type = ($data['jenis_piutang'] == 'penerimaan') ? 'increment' : 'decrement';
        self::saldoPegawai($piutangInternal->saldo_pegawai_id, $piutangInternal->nominal, $type);
        // store payment
        $piutangInternal->paymentable()->createMany($data['dataPayment']);
        return $piutangInternal;
    }

    public static function update(array $data)
    {
        $piutangInternal = PiutangInternal::find($data['piutang_internal_id']);
        $piutangInternal->update($data);
        // update saldo pegawai
        $type = ($data['jenis_piutang'] == 'penerimaan') ? 'increment' : 'decrement';
        self::saldoPegawai($piutangInternal->saldo_pegawai_id, $piutangInternal->nominal, $type);
        // store payment
        $piutangInternal->paymentable()->createMany($data['dataPayment']);
        return $piutangInternal->refresh();
    }

    public static function rollback($piutang_internal_id)
    {
        $piutangInternal = PiutangInternal::find($piutang_internal_id);
        $type = ($piutangInternal->jenis_piutang == 'penerimaan') ? 'increment' : 'decrement';
        self::saldoPegawaiRollback($piutangInternal->saldo_pegawai_id, $piutangInternal->nominal, $type);
        $piutangInternal->paymentable()->delete();
        return $piutangInternal->refresh();
    }

    public static function destroy($piutang_internal_id)
    {
        return self::rollback($piutang_internal_id)->delete();
    }
}
