<x-atoms.table.td>
    {{$row->kode}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->customer->nama ?? null}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{tanggalan_format($row->tgl_nota)}}
</x-atoms.table.td>
<x-atoms.table.td>
    @if($row->jenis_bayar == 'Tempo' ||$row->jenis_bayar == 'tempo')
        {{tanggalan_format($row->tgl_tempo)}}
    @endif
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->jenis_bayar}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->status_bayar}}
</x-atoms.table.td>
<x-atoms.table.td align="end">
    {{rupiah_format($row->total_bayar)}}
</x-atoms.table.td>
<x-atoms.table.td align="center">
    <x-atoms.button.btn-icon-link onclick="Livewire.emit('setPenjualan', '{{$row->id}}')">Set</x-atoms.button.btn-icon-link>
</x-atoms.table.td>
