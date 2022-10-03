<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\Keuangan\PengeluaranPembelian;
use Illuminate\Http\Request;

class ReportPengeluaranController extends Controller
{
    public function PengeluaranPembelianPrintOut($pengeluaranPembelianId)
    {
        $data = PengeluaranPembelian::find($pengeluaranPembelianId);
        //dd($data);
        $pdf = \PDF::loadView('pdf.jurnal-pengeluaran-pembelian-receipt', ['pengeluaran_pembelian'=>$data]);

        $option = [
            'margin-top' => 3,
            'margin-right' => 3,
            'margin-bottom' => 5,
            'margin-left' => 3,
            'footer-right' => utf8_decode('Hal [page] dari [topage]')
        ];
        $pdf->setPaper('a4');
        $pdf->setOptions($option);
        return $pdf->inline('receipt-pengeluaran-pembelian.pdf');
    }

}
