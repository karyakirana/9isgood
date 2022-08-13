<?php namespace App\Haramain\Service\SistemKeuangan\Kasir;

use App\Haramain\Service\SistemKeuangan\Jurnal\JurnalTransaksiRepo;
use App\Haramain\Service\SistemKeuangan\Jurnal\KasRepo;
use App\Haramain\Service\SistemKeuangan\Neraca\NeracaSaldoRepository;
use App\Models\Keuangan\PenerimaanPenjualan;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PenerimaanPenjualanService
{
    // dependency injection
    protected $penerimaanPenjualanRepo;
    protected $piutangPenjualanRepo;
    protected $jurnalTransaksiRepo;
    protected $neracaSaldo;
    protected $kasRepo;

    // set Data
    protected $mode;
    protected $penerimaan_penjualan_id;
    protected $active_cash;
    protected $kode;
    protected $customer_id;
    protected $tgl_penerimaan;
    protected $akun_kas_id, $nominal_kas;
    protected $akun_piutang_id, $nominal_piutang;
    protected $keterangan;

    // data detail
    protected $data_detail;

    public function __construct($data)
    {
        $this->penerimaanPenjualanRepo = new PenerimaanPenjualanRepo();
        $this->piutangPenjualanRepo = new PiutangPenjualanRepo();
        $this->jurnalTransaksiRepo = new JurnalTransaksiRepo();
        $this->neracaSaldo = new NeracaSaldoRepository();
        $this->kasRepo = new KasRepo();

        // set data
        $data = (is_array($data)) ? (object) $data : $data;
        $this->mode = $data->mode;
        $this->penerimaan_penjualan_id = $data->penerimaan_penjualan_id;
        $this->active_cash = session('ClosedCash');
        $this->kode = $this->handleKode();
        $this->tgl_penerimaan = tanggalan_database_format($data->tgl_penerimaan, 'd-M-Y');
        $this->akun_kas_id = $data->akun_kas_id;
        $this->nominal_kas = $data->nominal_kas;
        $this->akun_piutang_id = $data->akun_piutang_id;
        $this->nominal_piutang = $data->nominal_piutang;
        $this->keterangan = $data->keterangan ?? null;
    }

    public function handleStore($data): object
    {
        DB::beginTransaction();
        try {
            // create penerimaan penjualan
            $penerimaanPenjualan = $this->store();
            // store penerimaan penjualan detail
            $this->storeDetail($penerimaanPenjualan);
            // update piutang penjualan and status penjualan or penjualan_retur
            $this->updateStatusPiutangPenjualan();
            // create jurnal transaksi and update neraca saldo
            $this->storeJurnalAndNeraca($penerimaanPenjualan);
            // create kas debet (update saldo)
            $this->storeKas($penerimaanPenjualan);
            // return id kas masuk
            DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>$penerimaanPenjualan
            ];
        } catch (ModelNotFoundException $e)
        {
            DB::rollBack();
            return (object)[
                'status'=>false,
                'keterangan'=>$e
            ];
        }

    }

    public function handleUpdate()
    {
        // rollback
        // update
    }

    public function handleDestroy()
    {
        //
    }

    public function handleGetData()
    {
        //
    }

    public function handleInitiate()
    {
        //
    }

    /**
     * @return string
     */
    protected function handleKode()
    {
        $query = PenerimaanPenjualan::query()
            ->where('active_cash', session('ClosedCash'))
            ->latest('kode');
        $num = (int)$query->first()->last_num_char + 1 ;
        return sprintf("%05s", $num) . "/PP/" . date('Y');
    }

    /**
     * @return Builder|Model
     */
    protected function store()
    {
        return PenerimaanPenjualan::query()
            ->create([
                'active_cash'=>$this->active_cash,
                'kode'=>$this->kode,
                'customer_id'=>$this->customer_id,
                'akun_kas_id'=>$this->akun_kas_id,
                'nominal_kas'=>$this->nominal_kas,
                'akun_piutang_id'=>$this->akun_piutang_id,
                'nominal_piutang'=>$this->nominal_piutang
            ]);
    }

    /**
     * @return bool|int
     */
    protected function update()
    {
        $penerimaanPenjualan = PenerimaanPenjualan::query()->find($this->penerimaan_penjualan_id);
        return $penerimaanPenjualan->update([
            'customer_id'=>$this->customer_id,
            'akun_kas_id'=>$this->akun_kas_id,
            'nominal_kas'=>$this->nominal_kas,
            'akun_piutang_id'=>$this->akun_piutang_id,
            'nominal_piutang'=>$this->nominal_piutang
        ]);
    }

    /**
     * @param $penerimaanPenjualan
     * @return void
     */
    protected function storeDetail($penerimaanPenjualan)
    {
        $penerimaanPenjualanDetail = $penerimaanPenjualan->penerimaanPenjualanDetail();
        foreach ($this->data_detail as $item) {
            // store piutang_penerimaan_penjualan
            $penerimaanPenjualanDetail->create([
                'nominal_dibayar'=>$item['total_tagihan'] - $item['kurang_bayar'],
                'kurang_bayar'=>$item['kurang_bayar'],
            ]);
            // update status piutang penjualan
            $this->piutangPenjualanRepo->updateStatusPenjualan($item['piutang_penjualan_id'], $item['status_bayar'], $item['kurang_bayar']);
        }
    }

    /**
     * @return void
     */
    protected function updateStatusPiutangPenjualan()
    {
        foreach ($this->data_detail as $item) {
            $this->piutangPenjualanRepo->updateStatusPenjualan($item['piutang_penjualan_id'], $item['status_bayar'], $item['kurang_bayar']);
        }
    }

    /**
     * @param $penerimaanPenjualan
     * @return Builder|Model
     */
    protected function storeKas($penerimaanPenjualan)
    {
        return $this->kasRepo->store(
            $penerimaanPenjualan::class,
            $penerimaanPenjualan->id,
            (object)[
                'type'=>'debet',
                'akun_id'=>$this->akun_kas_id,
                'nominal_debet'=>$this->nominal_kas,
                'nominal_saldo'=>$this->nominal_kas + $this->kasRepo->getLastValue()
            ]
        );
    }

    /**
     * @param $penerimaanPenjualan
     * @param $data
     * @return void
     */
    protected function storeJurnalAndNeraca($penerimaanPenjualan, $data)
    {
        // store debet first
        $this->jurnalTransaksiRepo->createDebet($this->akun_kas_id, PenerimaanPenjualan::class, $penerimaanPenjualan->id, $this->nominal_kas);
        $this->neracaSaldo->updateDebet($this->akun_kas_id, $this->nominal_kas);
        // store kredit after
        $this->jurnalTransaksiRepo->createKredit($this->akun_piutang_id, PenerimaanPenjualan::class, $penerimaanPenjualan->id, $this->nominal_piutang);
        $this->neracaSaldo->updateKredit($this->akun_piutang_id, $this->nominal_piutang);
    }

    protected function setBiayaLain()
    {
        /**
         * mengkategorikan dan menjumlahkan per kategori jenis biaya_lain
         * dari data_detail
         */
    }

    protected function setPPN()
    {
        /**
         * mengkategorikan dan menjumlahkan per kategori jenis biaya_lain
         * dari data_detail
         */
    }

    protected function rollback()
    {
        //
    }
}
