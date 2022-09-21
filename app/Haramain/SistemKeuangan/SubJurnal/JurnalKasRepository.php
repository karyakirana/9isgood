<?php namespace App\Haramain\SistemKeuangan\SubJurnal;

use App\Haramain\SistemKeuangan\SubNeraca\SaldoKasRepository;
use App\Models\Keuangan\JurnalKas;
use Exception;

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

    public function __construct()
    {
        $this->activeCash = session('ClosedCash');
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

    public function store($data, $type, $cashableType, $cashableId)
    {
        $data = (object) $data;
        if($type == 'debet'){
            $field = 'nominal_debet';
            // update saldo kas
            $this->saldoKasRepository->increment($data->akunKasId, $data->totalTransaksiKas);
        } else {
            $field = 'nominal_kredit';
            $this->saldoKasRepository->decrement($data->akunKasId, $data->totalTransaksiKas);
        }
        return JurnalKas::query()
            ->create([
                'kode'=>$this->kode(),
                'active_cash'=>session('ClosedCash'),
                'type'=>$type,
                'cash_type'=>$cashableType,
                'cash_id'=>$cashableId,
                'akun_id'=>$data->akunKasId,
                $field=>$data->totalTransaksiKas
            ]);
    }

    public function update($data, $type, $cashableType, $cashableId)
    {
        $data = (object) $data;
        if($type == 'debet'){
            $field = 'nominal_debet';
            // update saldo kas
            $this->saldoKasRepository->increment($data->akunKasId, $data->totalTransaksiKas);
        } else {
            $field = 'nominal_kredit';
            $this->saldoKasRepository->decrement($data->akunKasId, $data->totalTransaksiKas);
        }
        $this->getDataById($cashableType, $cashableId)->update([
            'type'=>$type,
            'cash_type'=>$cashableType,
            'cash_id'=>$cashableId,
            'akun_id'=>$data->akunKasId,
            $field=>$data->totalTransaksiKas
        ]);
        return $this->getDataById($cashableType, $cashableId);
    }

    /**
     * @throws Exception
     */
    public function destroy($cashableType, $cashableId)
    {
        $this->rollback($cashableType, $cashableId);
        return $this->getDataById($cashableType, $cashableId)->delete();
    }

    /**
     * @throws Exception
     */
    public function rollback($cashableType, $cashableId): void
    {
        $jurnalKas = JurnalKas::query()
            ->where('cash_type', $cashableType)
            ->where('cash_id', $cashableId);
        if ($jurnalKas == 0){
            throw new Exception('Data Jurnal Kas Tidak ada');
        }
        $jurnalKas = $jurnalKas->first();
        if($jurnalKas->type == 'masuk'){
            $this->saldoKasRepository->decrement($jurnalKas->akun_id, $jurnalKas->nominal_debet);
        } else {
            $this->saldoKasRepository->increment($jurnalKas->akun_id, $jurnalKas->nominal_kredit);
        }
    }
}
