<?php namespace App\Haramain\SistemKeuangan\SubJurnal;

use App\Haramain\SistemKeuangan\SubNeraca\NeracaSaldoRepository;
use App\Haramain\SistemKeuangan\SubOther\KonfigurasiJurnalRepository;
use Str;

trait JurnalTransaksiServiceTrait
{
    protected function rollbackJurnalAndSaldo($class)
    {
        //dd($class);
        $getJurnal = JurnalTransaksiRepo::build($class)->getData();
        //dd($getJurnal);
        foreach ($getJurnal as $jurnal) {
            //dd($jurnal);
            if ((int)$jurnal->nominal_debet > 0){
                NeracaSaldoRepository::debetRollback($jurnal->akun_id, $jurnal->nominal_debet);
            }
            if ((int)$jurnal->nominal_kredit > 0){
                NeracaSaldoRepository::kreditRollback($jurnal->akun_id, $jurnal->nominal_kredit);
            }
        }
        return JurnalTransaksiRepo::build($class)->rollback();
    }

    protected $akunPiutangPenjualan;
    protected $akunPenjualan;
    protected $akunPPNPenjualan;
    protected $akunBiayaLainPenjualan;
    protected $akunHPP;
    protected $akunPersediaanKalimas;
    protected $akunPersediaanPerak;

    protected function akunForPenjualanService()
    {
        $this->akunPiutangPenjualan = KonfigurasiJurnalRepository::build('piutang_usaha')->getAkun();
        $this->akunPenjualan = KonfigurasiJurnalRepository::build('penjualan')->getAkun();
        $this->akunPPNPenjualan = KonfigurasiJurnalRepository::build('ppn_penjualan')->getAkun();
        $this->akunBiayaLainPenjualan = KonfigurasiJurnalRepository::build('biaya_penjualan')->getAkun();
        $this->akunHPP = KonfigurasiJurnalRepository::build('hpp_internal')->getAkun();
        $this->akunPersediaanKalimas = KonfigurasiJurnalRepository::build('persediaan_baik_kalimas')->getAkun();
        $this->akunPersediaanPerak = KonfigurasiJurnalRepository::build('persediaan_baik_perak')->getAkun();
    }

    protected function jurnalPenjualanService($penjualan)
    {
        $jurnalTransaksiRepo = JurnalTransaksiRepo::build($penjualan);
        $totalPenjualan = $penjualan->total_bayar - (int) $penjualan->ppn - (int) $penjualan->biaya_lain;
        // piutang penjualan debet
        $jurnalTransaksiRepo->debet($this->akunPiutangPenjualan, $penjualan->total_bayar);
        NeracaSaldoRepository::debet($this->akunPiutangPenjualan, $penjualan->total_bayar);
        // penjualan kredit
        $jurnalTransaksiRepo->kredit($this->akunPenjualan, $totalPenjualan);
        NeracaSaldoRepository::kredit($this->akunPenjualan, $totalPenjualan);
        // ppn kredit
        if((int) $penjualan->ppn > 0){
            $jurnalTransaksiRepo->kredit($this->akunPPNPenjualan, $penjualan->ppn);
            NeracaSaldoRepository::kredit($this->akunPPNPenjualan, $penjualan->ppn);
        }
        // biaya lain kredit
        if((int) $penjualan->biaya_lain > 0){
            $jurnalTransaksiRepo->kredit($this->akunBiayaLainPenjualan, $penjualan->biaya_lain);
            NeracaSaldoRepository::kredit($this->akunBiayaLainPenjualan, $penjualan->biaya_lain);
        }
        // hpp debet berdasarkan persediaan keluar
        // $jurnalTransaksiRepo->debet($this->akunHPP, $persediaanTransaksi->kredit);
        // NeracaSaldoRepository::debet($this->akunHPP, $persediaanTransaksi->kredit);
        // persediaan by gudang kredit
        // $akunGudang = ($penjualan->gudang_id == '1') ? $this->akunPersediaanKalimas : $this->akunPersediaanPerak;
        // $jurnalTransaksiRepo->kredit($akunGudang, $persediaanTransaksi->kredit);
        // NeracaSaldoRepository::kredit($akunGudang, $persediaanTransaksi->kredit);
    }

