<x-atoms.table.td width="10%">
    {{$row->kode}}
</x-atoms.table.td>
<x-atoms.table.td width="15%">
    {{$row->customer->nama}}
</x-atoms.table.td>
<x-atoms.table.td width="12%">
    {{tanggalan_format($row->tgl_nota)}}
</x-atoms.table.td>
<x-atoms.table.td width="12%">
    @if($row->tgl_tempo)
        {{tanggalan_format($row->tgl_tempo)}}
    @endif
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->jenis_bayar}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->status_bayar}}
</x-atoms.table.td>
<x-atoms.table.td width="10%" align="end">
    {{rupiah_format($row->total_bayar)}}
</x-atoms.table.td>
<x-atoms.table.td align="center">
    <x-atoms.button.btn-icon-link color="success"><i class="fas fa-file-powerpoint"></i></x-atoms.button.btn-icon-link>
</x-atoms.table.td>
