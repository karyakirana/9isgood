<x-atoms.table.td>
    {{$row->customer->nama}}
</x-atoms.table.td>
<x-atoms.table.td>
    @if($row->penjualan_type === 'App\Models\Penjualan\Penjualan')
        Penjualan
    @elseif($row->penjualan_type === 'App\Models\Penjualan\PenjualanRetur')
        Retur Penjualan
    @endif
</x-atoms.table.td>
<x-atoms.table.td align="center">
    {{$row->piutangablePenjualan->kode}}
</x-atoms.table.td>
<x-atoms.table.td align="center">
    {{$row->status_bayar}}
</x-atoms.table.td>
<x-atoms.table.td align="end">
    {{($row->kurang_bayar) ? rupiah_format($row->kurang_bayar) : ''}}
</x-atoms.table.td>
