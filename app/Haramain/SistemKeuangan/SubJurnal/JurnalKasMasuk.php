<?php namespace App\Haramain\SistemKeuangan\SubJurnal;

use App\Models\Keuangan\PenerimaanPenjualan;

class JurnalKasMasuk extends JurnalKasRepository
{
    public function __construct(PenerimaanPenjualan $penerimaanPenjualan)
    {
        parent::__construct($penerimaanPenjualan);
        $this->kode = $this->kode('masuk');
        $this->type = 'masuk';
    }
}