    protected $akunPenjualanretur;
    protected $akunPersediaanRusakKalimas;
    protected $akunPersediaanRusakPerak;

    protected function akunPenjualanReturService()
    {
        $this->akunPiutangPenjualan = KonfigurasiJurnalRepository::build('piutang_usaha')->getAkun();
        $this->akunPenjualanretur = KonfigurasiJurnalRepository::build('penjualan_retur')->getAkun();
        $this->akunPPNPenjualan = KonfigurasiJurnalRepository::build('ppn_penjualan')->getAkun();
        $this->akunBiayaLainPenjualan = KonfigurasiJurnalRepository::build('biaya_penjualan')->getAkun();
        $this->akunHPP = KonfigurasiJurnalRepository::build('hpp_internal')->getAkun();
        $this->akunPersediaanKalimas = KonfigurasiJurnalRepository::build('persediaan_baik_kalimas')->getAkun();
        $this->akunPersediaanPerak = KonfigurasiJurnalRepository::build('persediaan_baik_perak')->getAkun();
        $this->akunPersediaanRusakKalimas = KonfigurasiJurnalRepository::build('persediaan_rusak_kalimas')->getAkun();
        $this->akunPersediaanRusakPerak = KonfigurasiJurnalRepository::build('persediaan_rusak_perak')->getAkun();
    }

    protected function jurnalPenjualanReturService($penjualanRetur)
    {
        $jurnalTransaksiRepo = JurnalTransaksiRepo::build($penjualanRetur);
        $totalPenjualan = $penjualanRetur->total_bayar - (int) $penjualanRetur->ppn - (int) $penjualanRetur->biaya_lain;
        // penjualan retur debet
        $jurnalTransaksiRepo->debet($this->akunPenjualanretur, $totalPenjualan);
        NeracaSaldoRepository::debet($this->akunPenjualanretur, $totalPenjualan);
        // ppn debet
        if ((int)$penjualanRetur->ppn > 0){
            $jurnalTransaksiRepo->debet($this->akunPPNPenjualan, $penjualanRetur->ppn);
            NeracaSaldoRepository::debet($this->akunPPNPenjualan, $penjualanRetur->ppn);
        }
        // biaya lain debet
        if ((int)$penjualanRetur->biaya_lain > 0){
            $jurnalTransaksiRepo->debet($this->akunBiayaLainPenjualan, $penjualanRetur->biaya_lain);
            NeracaSaldoRepository::debet($this->akunBiayaLainPenjualan, $penjualanRetur->biaya_lain);
        }
        // piutang penjualan kredit
        $jurnalTransaksiRepo->kredit($this->akunPiutangPenjualan, $penjualanRetur->total_bayar);
        NeracaSaldoRepository::kredit($this->akunBiayaLainPenjualan, $penjualanRetur->total_bayar);
        // persediaan debet
        // $jurnalTransaksiRepo->debet($this->akunPiutangPenjualan, $persediaanTransaksi->debet);
        // NeracaSaldoRepository::debet($this->akunBiayaLainPenjualan, $persediaanTransaksi->debet);
        // hpp kredit
        // $jurnalTransaksiRepo->kredit($this->akunPiutangPenjualan, $persediaanTransaksi->debet);
        // NeracaSaldoRepository::kredit($this->akunBiayaLainPenjualan, $persediaanTransaksi->debet);
    }

    protected $akunHutangPembelian;
    protected $akunPPNPembelian;
    protected $akunBiayaLainPembelian;

    protected function akunPembelianService()
    {
        $this->akunHutangPembelian = KonfigurasiJurnalRepository::build('hutang_dagang')->getAkun();
        $this->akunPersediaanKalimas = KonfigurasiJurnalRepository::build('persediaan_baik_kalimas')->getAkun();
        $this->akunPersediaanPerak = KonfigurasiJurnalRepository::build('persediaan_baik_perak')->getAkun();
        $this->akunPPNPembelian = KonfigurasiJurnalRepository::build('ppn_pembelian')->getAkun();
        $this->akunBiayaLainPembelian = KonfigurasiJurnalRepository::build('biaya_pembelian')->getAkun();
    }

