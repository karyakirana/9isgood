<?php namespace App\Haramain\Service\SistemPenjualan\SubPenjualan;

use App\Haramain\Service\SystemCore\SessionTraits;
use App\Models\Penjualan\Penjualan;


class PenjualanRepo
{
    use SessionTraits;

    public string|null $penjualan_id;
    public string $kode;
    public string $active_cash;
    public int $customer_id;
    public int $gudang_id;
    public int $user_id;
    public string|null $tanggal_nota, $tanggal_tempo;
    public string $jenis_bayar;
    public string $status_bayar;
    public int $total_barang;
    public int|null $ppn, $biaya_lain, $total_bayar;
    public string $keterangan;
    public int $print;

    public array $penjualan_detail;

    public Penjualan $penjualan;

    public function __construct($data)
    {
        $data = $this->objectData($data);
        // initiate
        $this->penjualan_id = $data->penjualan_id ?? null;
        $this->kode = $data->kode ?? $this->kode();
        $this->active_cash = $this->sessionActive();
        $this->customer_id = $data->customer_id;
        $this->gudang_id = $data->gudang_id;
        $this->user_id = \Auth::id();
        $this->tanggal_nota = tanggalan_database_format($data->tgl_nota, 'd-m-Y');
        $this->setTglTempo($data->jenisBayar, $data->tglTempo);
        $this->jenis_bayar = $data->jenis_bayar;
        $this->status_bayar = $data->status_bayar ?? 'belum';
        $this->total_barang = $data->total_barang;
        $this->ppn = $data->ppn ?? null;
        $this->biaya_lain = $data->biaya_lain ?? null;
        $this->total_bayar = $data->total_bayar;
        $this->keterangan = $data->keterangan ?? null;
        $this->print = $data->print ?? null;

        $this->penjualan_detail = $data->detail;
    }

    protected function setTglTempo($jenisBayar, $tglTempo): void
    {
        if ($jenisBayar == 'tempo') {
            $this->tanggal_tempo = tanggalan_database_format($tglTempo, 'd-m-Y');
        } else {
            $this->tanggal_tempo = null;
        }
    }

    public function getByActiveSession(): object|array|null
    {
        return $this->penjualan::query()->where('active_cash', $this->active_cash);
    }

    public function getById($penjualan_id): object|null
    {
        return $this->penjualan::query()->find($penjualan_id);
    }

    public function getDetailByMasterId($penjualan_id): array|object|null
    {
        $penjualan = $this->getById($penjualan_id);
        return $penjualan->penjualanDetail;
    }

    protected function kode(): string
    {
        $query = $this->penjualan::query()
            ->where('active_cash', session('ClosedCash'))
            ->latest('kode');

        // check last num
        if ($query->doesntExist()) {
            return '0001/PJ/' . date('Y');
        }

        $num = (int)$query->first()->last_num_trans + 1;
        return sprintf("%04s", $num) . "/PJ/" . date('Y');
    }

    public function store(): object
    {
        if ($this->penjualan_id == null){
            return $this->create();
        }
        return $this->update();
    }

    protected function objectData(object|array $data):object
    {
        if (is_array($data)){
            $data = (object) $data;
        }
        return $data;
    }

    protected function create(): object
    {
        // penjualan model
        $penjualan = $this->penjualan::query()->create([
            'kode'=>$this->kode,
            'active_cash'=>$this->active_cash,
            'customer_id'=>$this->customer_id,
            'gudang_id'=>$this->gudang_id,
            'user_id'=>$this->user_id,
            'tgl_nota'=>$this->tanggal_nota,
            'tgl_tempo'=>$this->tanggal_tempo,
            'jenis_bayar'=>$this->jenis_bayar,
            'status_bayar'=>$this->status_bayar,
            'total_barang'=>$this->total_barang,
            'ppn'=>$this->ppn,
            'biaya_lain'=>$this->biaya_lain,
            'total_bayar'=>$this->total_bayar,
            'keterangan'=>$this->keterangan
        ]);

        $penjualanDetail = $penjualan->penjualanDetail();

        $this->createDetail($penjualanDetail);

        return $penjualan;
    }

    protected function update():object
    {
        $penjualan = $this->penjualan::query()->find($this->penjualan_id);
        $penjualanDetail = $penjualan->penjualanDetail();

        // delete detail before
        $this->destroyDetail($penjualanDetail);

        $penjualan->update([
            'customer_id'=>$this->customer_id,
            'gudang_id'=>$this->gudang_id,
            'user_id'=>$this->user_id,
            'tgl_nota'=>$this->tanggal_nota,
            'tgl_tempo'=>$this->tanggal_tempo,
            'jenis_bayar'=>$this->jenis_bayar,
            'status_bayar'=>$this->status_bayar,
            'total_barang'=>$this->total_barang,
            'ppn'=>$this->ppn,
            'biaya_lain'=>$this->biaya_lain,
            'total_bayar'=>$this->total_bayar,
            'keterangan'=>$this->keterangan
        ]);

        $this->createDetail($penjualanDetail);

        return $penjualan;
    }

    public function destroy(): int
    {
        return $this->penjualan::destroy($this->penjualan_id);
    }

    /**
     * Detail Processing
     */
    protected function createDetail(object $classDetail):void
    {
        foreach ($this->penjualan_detail as $detail){
            $classDetail->insert([
                'produk_id'=>$detail['produk_id'],
                'harga'=>$detail['harga'],
                'jumlah'=>$detail['jumlah'],
                'diskon'=>$detail['diskon'],
                'sub_total'=>$detail['sub_total'],
            ]);
        }
    }

    protected function destroyDetail(object $classDetail)
    {
        return $classDetail->delete();
    }
}
