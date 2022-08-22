<?php namespace App\Haramain\Service\SistemPenjualan;

use App\Haramain\Service\SistemKeuangan\Jurnal\JurnalPersediaanService;
use App\Haramain\Service\SistemKeuangan\Jurnal\PersediaanRepository;
use App\Haramain\Service\SistemKeuangan\Neraca\NeracaSaldoRepository;
use App\Haramain\Service\SistemKeuangan\Neraca\SaldoPiutangPenjualanRepo;
use App\Haramain\Service\SistemStock\StockKeluarRepo;
use App\Models\Penjualan\Penjualan;
use Illuminate\Support\Facades\Auth;

class PenjualanService
{
    // initiate on construct
    protected $stockKeluarRepo;
    protected $jurnalPersediaanService;
    protected $persediaanRepo;

    // initiate variabel
    protected $penjualan;
    protected $penjualanDetail;
    protected $stockKeluar;
    protected $stockKeluarDetail;
    protected $piutangPenjualan;
    protected $saldoPiutangPenjualan;
    protected $persediaan;
    protected $jurnalTransaksi;
    protected $neracaSaldo;
    protected $jurnalPersediaan;

    // database variabel
    public $penjualanId;
    protected $activeCash;
    protected $kode;
    protected $tglNota, $tglTempo;
    protected $customerId;
    protected $gudangId;
    protected $userId;
    protected $jenisbayar;
    protected $statusBayar;
    protected $jumlahBarang;
    protected $ppn;
    protected $biayaLain;
    protected $totalBayar;
    protected $totalPendapatan; // penjualan sebelum biaya_lain dan ppn
    protected $keterangan;
    public $print;

    // database stock dan persediaan
    public $kondisi;

    protected $dataDetail;
    protected $dataDetailOut;

    // jurnal config variable
    protected $akun_piutang_id;
    protected $akun_pendapatan_id;
    protected $akun_hutang_biaya_lain_id;
    protected $akun_hutang_ppn_id;

    protected $akun_hpp_id, $akun_persediaan_id;

    // mode create or update
    protected $mode;

    public function __construct()
    {
        $this->stockKeluarRepo = new StockKeluarRepo();
        $this->saldoPiutangPenjualan = new SaldoPiutangPenjualanRepo();
        $this->neracaSaldo = new NeracaSaldoRepository();
        $this->jurnalPersediaanService = new JurnalPersediaanService();
        $this->persediaanRepo = new PersediaanRepository();
    }

