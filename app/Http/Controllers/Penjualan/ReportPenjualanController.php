<?php

namespace App\Http\Controllers\Penjualan;

use App\Haramain\Repository\Penjualan\PenjualanPureRepo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportPenjualanController extends Controller
{
    public $penjualanRepo;

    public function __construct()
    {
        $this->penjualanRepo = new PenjualanPureRepo();
    }

    public function index()
    {
        //
    }

    public function reportByDate($tglAwal, $tglAkhir)
    {
        $data = $this->penjualanRepo->getByDate($tglAwal,$tglAkhir)->get();
        //dd($data);
        $pdf = \PDF::loadView('pdf.penjualan-report-bydate', [
            'penjualan'=>$data
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

    public function reportByMonth($bulan)
    {
        $data = $this->penjualanRepo->getByMonth($bulan);
        //dd($data);
        $pdf = \PDF::loadView('pdf.penjualan-report-bydate', [
            'penjualan'=>$data
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

    public function reportByPeriodic($active_cash)
    {
        //
    }

    public function reportByProduk($produk_id)
    {
        //
    }
}
