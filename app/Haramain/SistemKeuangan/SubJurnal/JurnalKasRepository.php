<?php namespace App\Haramain\SistemKeuangan\SubJurnal;

use App\Haramain\SistemKeuangan\SubNeraca\SaldoKasRepository;
use App\Models\Keuangan\JurnalKas;
use App\Models\Keuangan\PenerimaanPenjualan;
use App\Models\Keuangan\PengeluaranPembelian;

class JurnalKasRepository
{
    protected $kode;
    protected $activeCash;
    protected $type;
    protected $cashableType;
    protected $cashableId;
    protected $akunId;
    protected $nominalDebet;
    protected $nominalKredit;

    public function __construct(PenerimaanPenjualan|PengeluaranPembelian $model)
    {
        $this->activeCash = session('ClosedCash');
        $this->type = 'masuk';
        $this->kode = $this->kode('masuk');
        $this->cashableType = $model::class;
        $this->cashableId = $model->id;
        $this->akunId = $model->akun_kas_id;
        $this->nominalDebet = $model->nominal_kas;
        $this->nominalKredit = $model->nominal_piutang;
    }

    public static function build(...$params)
    {
        return new static(...$params);
    }

    public static function getKode($type)
    {
        $initial = ($type == 'masuk') ? 'KM' : 'KK';
        $query = JurnalKas::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('type', $type)
            ->latest('kode');
        // check last num
        if ($query->doesntExist()) {
            return '00001/' .$initial.'/'.date('Y');
        }
        $num = (int)$query->first()->last_num_char + 1 ;
        return sprintf("%05s", $num) . "/".$initial."/" . date('Y');
    }

    public function kode($type)
    {
        return self::getKode($type);
    }

    protected function getDataById()
    {
        return JurnalKas::query()
            ->where('cash_type', $this->cashableType)
            ->where('cash_id', $this->cashableId)
            ->first();
    }

    public function store()
    {
        $type = 'increment';
        $saldo = $this->nominalDebet;
        if ($this->type == 'keluar'){
            $type = 'decrement';
            $saldo = $this->nominalKredit;
        }
        SaldoKasRepository::update($this->akunId, $saldo, $type);
        return JurnalKas::create([
            'kode'=>$this->kode,
            'active_cash'=>$this->activeCash,
            'type'=>$this->type,
            'cash_type'=>$this->cashableType,
            'cash_id'=>$this->cashableId,
            'akun_id'=>$this->akunId,
            'nominal_debet'=>$this->nominalDebet,
            'nominal_kredit'=>$this->nominalKredit,
        ]);
    }

    public function update()
    {
        $jurnalkas = $this->getDataById();
        if ($jurnalkas != null){
            return $this->store();
        }
        $type = 'increment';
        $saldo = $this->nominalDebet;
        if ($this->type == 'keluar'){
            $type = 'decrement';
            $saldo = $this->nominalKredit;
        }
        SaldoKasRepository::update($this->akunId, $saldo, $type);
        $jurnalkas->update([
            'type'=>$this->type,
            'cash_type'=>$this->cashableType,
            'cash_id'=>$this->cashableId,
            'akun_id'=>$this->akunId,
            'nominal_debet'=>$this->nominalDebet,
            'nominal_kredit'=>$this->nominalKredit,
        ]);
        return $jurnalkas->refresh();
    }

    public static function rollback(PenerimaanPenjualan|PengeluaranPembelian $model)
    {
        $jenis = class_basename($model::class);
        $type = 'increment';
        $saldo = $model->nominal_kas;
        if ($jenis == 'PenjualanRetur'){
            $type = 'decrement';
            $saldo = $model->nominal_piutang;
        }
        return SaldoKasRepository::update($model->akun_kas_id, $saldo, $type);
    }
    public static function destroy(PenerimaanPenjualan|PengeluaranPembelian $model)
    {
        self::rollback($model);
        return $model->delete();
    }
}
