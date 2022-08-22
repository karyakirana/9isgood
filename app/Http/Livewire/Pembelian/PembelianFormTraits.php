<?php namespace App\Http\Livewire\Pembelian;

use App\Models\Keuangan\HargaHppALL;
use App\Models\KonfigurasiJurnal;
use App\Models\Master\Gudang;
use App\Models\Master\Produk;
use App\Models\Master\Supplier;
use App\Models\Purchase\Pembelian;

trait PembelianFormTraits
{
    // var master
    public $pembelian_id;
    public $nomor_nota, $nomor_surat_jalan;
    public $supplier_id, $supplier_nama, $gudang_id, $gudang_data = [];
    public $tgl_nota, $tgl_tempo, $jenis_bayar;
    public $total_barang;
    public $total_bayar, $biaya_lain, $ppn, $keterangan;

    // var for manipulate
    public $update = false;
    public $mode = 'create';

    // var detail
    public $data_detail = [];
    public $index_detail;
    public $produk_id, $produk_nama, $produk_harga, $produk_kode_lokal;
    public $diskon, $hpp, $harga_setelah_hpp, $jumlah, $sub_total;

    // var jurnal transaksi
    public $biaya_pembelian, $ppn_pembelian, $hutang_dagang, $hutang_dagang_internal, $persediaan_kalimas, $persediaan_perak;

    public function mount($pembelianId = null){
        $this->gudang_data = Gudang::all();
        $this->tgl_nota = tanggalan_format(now('ASIA/JAKARTA'));
        $this->tgl_tempo = tanggalan_format(now('ASIA/JAKARTA')->addMonth(2));

        $this->setJurnalTransaksi();
        $this->setHpp();

        // for edit
        if ($pembelianId){
            // get data
            $pembelian = Pembelian::query()->find($pembelianId);
            $this->pembelian_id = $pembelian->id;
            $this->gudang_id = $pembelian->gudang_id;
            $this->supplier_id = $pembelian->supplier_id;
            $this->supplier_nama = $pembelian->supplier->nama;
            $this->tgl_nota = tanggalan_format($pembelian->tgl_nota);
            $this->nomor_surat_jalan = $pembelian->nomor_surat_jalan;
            $this->nomor_nota = $pembelian->nomor_nota;
            $this->keterangan = $pembelian->keterangan;

            // data detail
            foreach ($pembelian->pembelianDetail as $item) {
                $this->data_detail[] = [
                    'produk_id'=>$item->produk_id,
                    'produk_kode_lokal'=>$item->produk->kode_lokal,
                    'produk_nama'=>$item->produk->nama,
                    'produk_harga'=>$item->produk->harga,
                    'diskon'=>null,
                    'harga'=>$item->harga,
                    'jumlah'=>$item->jumlah,
                    'sub_total'=>$item->sub_total
                ];
            }
            $this->mode = 'update';
        }
    }

    public function setHpp()
    {
        $this->hpp = HargaHppALL::query()->latest()->first()->persen;
    }

    public function setJurnalTransaksi()
    {
        $this->biaya_pembelian = KonfigurasiJurnal::query()->find('biaya_pembelian')->akun_id ?? '';
        $this->ppn_pembelian = KonfigurasiJurnal::query()->find('ppn_pembelian')->akun_id ?? '';
        $this->hutang_dagang = KonfigurasiJurnal::query()->find('hutang_dagang')->akun_id ?? '';
        $this->hutang_dagang_internal = KonfigurasiJurnal::query()->find('hutang_dagang_internal')->akun_id ?? '';
        $this->persediaan_kalimas = KonfigurasiJurnal::query()->find('persediaan_baik_kalimas')->akun_id ?? '';
        $this->persediaan_perak = KonfigurasiJurnal::query()->find('persediaan_baik_perak')->akun_id ?? '';
    }

    public function setSupplier(Supplier $supplier)
    {
        $this->supplier_id = $supplier->id;
        $this->supplier_nama = $supplier->nama;
    }

    /**
     * @return array
     */
    protected function getTotalBarang(): array
    {
        $this->totalBarang = array_sum(array_column($this->data_detail, 'jumlah'));
        $this->totalBayar = array_sum(array_column($this->data_detail, 'sub_total'));
        $data = $this->validate([
            'pembelian_id' => 'nullable',
            'supplier_id' => 'required',
            'gudang_id' => 'required',
            'tgl_nota' => 'required',
            'jenis' => 'required',
            'kondisi' => 'required',
            'nomor_surat_jalan' => 'required',
            'nomor_nota' => 'required',
            'keterangan' => 'nullable',
            'total_barang' => 'nullable',
            'total_bayar' => 'nullable',
            'data_detail' => 'required',
            'hutang_dagang_internal' => 'nullable',
            'persediaan_kalimas' => 'nullable',
            'persediaan_perak' => 'nullable',
        ]);
        \DB::beginTransaction();
        return $data;
    }
}
