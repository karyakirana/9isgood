<x-atoms.table.td align="center">
    {{$row->kode}}
</x-atoms.table.td>
<x-atoms.table.td align="center">
    @if($row->stockable_masuk_type == 'App\Models\Penjualan\PenjualanRetur')
        Retur Penjualan
    @elseif($row->stockable_masuk_type == 'App\Models\Stock\StockMutasi')
        Mutasi Stock
    @endif
</x-atoms.table.td>
<x-atoms.table.td align="center">
    {{ucwords($row->gudang->nama)}}
</x-atoms.table.td>
<x-atoms.table.td align="center">
    {{$row->nomor_po}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->supplier->nama ?? ''}}
</x-atoms.table.td>
<x-atoms.table.td align="center">
    {{ucwords($row->users->name)}}
</x-atoms.table.td>
<x-atoms.table.td align="center">
    {{tanggalan_format($row->tgl_masuk)}}
</x-atoms.table.td>
<x-atoms.table.td align="center" width="12%">
    @if($row->stockable_masuk_type == 'App\Models\Penjualan\PenjualanRetur')

    @elseif($row->stockable_masuk_type == 'App\Models\Stock\StockMutasi')

    @else
        <x-atoms.button.btn-icon-link :href="url('/').'/stock/transaksi/keluar/trans/'.$row->id"><i class="far fa-edit fs-4"></i></x-atoms.button.btn-icon-link>
        <x-atoms.button.btn-icon color="danger"><i class="bi bi-trash-fill fs-4"></i></x-atoms.button.btn-icon>
    @endif
        <x-atoms.button.btn-icon><i class="fas fa-indent fs-4"></i></x-atoms.button.btn-icon>
        <x-atoms.button.btn-icon-link color="info"><i class="fas fa-print fs-4"></i></x-atoms.button.btn-icon-link>
</x-atoms.table.td>
