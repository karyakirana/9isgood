<?php namespace App\Haramain\Repository\Neraca;

use App\Models\Keuangan\Akun;
use App\Models\Keuangan\NeracaSaldo;

class NeracaSaldoRepo
{
    protected $neracaSaldo;

    // attributes
    protected $session;
    protected $akunId, $nominal;
    protected $typeAkun;
    protected $field;

    public function __construct($akunId)
    {
        $this->akunId = $akunId;
        $this->session = session('ClosedCash');

        // get akun_tipe
        $akun = Akun::query()->find($akunId);
        $this->typeAkun = $akun->AkunTipe->default_saldo;
    }

    private function create($nominal)
    {
        return NeracaSaldo::query()
            ->create([
                'active_cash'=>$this->session,
                'akun_id'=>$this->akunId,
                'type'=>$this->typeAkun,
                $this->typeAkun => $nominal,
            ]);
    }

    private function query()
    {
        return $this->neracaSaldo->newQuery()
            ->where('active_cash', session('ClosedCash'))
            ->where('akun_id', $this->akunId);
    }

    public function increment($nominal)
    {
        $neracaSaldo = $this->query()->first();
        if ($neracaSaldo){
            // update
            return $neracaSaldo->incrementByType($this->typeAkun, $this->typeAkun, $nominal);
        }
        return $this->create($nominal);
    }

    public function decrement($nominal)
    {
        $neracaSaldo = $this->query()->first();
        return $neracaSaldo->decrementByType($this->typeAkun, $this->typeAkun, $nominal);
    }
}
