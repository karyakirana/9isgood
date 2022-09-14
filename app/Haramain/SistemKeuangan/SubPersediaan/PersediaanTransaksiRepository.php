<?php namespace App\Haramain\SistemKeuangan\SubPersediaan;

use App\Models\Keuangan\PersediaanTransaksi;
use App\Models\Keuangan\PersediaanTransaksiDetail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PersediaanTransaksiRepository
{
    protected $persediaanRepository;

    protected $activeCash;
    protected $kode;
    protected $jenis;
    protected $tglInput;
    protected $kondisi;
    protected $gudangId;
    protected $persediaanableType;
    protected $persediaanbleId;
    protected $debet;
    protected $kredit;

    protected $dataDetail;

    protected $persediaan_id;
    protected $produk_id;
    protected $harga;
    protected $jumlah;
    protected $sub_total;

    public function __construct()
    {
        $this->persediaanRepository = new PersediaanRepository();
        $this->activeCash = session('ClosedCash');
        $this->kode = $this->kode();
    }

    protected function kode(): string
    {
        $query = PersediaanTransaksi::query()
            ->where('active_cash', session('ClosedCash'))
            ->latest();

        if ($query->doesntExist()){
            return '0001/PD/'.date('Y');
        }

        $num = (int)$query->first()->last_num_trans + 1 ;
        return sprintf("%04s", $num)."/PD/".date('Y');
    }

    public static function build($penjualanRetur)
    {
        return new static($penjualanRetur);
    }

    public function store()
    {
        $detail = $this->detail();
        $persediaanTransaksi = $this->create();
        $persediaanTransaksi->persediaan_transaksi_detail()->createMany($detail);
        return $persediaanTransaksi;
    }

    public function update()
    {
        $detail = $this->detail();
        $persediaanTransaksi =  $this->updateData();
        $persediaanTransaksi->persediaan_transaksi_detail()->createMany($detail);
        return $persediaanTransaksi;
    }

    public function getByPersediaanable()
    {
        return PersediaanTransaksi::query()
            ->where('persediaan_type', $this->persediaanableType)
            ->where('persediaan_id', $this->persediaanbleId)
            ->firstOrFail();
    }

    protected function create()
    {
        return PersediaanTransaksi::query()
            ->create([
                'active_cash'=>$this->activeCash,
                'kode'=>$this->kode,
                'jenis'=>$this->jenis, // masuk atau keluar
                'tgl_input'=>$this->tglInput,
                'kondisi'=>$this->kondisi, // baik atau rusak
                'gudang_id'=>$this->gudangId,
                'persediaan_type'=>$this->persediaanableType,
                'persediaan_id'=>$this->persediaanbleId,
                'debet'=>$this->debet,
                'kredit'=>$this->kredit,
            ]);
    }

    protected function updateData()
    {
        $this->getByPersediaanable()->update([
            'jenis'=>$this->jenis, // masuk atau keluar
            'tgl_input'=>$this->tglInput,
            'kondisi'=>$this->kondisi, // baik atau rusak
            'gudang_id'=>$this->gudangId,
            'persediaan_type'=>$this->persediaanableType,
            'persediaan_id'=>$this->persediaanbleId,
            'debet'=>$this->debet,
            'kredit'=>$this->kredit,
        ]);
        return $this->getByPersediaanable();
    }

    protected function setDataDetailFromArray($item): void
    {
        $this->persediaan_id = $item['persediaan_id'] ?: null;
        $this->produk_id = $item['produk_id'];
        $this->harga = $item['harga'];
        $this->jumlah = $item['jumlah'];
        $this->sub_total = $item['harga'] * $item['jumlah'];
    }

    protected function setDataDetailFromObject($item): void
    {
        $this->persediaan_id = $item->persediaan_id ?: null;
        $this->produk_id = $item->produk_id;
        $this->harga = $item->harga;
        $this->jumlah = $item->jumlah;
        $this->sub_total = $item->harga * $item->jumlah;
    }

    protected function storeDetail(): array
    {
        $detail = [];
        foreach ($this->dataDetail as $item) {
            is_array($item) ? $this->setDataDetailFromArray($item) : $this->setDataDetailFromObject($item);
            $detail[] = [
                'persediaan_id'=>$this->persediaan_id,
                'produk_id'=>$this->produk_id,
                'harga'=>$this->harga,
                'jumlah'=>$this->jumlah,
                'sub_total'=>$this->sub_total,
            ];
        }
        return $detail;
    }

    public function transaksiDetailDelete()
    {
        return $this->getByPersediaanable()->persediaan_transaksi_detail()->delete();
    }
}
