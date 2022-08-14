
<x-atoms.table.td align="center" width="10%">
    {{$row->jenis}}
</x-atoms.table.td>
<x-atoms.table.td align="center" width="10%">
    {{$row->tgl_input}}
</x-atoms.table.td>
<x-atoms.table.td align="center" width="10%">
    {{$row->gudang->nama}}
</x-atoms.table.td>
<x-atoms.table.td width="45%">
    <span class="fw-bold">({{$row->produk->kode_lokal}}) {{$row->produk->nama}} </span><br>
    <div class="row">
        <div class="col-3">Stock Opname</div>
        <div class="col-9">: {{rupiah_format($row->stock_opname)}}</div>
        <div class="col-3">Stock Masuk</div>
        <div class="col-9">: {{rupiah_format($row->stock_masuk)}}</div>
        <div class="col-3">Stock Keluar</div>
        <div class="col-9">: {{rupiah_format($row->stock_keluar)}}</div>
        <div class="col-3">Stock Akhir</div>
        <div class="col-9">: {{rupiah_format($row->stock_akhir)}}</div>
        <div class="col-3">Stock Lost</div>
        <div class="col-9">: {{rupiah_format($row->stock_lost)}}</div>
    </div>
</x-atoms.table.td>
<x-atoms.table.td align="end" width="15%">
    {{rupiah_format($row->harga)}}
</x-atoms.table.td>
<x-atoms.table.td align="end" width="10%">
    {{rupiah_format($row->stock_saldo)}}
</x-atoms.table.td>
