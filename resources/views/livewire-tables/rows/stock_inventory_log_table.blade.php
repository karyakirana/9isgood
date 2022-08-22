<x-atoms.table.td>
    {{ucwords($row->gudang->nama)}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{ucwords($row->jenis)}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->produk->kode_lokal}}
</x-atoms.table.td>
<x-atoms.table.td>
    <span class="fw-bold">{{$row->produk->nama}}</span> <br>
    <div class="row">
        <div class="col-4">Stock Opname</div>
        <div class="col-8">: {{$row->stock_opname}}</div>
    </div>
    <div class="row">
        <div class="col-4">Stock Masuk</div>
        <div class="col-8">: {{$row->stock_masuk}}</div>
    </div>
    <div class="row">
        <div class="col-4">Stock Keluar</div>
        <div class="col-8">: {{$row->stock_keluar}}</div>
    </div>
    <div class="row">
        <div class="col-4">Stock Akhir</div>
        <div class="col-8">: {{$row->stock_akhir}}</div>
    </div>
    <div class="row">
        <div class="col-4">Stock Lost</div>
        <div class="col-8">: {{$row->stock_lost}}</div>
    </div>
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->stock_saldo}}
</x-atoms.table.td>
