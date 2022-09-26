<x-atoms.table.td>
    {{$loop->iteration}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->supplier->nama}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{strtoupper(class_basename($row->pembelian_type))}} {{$row->hutangablePembelian->jenis}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{ucwords($row->status_bayar)}}
</x-atoms.table.td>
<x-atoms.table.td align="end">
    {{rupiah_format($row->total_bayar)}}
</x-atoms.table.td>
<x-atoms.table.td align="end">
    {{rupiah_format($row->kurang_bayar)}}
</x-atoms.table.td>
<x-atoms.table.td align="center">
    @php
        $pembelian = (class_basename($row->pembelian_type) === 'Pembelian') ? 'showPembelianDetail' : 'showPembelianReturDetail';
    @endphp
    <x-atoms.button.btn-icon color="dark" onclick="Livewire.emit('{{$pembelian}}', {{$row->pembelian_id}})"><i class="fas fa-indent"></i></x-atoms.button.btn-icon>
    <x-atoms.button.btn-icon color="info" onclick="Livewire.emit('setHutangPembelian', {{$row->id}})"><i class="fas fa-pen"></i></x-atoms.button.btn-icon>
</x-atoms.table.td>