    protected function jurnalPembelianService($pembelian)
    {
        // dd($pembelian);
        $jurnalTransaksi = JurnalTransaksiRepo::build($pembelian);
        $akunGudang = ($pembelian->gudang_id == '1') ? $this->akunPersediaanKalimas : $this->akunPersediaanPerak;
        $persediaan = (int)$pembelian->total_bayar - (int)$pembelian->ppn - (int)$pembelian->biaya_lain;
        // persediaan debet
        //dd($persediaan);
        $jurnalTransaksi->debet($akunGudang, $persediaan);
        NeracaSaldoRepository::debet($akunGudang, $persediaan);
        // biaya lain debet
        if ((int)$pembelian->biaya_lain){
            $jurnalTransaksi->debet($this->akunBiayaLainPembelian, $pembelian->biaya_lain);
            NeracaSaldoRepository::debet($this->akunBiayaLainPembelian, $pembelian->biaya_lain);
        }
        // ppn debet
        if ((int)$pembelian->ppn){
            $jurnalTransaksi->debet($this->akunPPNPembelian, $pembelian->ppn);
            NeracaSaldoRepository::debet($this->akunPPNPembelian, $pembelian->ppn);
        }
        // hutang pembelian debet
        $jurnalTransaksi->kredit($this->akunHutangPembelian, $pembelian->total_bayar);
        NeracaSaldoRepository::kredit($this->akunHutangPembelian, $pembelian->total_bayar);
    }

    protected $akunPersediaanKalimasRusak;
    protected $akunPersediaanPerakRusak;

    protected function akunStockMutasiService()
    {
        $this->akunPersediaanKalimas = KonfigurasiJurnalRepository::build('persediaan_baik_kalimas')->getAkun();
        $this->akunPersediaanKalimasRusak = KonfigurasiJurnalRepository::build('persediaan_rusak_kalimas')->getAkun();
        $this->akunPersediaanPerak = KonfigurasiJurnalRepository::build('persediaan_baik_perak')->getAkun();
        $this->akunPersediaanPerakRusak = KonfigurasiJurnalRepository::build('persediaan_rusak_perak')->getAkun();
    }

    protected function jurnalStockMutasiService($persediaanMutasi)
    {
        // initiate jurnal
        $gudangAsalId = ucfirst($persediaanMutasi->gudangAsal->nama);
        $gudangTujuanId = ucfirst($persediaanMutasi->gudangTujuan->nama);
        $jenisMutasi = $persediaanMutasi->jenis_mutasi;
        $kondisiKeluar = Str::before($jenisMutasi, '_');
        $kondisiKeluar = ($kondisiKeluar == 'rusak') ? ucfirst($kondisiKeluar) : null;
        $kondisiMasuk = Str::after($jenisMutasi, '_');
        $kondisiMasuk = ($kondisiMasuk == 'rusak') ? ucfirst($kondisiMasuk) : null;

        // persediaan tujuan debet
        $jurnalTransaksi = JurnalTransaksiRepo::build($persediaanMutasi);
        $jurnalTransaksi->debet($this->{'akunPersediaan'.$gudangTujuanId.$kondisiMasuk}, $persediaanMutasi->total_harga);
        NeracaSaldoRepository::debet($this->{'akunPersediaan'.$gudangTujuanId.$kondisiMasuk}, $persediaanMutasi->total_harga);
        $jurnalTransaksi->kredit($this->{'akunPersediaan'.$gudangAsalId.$kondisiKeluar}, $persediaanMutasi->total_harga);
        NeracaSaldoRepository::kredit($this->{'akunPersediaan'.$gudangAsalId.$kondisiKeluar}, $persediaanMutasi->total_harga);
    }

    protected $akunModalAwal;

    protected function akunStockOpnameKoreksiService()
    {
        $this->akunModalAwal = KonfigurasiJurnalRepository::build('prive_modal_awal')->getAkun();
        $this->akunPersediaanKalimas = KonfigurasiJurnalRepository::build('persediaan_baik_kalimas')->getAkun();
        $this->akunPersediaanKalimasRusak = KonfigurasiJurnalRepository::build('persediaan_rusak_kalimas')->getAkun();
        $this->akunPersediaanPerak = KonfigurasiJurnalRepository::build('persediaan_baik_perak')->getAkun();
        $this->akunPersediaanPerakRusak = KonfigurasiJurnalRepository::build('persediaan_rusak_perak')->getAkun();
    }
}
