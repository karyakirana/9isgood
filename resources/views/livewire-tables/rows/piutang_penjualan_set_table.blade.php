<x-atoms.table.td>
    @if($row->penjualan_type === 'App\Models\Penjualan\Penjualan')
        Penjualan
    @elseif($row->penjualan_type === 'App\Models\Penjualan\PenjualanRetur')
        Retur Penjualan
    @endif
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->piutangablePenjualan->kode}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->piutangablePenjualan->customer->nama}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{($row->piutangablePenjualan->tgl_nota) ? tanggalan_format($row->piutangablePenjualan->tgl_nota) : ''}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{($row->piutangablePenjualan->tgl_tempo) ? tanggalan_format($row->piutangablePenjualan->tgl_tempo) : ''}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{ucwords($row->status_bayar)}}
</x-atoms.table.td>
<x-atoms.table.td>
    Rp. {{rupiah_format($row->kurang_bayar)}}
</x-atoms.table.td>
<x-atoms.table.td>
    @if($row->penjualan_type === 'App\Models\Penjualan\Penjualan')
        <x-atoms.button.btn-icon color="dark" onclick="Livewire.emit('showPenjualanDetail', {{$row->id}})"><i class="fas fa-indent"></i></x-atoms.button.btn-icon>
        <x-atoms.button.btn-icon color="info" onclick="Livewire.emit('setPenjualan', {{$row->id}})"><i class="fas fa-pen"></i></x-atoms.button.btn-icon>
    @elseif($row->penjualan_type === 'App\Models\Penjualan\PenjualanRetur')
        <x-atoms.button.btn-icon color="dark" onclick="Livewire.emit('showPenjualanReturDetail', {{$row->id}})"><i class="fas fa-indent"></i></x-atoms.button.btn-icon>
        <x-atoms.button.btn-icon color="info" onclick="Livewire.emit('setPenjualanRetur', {{$row->id}})"><i class="fas fa-pen"></i></x-atoms.button.btn-icon>
    @endif
</x-atoms.table.td>
