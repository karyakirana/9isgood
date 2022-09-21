<?php namespace App\Haramain\SistemKeuangan\SubJurnal;

use App\Models\Keuangan\PenerimaanPenjualan;
use App\Models\Keuangan\PengeluaranPembelian;

class JurnalKasMasuk extends JurnalKasRepository
{
    public function __construct(PenerimaanPenjualan $penerimaanPenjualan)
    {
        parent::__construct($penerimaanPenjualan);
    }
}
