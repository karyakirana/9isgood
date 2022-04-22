<?php
namespace App\Http\Controllers\Pdf;
use App\Http\Controllers\Controller;
use App\Models\Penjualan\Penjualan;
use Illuminate\Http\Request;

class ReportPdfController extends Controller
{
    public function penjualanPdf(Penjualan $penjualan)
    {
        return view('pdf.penjualan-invoice', [
            'penjualan'=>$penjualan
        ]);
    }

}