    public function handleStore($data)
    {
        // exception jumlah barang yang tidak tersedia
        // atau barang kurang dari persediaan
        $checkPersediaan = $this->persediaanRepo->handleExceptionOut($data['dataDetail'], 'baik');
        if (!$checkPersediaan){
            return [
                'status'=>false,
                'keterangan'=>'Jumlah salah satu produk kurang / tidak tersedia'
            ];
        }
        \DB::beginTransaction();
        try {
            // set data
            $this->setData($data);
            // simpan penjualan
            $penjualan = $this->store();
            // initiate penjualanDetail
            $penjualanDetail = $penjualan->penjualanDetail();
            // initiate and store stock
            $this->stockKeluar = $penjualan->stockKeluarMorph();
            $this->stockKeluarDetail = $this->storeStockKeluar()->stockKeluarDetail();
            // proses data detail
            foreach ($data['data_detail'] as $datum) {
                // store penjualan detail
                $this->storeDetail($datum);
                // store stock
                $this->storeStockKeluarDetail($datum);
            }
            // initiate piutangPenjualan
            $this->piutangPenjualan = $penjualan->piutangPenjualan();
            $this->storePiutangPenjualan();
            // initiate jurnaltransaksi
            $this->jurnalTransaksi = $penjualan->jurnal_transaksi();
            // simpan jurnal transaksi dan neraca saldo
            $this->storeJurnalTransaksi();
            // proses persediaan
            $this->jurnalPersediaan = $penjualan->persediaan_transaksi();
            $this->dataDetailOut = $this->persediaanRepo->getPersediaanToOut($this->dataDetail, $this->kondisi);
            // return penjualan id
            \DB::commit();
            return [
                'status'=>false,
                'keterangan'=>$penjualan
            ];
        } catch (\Exception $e){
            \DB::rollBack();
            return [
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }

    public function handleUpdate($data)
    {
        //
    }

    public function handleDelete($penjualanId)
    {
        //
    }

    protected function getKode()
    {
        $query = Penjualan::query()
            ->where('active_cash', session('ClosedCash'))
            ->latest('kode');

        // check last num
        if ($query->doesntExist()){
            return '0001/PJ/'.date('Y');
        }

        $num = (int)$query->first()->last_num_trans + 1 ;
        return sprintf("%04s", $num)."/PJ/".date('Y');
    }

    protected function setData($data)
    {
        $this->mode = $data['mode'];
        if ($this->mode == 'update'){
            $this->penjualanId = $data['penjualanId'];
        } else{
            $this->activeCash = session('ClosedCash');
            $this->kode = $this->getKode();
        }
        $this->tglNota = tanggalan_database_format($data['tglNota'], 'd-M-Y');
        $this->jenisbayar = $data['jenisBayar'];
        $this->tglTempo = ($this->jenisbayar == 'cash' || $this->jenisbayar == 'Tunai') ? null : tanggalan_database_format($data['tglTempo'], 'd-M-Y');
        $this->customerId = $data['customerId'];
        $this->gudangId = $data['gudangId'];
        $this->userId = Auth::id();
        $this->statusBayar = 'belum';
        $this->jumlahBarang = $data['totalBarang'];
        $this->biayaLain = $data['biayaLain'];
        $this->ppn = $data['ppn'];
        $this->totalBayar = $data['totalBayar'];
        $this->totalPendapatan = $data['pendapatan'];
        $this->keterangan = $data['keterangan'];
        $this->dataDetail = $data['dataDetail'];
        $this->print = $data['print'];

        // stock
        $this->kondisi = 'baik';

        // akun pendapatan
        $this->akun_pendapatan_id = $data['akunPendapatanPenjualan'];
        $this->akun_piutang_id = $data['akunPiutangId'];
        $this->akun_hutang_biaya_lain_id = $data['akunHutangBiayaLain'];
        $this->akun_hutang_ppn_id = $data['akunHutangPPNId'];

        // akun persediaan
        $this->akun_persediaan_id = $data['akunPersediaanId'];
        $this->akun_hpp_id = $data['akunHppId'];
    }

    protected function store()
    {
        return Penjualan::query()->create([
            'kode'=>$this->kode,
            'active_cash'=>$this->activeCash,
            'customer_id'=>$this->customerId,
            'gudang_id'=>$this->gudangId,
            'user_id'=>$this->userId,
            'tgl_nota'=>$this->tglNota,
            'tgl_tempo'=>$this->tglTempo,
            'jenis_bayar'=>$this->jenisbayar,
            'status_bayar'=>$this->statusBayar,
            'total_barang'=>$this->totalBayar,
            'ppn'=>$this->ppn,
            'biaya_lain'=>$this->biayaLain,
            'total_bayar'=>$this->totalBayar,
            'keterangan'=>$this->keterangan,
            'print'=>$this->print,
        ]);
    }

    /**
     * Store helper
     */

    protected function storeDetail($dataItem)
    {
        return $this->penjualanDetail->create([
            'produk_id'=>$dataItem['produk_id'],
            'harga'=>$dataItem['harga'],
            'jumlah'=>$dataItem['jumlah'],
            'diskon'=>$dataItem['diskon'],
            'sub_total'=>$dataItem['sub_total'],
        ]);
    }

    protected function storeStockKeluar()
    {
        return $this->stockKeluarRepo->createFromMorph($this->stockKeluar, [
            'tglkeluar'=>$this->tglNota,
            'gudangId'=>$this->gudangId,
            'userId'=>$this->userId,
            'keterangan'=>$this->keterangan
        ]);
    }

    protected function storeStockKeluarDetail($dataItem)
    {
        $this->stockKeluarRepo->storeDetail($this->stockKeluarDetail, $dataItem, $this->gudangId);
    }

    protected function storePiutangPenjualan()
    {
        // update saldo_piutang_penjualan
        $this->saldoPiutangPenjualan->store($this->customerId, 'penjualan', $this->totalBayar);

        return $this->piutangPenjualan->create([
            'saldo_piutang_penjualan_id'=>$this->customerId,
            'status_bayar'=>$this->statusBayar, // enum ['lunas', 'belum', 'kurang']
            'kurang_bayar'=>$this->totalBayar,
        ]);
    }

    protected function storeJurnalTransaksi()
    {
        // store debet (piutang penjualan)
        $this->jurnalTransaksi->create([
            'active_cash'=>$this->activeCash,
            'akun_id'=>$this->akun_piutang_id,
            'nominal_debet'=>$this->totalBayar,
            'nominal_kredit'=>null,
            'keterangan'=>$this->keterangan
        ]);
        $this->neracaSaldo->updateDebet($this->akun_piutang_id, $this->totalBayar);
        // store kredit (pendapatan (sebelum biaya dan ppn))
        $this->jurnalTransaksi->create([
            'active_cash'=>$this->activeCash,
            'akun_id'=>$this->akun_pendapatan_id,
            'nominal_debet'=>null,
            'nominal_kredit'=>$this->totalPendapatan,
            'keterangan'=>$this->keterangan
        ]);
        $this->neracaSaldo->updateKredit($this->akun_pendapatan_id, $this->totalPendapatan);
        // store ppn jika ada
        if ($this->biayaLain > 0 || $this->biayaLain != null){
            $this->jurnalTransaksi->create([
                'active_cash'=>$this->activeCash,
                'akun_id'=>$this->akun_hutang_biaya_lain_id,
                'nominal_debet'=>null,
                'nominal_kredit'=>$this->biayaLain,
                'keterangan'=>$this->keterangan
            ]);
            $this->neracaSaldo->updateKredit($this->akun_hutang_biaya_lain_id, $this->biayaLain);
        }
        // store biaya_lain jika ada
        if ($this->ppn > 0 || $this->biayaLain != null){
            $this->jurnalTransaksi->create([
                'active_cash'=>$this->activeCash,
                'akun_id'=>$this->akun_hutang_ppn_id,
                'nominal_debet'=>null,
                'nominal_kredit'=>$this->ppn,
                'keterangan'=>$this->keterangan
            ]);
            $this->neracaSaldo->updateKredit($this->akun_hutang_ppn_id, $this->ppn);
        }
    }

    protected function storeJurnalPersediaanTransaksi()
    {
        $jurnal = $this->jurnalPersediaan->create([
            'active_cash'=>$this->activeCash,
            'kode'=>$this->kode,
            'jenis'=>'keluar', // masuk atau keluar
            'kondisi'=>$this->kondisi, // baik atau rusak
            'gudang_id'=>$this->gudangId,
        ]);
        $jurnalDetail = $jurnal->persediaan_transaksi_detail();
        $subTotalSum = 0;
        foreach ($this->dataDetailOut as $item) {
            $subTotal = $item['harga_persediaan'] * $item['jumlah'];
            $jurnalDetail->create([
                'produk_id'=>$item['produk_id'],
                'harga'=>$item['harga_persediaan'],
                'jumlah'=>$item['jumlah'],
                'sub_total'=>$subTotal,
            ]);
            $subTotalSum += $subTotal;
            // update persediaan
            $this->persediaanRepo->updateStockOut($item, 'stock_keluar', $this->tglNota, $this->gudangId, $this->kondisi);
        }

        // store jurnal transaksi
        $jurnalTransaksi = $jurnal->jurnal_transaksi();

        // store debet
        $jurnalTransaksi->create([
            'active_cash'=>$this->activeCash,
            'akun_id'=>$this->akun_piutang_id,
            'nominal_debet'=>$subTotalSum,
            'nominal_kredit'=>null,
            'keterangan'=>$this->keterangan
        ]);

        // store kredit
        $jurnalTransaksi->create([
            'active_cash'=>$this->activeCash,
            'akun_id'=>$this->akun_hpp_id,
            'nominal_debet'=>null,
            'nominal_kredit'=>$subTotalSum,
            'keterangan'=>$this->keterangan
        ]);
    }

    /**
     * Update Helper
     */
}
