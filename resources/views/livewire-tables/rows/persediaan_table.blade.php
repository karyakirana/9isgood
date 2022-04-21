<x-atoms.table.td align="center" width="10%">
    {{$row->produk->kode_lokal}}
</x-atoms.table.td>
<x-atoms.table.td align="center" width="10%">
    {{$row->jenis}}
</x-atoms.table.td>
<x-atoms.table.td align="center" width="10%">
    {{$row->gudang->nama}}
</x-atoms.table.td>
<x-atoms.table.td width="25%">
    {{$row->produk->nama}}
</x-atoms.table.td>
<x-atoms.table.td align="end" width="15%">
    {{rupiah_format($row->harga)}}
</x-atoms.table.td>
<x-atoms.table.td align="center" width="10%">
    {{$row->jumlah}}
</x-atoms.table.td>
<x-atoms.table.td align="end" width="15%">
    {{rupiah_format($row->harga * $row->jumlah)}}
</x-atoms.table.td>
