<?php namespace App\Haramain\SistemKeuangan\SubJurnal;

use App\Haramain\SistemKeuangan\SubNeraca\SaldoKasRepository;
use App\Models\Keuangan\JurnalKas;
use Exception;

class JurnalKasRepository
{
    protected $saldoKasRepository;

    public function __construct()
    {
        $this->saldoKasRepository = new SaldoKasRepository();
    }

    public function kode()
    {
        // kode kas by akun id
        return null;
    }

    public function getDataById($cashableType, $cashableId)
    {
        return JurnalKas::query()
            ->where('cash_type', $cashableType)
            ->where('cash_id', $cashableId)
            ->firstOrFail();
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
