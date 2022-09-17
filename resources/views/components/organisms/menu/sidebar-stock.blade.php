@php
    $gudang = \App\Models\Master\Gudang::query()
            ->whereNotIn('nama', ['GUDANG BUKU RUSAK'])
            ->get();
    $jenis = collect([['jenis'=>'baik'], ['jenis'=>'rusak']]);
    $stockMasuk = \App\Models\Stock\StockMasuk::query()
            ->where('kondisi')
            ->get();
    $kondisi = collect([['kondisi'=>'baik'], ['kondisi'=>'rusak']]);
@endphp
<!--begin:Menu item-->
<div data-kt-menu-trigger="click"
     class="menu-item {{request()->is('stock/inventory*') ? 'here show' : ''}} menu-accordion">
    <!--begin:Menu link-->
    <span class="menu-link">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Stock Report</span>
                                <span class="menu-arrow"></span>
                            </span>
    <!--end:Menu link-->
    <!--begin:Menu sub-->
    <div class="menu-sub menu-sub-accordion menu-active-bg">
        <!--begin:Menu item-->
        <div class="menu-item">
            <!--begin:Menu link-->
            <a class="menu-link {{request()->is('stock/inventory') ? 'active' : ''}}"
               href="{{route('inventory')}}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                <span class="menu-title">Stock Semua</span>
            </a>
            <!--end:Menu link-->
        </div>
        <!--end:Menu item-->
        @foreach($gudang as $row)
            @foreach($jenis as $item)
                <!--begin:Menu item-->
                <div class="menu-item">
                    <!--begin:Menu link-->
                    <a class="menu-link  {{request()->is('stock/inventory/'.$item['jenis'].'/'.$row->id) ? 'active' : ''}}"
                       href="{{url('/').'/stock/inventory/'.$item['jenis'].'/'.$row->id}}">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                        <span
                            class="menu-title">Stock {{ucwords($row->nama)}} {{ucwords($item['jenis'])}}</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->
            @endforeach
        @endforeach
    </div>
    <!--end:Menu sub-->
</div>
<!--end:Menu item-->
<!--begin:Menu item-->
<div data-kt-menu-trigger="click"
     class="menu-item {{request()->is('stock/masuk*') ? 'here show' : ''}} menu-accordion mb-1">
    <!--begin:Menu link-->
    <span class="menu-link">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Stock Masuk</span>
                                <span class="menu-arrow"></span>
                            </span>
    <!--end:Menu link-->
    <!--begin:Menu sub-->
    <div class="menu-sub menu-sub-accordion menu-active-bg">
        <!--begin:Menu item-->
        <div class="menu-item">
            <!--begin:Menu link-->
            <a class="menu-link {{request()->is('stock/masuk') ? 'active' : ''}}"
               href="{{url('/').'/stock/masuk'}}">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                <span class="menu-title">Stock Masuk</span>
            </a>
            <!--end:Menu link-->
        </div>
        <!--end:Menu item-->
        <!--begin:Menu item-->
        <div class="menu-item">
            <!--begin:Menu link-->
            <a class="menu-link {{request()->is('stock/masuk/form*') ? 'active' : ''}}"
               href="{{url('/').'/stock/masuk/form'}}">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                <span class="menu-title">Stock Masuk Baru</span>
            </a>
            <!--end:Menu link-->
        </div>
        <!--end:Menu item-->
    </div>
    <!--end:Menu sub-->
</div>
<!--end:Menu item-->
<!--begin:Menu item-->
<div data-kt-menu-trigger="click"
     class="menu-item {{request()->is('stock/keluar*') ? 'here show' : ''}} menu-accordion">
    <!--begin:Menu link-->
    <span class="menu-link">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Stock Keluar</span>
                                <span class="menu-arrow"></span>
                            </span>
    <!--end:Menu link-->
    <!--begin:Menu sub-->
    <div class="menu-sub menu-sub-accordion menu-active-bg">
        <!--begin:Menu item-->
        <div class="menu-item">
            <!--begin:Menu link-->
            <a class="menu-link {{request()->is('stock/transaksi/keluar') ? 'active' : ''}}"
               href="{{url('/').'/stock/transaksi/keluar/'}}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                <span class="menu-title">Stock Keluar Index</span>
            </a>
            <!--end:Menu link-->
        </div>
        <!--end:Menu item-->
        @foreach($kondisi as $item)
            <!--begin:Menu item-->
            <div class="menu-item">
                <!--begin:Menu link-->
                <a class="menu-link {{request()->is('stock/transaksi/keluar/'.$item['kondisi']) ? 'active' : ''}}"
                   href="{{url('/').'/stock/transaksi/keluar/'.$item['kondisi']}}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                    <span class="menu-title">Stock Keluar {{ucwords($item['kondisi'])}}</span>
                </a>
                <!--end:Menu link-->
            </div>
            <!--end:Menu item-->
        @endforeach
    </div>
    <!--end:Menu sub-->
