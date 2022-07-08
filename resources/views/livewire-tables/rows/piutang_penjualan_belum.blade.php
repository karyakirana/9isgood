<x-atoms.table.td>
    {{$row->penjualan->kode}}
</x-atoms.table.td>
<x-atoms.table.td align="center">
    {{$row->status_bayar}}
</x-atoms.table.td>
<x-atoms.table.td align="end">
    {{rupiah_format($row->penjualan->total_bayar)}}
</x-atoms.table.td>
<x-atoms.table.td align="end">
    {{rupiah_format($row->kurang_bayar)}}
</x-atoms.table.td>
