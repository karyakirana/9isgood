<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\Keuangan\PenerimaanPenjualan;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function PenerimaanPenjualanPrintOut($penerimaanPenjualanId)
    {
        $data = PenerimaanPenjualan::find($penerimaanPenjualanId);
        //dd($data);
        $pdf = \PDF::loadView('pdf.jurnal-penerimaan-penjualan-receipt', ['penerimaan_penjualan'=>$data]);

        $option = [
            'margin-top' => 3,
            'margin-right' => 3,
            'margin-bottom' => 5,
            'margin-left' => 3,
            'footer-right' => utf8_decode('Hal [page] dari [topage]')
        ];
        $pdf->setPaper('a4');
        $pdf->setOptions($option);
        return $pdf->inline('receipt-penerimaan-penjualan.pdf');
    }

}
