<?php namespace App\Haramain\SistemKeuangan\SubJurnal;

use App\Models\Keuangan\JurnalTransaksi;

class JurnalTransaksiRepo
{
    protected $class;

    public function __construct($class)
    {
        $this->class = $class;
    }

    public static function build($class)
    {
        return new static($class);
    }

    public function getData()
    {
        return JurnalTransaksi::query()
            ->where('jurnal_type', $this->class::class)
            ->where('jurnal_id', $this->class->id)
            ->get();
    }

    public function debet($akunId, $nominal)
    {
        return JurnalTransaksi::query()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'jurnal_type'=>$this->class::class,
                'jurnal_id'=>$this->class->id,
                'akun_id'=>$akunId,
                'nominal_debet'=>$nominal,
            ]);
    }

    public function kredit($akunId, $nominal)
    {
        return JurnalTransaksi::query()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'jurnal_type'=>$this->class::class,
                'jurnal_id'=>$this->class->id,
                'akun_id'=>$akunId,
                'nominal_kredit'=>$nominal,
            ]);
    }

    public function rollback()
    {
        return JurnalTransaksi::query()
            ->where('jurnal_type', $this->class::class)
            ->where('jurnal_id', $this->class->id)
            ->delete();
    }

    public function cleanupByAkun()
    {
        return JurnalTransaksi::query()
            ->where('akun_id', $this->akunId)
            ->where('active_cash', session('ClosedCash'))
            ->delete();
    }

    public function cleanUpByTypeClass()
    {
        return JurnalTransaksi::query()
            ->where('jurnal_type', $this->class::class)
            ->where('active_cash', session('ClosedCash'))
            ->delete();
    }
}
