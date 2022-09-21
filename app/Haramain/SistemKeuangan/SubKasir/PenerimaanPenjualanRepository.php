<?php namespace App\Haramain\SistemKeuangan\SubKasir;

use App\Haramain\SistemKeuangan\SubOther\KonfigurasiJurnalRepository;
use App\Models\Keuangan\PenerimaanPenjualan;
use App\Models\Keuangan\PiutangPenjualan;

class PenerimaanPenjualanRepository
{
    protected $penerimaanPenjualanId;
    protected $activeCash;
    protected $kode;
    protected $tglPenerimaan;
    protected $customerId;
    protected $akunKasId;
    protected $nominalPiutang;
    protected $akunPiutangId;
    protected $nominalKas;

    protected $dataDetail;

    public function __construct($data)
    {
        $this->penerimaanPenjualanId = $data['penerimaanPenjualanId'];
        $this->activeCash = session('ClosedCash');
        $this->kode = $this->kode();
        $this->tglPenerimaan = $data['tglPenerimaan'];
        $this->akunKasId = $data['akunKasId'];
        $this->nominalKas = $data['nominalKas'];
        $this->akunPiutangId = KonfigurasiJurnalRepository::build('piutang_usaha')->getAkun();
        $this->nominalPiutang = $data['nominalPiutang'];

        $this->dataDetail = $data['dataDetail'];
    }

    public static function buid($data)
    {
        return new static($data);
    }

    public static function getKode()
    {
        $query = PenerimaanPenjualan::query()
            ->where('active_cash', session('ClosedCash'))
            ->latest('kode');
        $num = (int)$query->first()->last_num_char + 1 ;
        return sprintf("%05s", $num) . "/PP/" . date('Y');
    }

    protected function kode()
    {
        return self::getKode();
    }

    protected function setData()
    {
        $data = [
            'active_cash'=>$this->activeCash,
            'kode'=>$this->kode,
            'tgl_penerimaan'=>$this->tglPenerimaan,
            'customer_id'=>$this->customerId,
            'akun_kas_id'=>$this->akunKasId,
            'nominal_kas'=>$this->nominalKas,
            'akun_piutang_id'=>$this->akunPiutangId,
            'nominal_piutang'=>$this->nominalPiutang,
        ];
        if($this->penerimaanPenjualanId == null){
            return $data;
        }
        unset($data['active_cash']);
        return $data;
    }

    public function updateOrCreate()
    {
        $detail = $this->detailProcess();
        if ($this->penerimaanPenjualanId == null)
        {
            return $this->store($detail);
        }
        return $this->update($detail);
    }

    protected function store($dataDetail)
    {
        $penerimaanPenjualan = PenerimaanPenjualan::create($this->setData());
        $penerimaanPenjualan->penerimaanPenjualanDetail()->createMany($dataDetail);
        return $penerimaanPenjualan->refresh();
    }

    protected function update($dataDetail)
    {
        $penerimaanPenjualan = PenerimaanPenjualan::find($this->penerimaanPenjualanId);
        $penerimaanPenjualan->update($this->setData());
        $penerimaanPenjualan->penerimaanPenjualanDetail()->createMany($dataDetail);
        return $penerimaanPenjualan->refresh();
    }

    protected function detailProcess()
    {
        $detail = [];
        foreach ($this->dataDetail as $item) {
            // update piutang penjualan
            $this->updatePiutangPenjualan($item['piutang_penjualan_id'], $item['status_bayar'], $item['kurang_bayar']);
            $detail[] = [
                'piutang_penjualan_id'=>$item['piutang_penjualan_id'],
                'nominal_dibayar'=>$item['nominal_dibayar'],
                'kurang_bayar'=>$item['kurang_bayar'],
            ];
        }
        return $detail;
    }

    protected function updatePiutangPenjualan($piutangPenjualanId, $statusBayar, $kurangBayar)
    {
        $piutangPenjualan = PiutangPenjualan::find($piutangPenjualanId);
        $piutangPenjualan->update([
            'status_bayar' => $statusBayar,
            'kurang_bayar' => $kurangBayar
        ]);
        $piutangPenjualan->piutangablePenjualan()->update(['status_bayar'=>$statusBayar]);
    }

    /** start static function */

    public static function rollback(PenerimaanPenjualan $penerimaanPenjualan)
    {
        foreach ($penerimaanPenjualan->penerimaanPenjualanDetail as $penerimaanPenjualanDetail) {
            PiutangPenjualanRollback::fromPenerimaanPenjualan($penerimaanPenjualanDetail);
        }
        return $penerimaanPenjualan->penerimaanPenjualanDetail()->delete();
    }

    public static function destroy(PenerimaanPenjualan $penerimaanPenjualan)
    {
        self::rollback($penerimaanPenjualan);
        return $penerimaanPenjualan->delete();
    }
}
