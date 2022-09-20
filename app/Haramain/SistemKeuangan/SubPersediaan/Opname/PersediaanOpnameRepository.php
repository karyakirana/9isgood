<?php namespace App\Haramain\SistemKeuangan\SubPersediaan\Opname;

use App\Haramain\SistemKeuangan\SubPersediaan\PersediaanForOpname;
use App\Models\Keuangan\PersediaanOpname;
use Auth;

class PersediaanOpnameRepository
{
    protected $persediaanOpnameId;
    protected $kode;
    protected $activeCash;
    protected $kondisi;
    protected $gudangId;
    protected $stockOpnameId;
    protected $userId;
    protected $keterangan;

    protected $tglInput;

    protected $dataDetail;

    public function __construct($data)
    {
        $this->persediaanOpnameId = $data['persediaanOpnameId'];
        $this->kode = $this->kode($data['kondisi']);
        $this->activeCash = session('ClosedCash');
        $this->kondisi = $data['kondisi'];
        $this->gudangId = $data['gudangId'];
        $this->stockOpnameId = null;
        $this->userId = Auth::id();
        $this->keterangan = $data['keterangan'];

        $this->dataDetail = $data['dataDetail'];
    }

    public static function build($data)
    {
        return new static($data);
    }

    public static function getKode($kondisi)
    {
        $query = PersediaanOpname::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('kondisi', $kondisi)
            ->latest('kode');

        $kode = ($kondisi == 'baik') ? 'POB' : 'POR';

        // check last num
        if ($query->doesntExist()){
            return "0001/$kode/".date('Y');
        }

        $num = (int)$query->first()->last_num_trans + 1 ;
        return sprintf("%04s", $num)."/$kode/".date('Y');
    }

    protected function kode($kondisi)
    {
        return self::getKode($kondisi);
    }

    protected function getDataById()
    {
        return PersediaanOpname::find($this->persediaanOpnameId);
    }

    public function store()
    {
        $persediaanOpname = $this->updateOrCreate();
        $this->tglInput = $persediaanOpname->created_at;
        $persediaanOpname->persediaan_opname_detail()->createMany($this->storeDetail());
        return $persediaanOpname;
    }

    protected function updateOrCreate()
    {
        $data = $this->setData();
        if ($this->persediaanOpnameId){
            // update
            unset($data['kode'], $data['active_cash']);
            $persediaanOpname = $this->getDataById();
            $persediaanOpname->update($data);
            return $persediaanOpname->refresh();
        }
        // create
        return PersediaanOpname::create($data);
    }

    protected function setData()
    {
        return [
            'kode'=>$this->kode,
            'active_cash'=>$this->activeCash,
            'kondisi'=>$this->kondisi,
            'gudang_id'=>$this->gudangId,
            'stock_opname_id'=>$this->stockOpnameId,
            'user_id'=>$this->userId,
            'keterangan'=>$this->keterangan,
        ];
    }

    protected function storeDetail()
    {
        $dataDetail = [];
        foreach ($this->dataDetail as $detail){
            // set detail
            PersediaanForOpname::build([
                'kondisi'=>$this->kondisi,
                'tglInput'=>$this->tglInput,
                'gudangId'=>$this->gudangId,
                'produkId'=>$detail['produk_id'],
                'harga'=>$detail['harga'],
                'jumlah'=>$detail['jumlah'],
                'type'=>'increment'
            ])->store();
            $dataDetail[] = [
                'produk_id'=>$detail['produk_id'],
                'jumlah'=>$detail['jumlah'],
                'harga'=>$detail['harga'],
                'sub_total'=>$detail['sub_total'],
            ];
        }
        return $dataDetail;
    }

    public static function rollback(PersediaanOpname $persediaanOpname)
    {
        $persediaanOpnameDetail = $persediaanOpname->persediaan_opname_detail;
        foreach ($persediaanOpnameDetail as $item) {
            PersediaanForOpname::build([
                'kondisi'=>$persediaanOpname->kondisi,
                'tglInput'=>$persediaanOpname->created_at,
                'gudangId'=>$persediaanOpname->gudang_id,
                'produkId'=>$item->produk_id,
                'harga'=>$item->harga,
                'jumlah'=>$item->jumlah,
                'type'=>'decrement'
            ])->store();
        }
        $persediaanOpname->persediaan_opname_detail()->delete();
    }
}