</div>
<!--end:Menu item-->
<!--begin:Menu item-->
<div data-kt-menu-trigger="click"
     class="menu-item  {{request()->is('stock/transaksi/opname*') ? 'here show' : ''}} menu-accordion">
    <!--begin:Menu link-->
    <span class="menu-link">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Stock Opname</span>
                                <span class="menu-arrow"></span>
                            </span>
    <!--end:Menu link-->
    <!--begin:Menu sub-->
    <div class="menu-sub menu-sub-accordion">
        <!--begin:Menu item-->
        <div class="menu-item">
            <!--begin:Menu link-->
            <a class="menu-link {{request()->is('stock/transaksi/opname') ? 'active' : ''}}"
               href="{{url('/').'/stock/transaksi/opname'}}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                <span class="menu-title">Stock Opname</span>
            </a>
            <!--end:Menu link-->
        </div>
        <!--end:Menu item-->
        @foreach($jenis as $item)
            <!--begin:Menu item-->
            <div class="menu-item">
                <!--begin:Menu link-->
                <a class="menu-link {{request()->is('stock/transaksi/opname/'.$item['jenis']) ? 'active' : ''}}"
                   href="{{url('/').'/stock/transaksi/opname/'.$item['jenis']}}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                    <span class="menu-title">Stock Opname {{ucwords($item['jenis'])}}</span>
                </a>
                <!--end:Menu link-->
            </div>
            <!--end:Menu item-->
            <!--begin:Menu item-->
            <div class="menu-item">
                <!--begin:Menu link-->
                <a class="menu-link {{request()->is('stock/transaksi/opname/trans/'.$item['jenis']) ? 'active' : ''}}"
                   href="{{url('/').'/stock/transaksi/opname/trans/'.$item['jenis']}}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                    <span
                        class="menu-title">Stock Opname {{ucwords($item['jenis'])}} Baru</span>
                </a>
                <!--end:Menu link-->
            </div>
            <!--end:Menu item-->
        @endforeach
        <!--begin:Menu item-->
        <div class="menu-item">
            <!--begin:Menu link-->
            <a class="menu-link {{request()->is('stock/opname/koreksi') ? 'active' : ''}}"
               href="{{url('/').'/stock/opname/koreksi'}}">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                <span class="menu-title">Stock Opname Koreksi</span>
            </a>
            <!--end:Menu link-->
        </div>
        <!--end:Menu item-->
        <!--begin:Menu item-->
        <div class="menu-item">
            <!--begin:Menu link-->
            <a class="menu-link {{request()->is('stock/opname/koreksi/form/tambah*') ? 'active' : ''}}"
               href="{{url('/').'/stock/opname/koreksi/form/tambah'}}">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                <span class="menu-title">Stock Opname Tambah Form</span>
            </a>
            <!--end:Menu link-->
        </div>
        <!--end:Menu item-->
        <!--begin:Menu item-->
        <div class="menu-item">
            <!--begin:Menu link-->
            <a class="menu-link {{request()->is('stock/opname/koreksi/form/kurang*') ? 'active' : ''}}"
               href="{{url('/').'/stock/opname/koreksi/form/kurang'}}">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                <span class="menu-title">Stock Opname Kurang Form</span>
            </a>
            <!--end:Menu link-->
        </div>
        <!--end:Menu item-->
    </div>
    <!--end:Menu sub-->
</div>
<!--end:Menu item-->
<!--begin:Menu item-->
<div data-kt-menu-trigger="click"
     class="menu-item  {{request()->is('stock/mutasi*') ? 'here show' : ''}} menu-accordion">
    <!--begin:Menu link-->
    <span class="menu-link">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Stock Mutasi</span>
                                <span class="menu-arrow"></span>
                            </span>
    <!--end:Menu link-->
    <div class="menu-sub menu-sub-accordion">
        <!--begin:Menu item-->
        <div class="menu-item">
            <!--begin:Menu link-->
            <a class="menu-link {{request()->is('stock/mutasi') ? 'active' : ''}}"
               href="{{route('stock.mutasi')}}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                <span class="menu-title">Mutasi Stock Index</span>
            </a>
            <!--end:Menu link-->
        </div>
        <!--end:Menu item-->
        <!--begin:Menu item-->
        <div class="menu-item">
            <!--begin:Menu link-->
            <a class="menu-link {{request()->is('stock/mutasi/report/baik_baik') ? 'active' : ''}}"
               href="{{route('stock.mutasi.kondisi', 'baik_baik')}}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                <span class="menu-title">Mutasi Stock Baik</span>
            </a>
            <!--end:Menu link-->
        </div>
        <!--end:Menu item-->
        <!--begin:Menu item-->
        <div class="menu-item">
            <!--begin:Menu link-->
            <a class="menu-link {{request()->is('stock/mutasi/report/rusak_rusak') ? 'active' : ''}}"
               href="{{route('stock.mutasi.kondisi', 'baik_baik')}}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                <span class="menu-title">Mutasi Stock Rusak</span>
            </a>
            <!--end:Menu link-->
        </div>
        <!--end:Menu item-->
        <!--begin:Menu item-->
        <div class="menu-item">
            <!--begin:Menu link-->
            <a class="menu-link {{request()->is('stock/mutasi/form*') ? 'active' : ''}}"
               href="{{route('stock.mutasi.form')}}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                <span class="menu-title">Mutasi Stock Form</span>
            </a>
            <!--end:Menu link-->
        </div>
        <!--end:Menu item-->
    </div>
</div>
<!--end:Menu item-->
