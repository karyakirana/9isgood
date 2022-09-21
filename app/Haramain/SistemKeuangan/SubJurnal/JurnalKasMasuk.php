<?php namespace App\Haramain\SistemKeuangan\SubJurnal;

use App\Models\Keuangan\PenerimaanPenjualan;

class JurnalKasMasuk extends JurnalKasRepository
{
    public function __construct(PenerimaanPenjualan $penerimaanPenjualan)
    {
        parent::__construct();
        $this->type = 'masuk';
        $this->kode = $this->kode('masuk');
        $this->cashableType = $penerimaanPenjualan::class;
        $this->cashableId = $penerimaanPenjualan->id;
        $this->akunId = $penerimaanPenjualan->akun_kas_id;
        $this->nominalDebet = $penerimaanPenjualan->nominal_kas;
    }
}
