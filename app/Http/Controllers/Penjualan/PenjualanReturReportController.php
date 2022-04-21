<?php

namespace App\Http\Controllers\Penjualan;

use App\Haramain\Repository\Penjualan\PenjualanReturRepo;
use App\Http\Controllers\Controller;
use App\Models\Penjualan\PenjualanRetur;
use Illuminate\Http\Request;

class PenjualanReturReportController extends Controller
{
    public function reportRetur($tglAwal,$tglAkhir)
    {
        $query = PenjualanRetur::query()
            ->where('active_cash', session('ClosedCash'))
            ->oldest('kode');
        if ($tglAwal && $tglAkhir){
            $query=$query->whereBetween('tgl_nota', [tanggalan_database_format($tglAwal, 'd-M-Y'), tanggalan_database_format($tglAkhir, 'd-M-Y')]);
        };
        $pdf = \PDF::loadView('pdf.penjualan-retur-report-bydate', [
            'penjualan'=>$query->get()
        ]);
        $options = [
            'margin-top'    => 3,
            'margin-right'  => 3,
            'margin-bottom' => 3,
            'margin-left'   => 3,
//            'page-width' => 216,
//            'page-height' => 140,
        ];
        $pdf->setPaper('letter');
        $pdf->setOptions($options);
        return $pdf->inline('invoice.pdf');
    }

}
