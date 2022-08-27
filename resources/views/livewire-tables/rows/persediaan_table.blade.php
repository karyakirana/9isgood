
<x-atoms.table.td align="center" width="10%">
    {{$row->jenis}}
</x-atoms.table.td>
<x-atoms.table.td align="center" width="15%">
    {{tanggalan_format($row->tgl_input)}}
</x-atoms.table.td>
<x-atoms.table.td align="center" width="10%">
    {{$row->gudang->nama}}
</x-atoms.table.td>
<x-atoms.table.td width="40%">
    <span class="fw-bold">({{$row->produk->kode_lokal}}) {{$row->produk->nama}} </span><br>
    <div class="row">
        <div class="col-4">Stock Opname</div>
        <div class="col-8">: {{rupiah_format($row->stock_opname)}}</div>
        <div class="col-4">Stock Masuk</div>
        <div class="col-8">: {{rupiah_format($row->stock_masuk)}}</div>
        <div class="col-4">Stock Keluar</div>
        <div class="col-8">: {{rupiah_format($row->stock_keluar)}}</div>
        <div class="col-4">Saldo</div>
        <div class="col-8">: {{rupiah_format($row->stock_saldo)}}</div>
    </div>
</x-atoms.table.td>
<x-atoms.table.td align="end" width="15%">
    {{rupiah_format($row->harga)}}
</x-atoms.table.td>
<x-atoms.table.td align="end" width="10%">
    {{rupiah_format($row->stock_saldo)}}
</x-atoms.table.td>
