<?php namespace App\Haramain\Service\SistemStock;

use App\Models\Stock\StockKeluar;
use App\Models\Stock\StockMasuk;
use App\Models\Stock\StockMutasi;

trait StockKodeTrait
{
    private function setKondisiMasuk($kondisi): string
    {
        if ($kondisi == 'baik_rusak'|| $kondisi == 'rusak_rusak'){
            return 'rusak';
        }

        return 'baik';
    }

    private function setKondisiKeluar($kondisi): string
    {
        if ($kondisi == 'baik_baik'|| $kondisi == 'baik_rusak'){
            return 'baik';
        }

        return 'rusak';
    }

    private function kodeStockKeluar($kondisi = null, $jenisMutasi = null): string
    {
        if ($jenisMutasi){
            $kondisi = $this->setKondisiKeluar($jenisMutasi);
        }

        // query
        $query = StockKeluar::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('kondisi', $kondisi)
            ->latest('kode');

        $kodeKondisi = ($kondisi == 'baik') ? 'SK' : 'SKR';

        // check last num
        if ($query->doesntExist()){
            return "0001/{$kodeKondisi}/".date('Y');
        }

        $num = (int) $query->first()->last_num_trans + 1;
        return sprintf("%04s", $num)."/{$kodeKondisi}/".date('Y');
    }

    private function kodeStockMutasi($jenisMutasi): string
    {
        $query = StockMutasi::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis_mutasi', $jenisMutasi)
            ->latest('kode');

        if ($jenisMutasi == 'baik_baik'){
            $kodeKondisi = 'MBB';
        } elseif ($jenisMutasi == 'baik_rusak'){
            $kodeKondisi = 'MBR';
        } else {
            $kodeKondisi = 'MRR';
        }

        // check last num
        if ($query->doesntExist()){
            return "0001/{$kodeKondisi}/".date('Y');
        }

        $num = (int) $query->first()->last_num_trans + 1;
        return sprintf("%04s", $num)."/{$kodeKondisi}/".date('Y');
    }
}
