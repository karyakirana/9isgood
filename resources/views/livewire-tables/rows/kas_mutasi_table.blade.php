<x-atoms.table.td>
    {{$row->kode}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->tgl_mutasi}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->users->name}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{rupiah_format($row->total_mutasi)}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->keterangan}}
</x-atoms.table.td>
<x-atoms.table.td>
    <x-atoms.button.btn-icon-link :href="route('kasir.mutasi.form.edit', $row->id)"><i class="fa fa-edit"></i></x-atoms.button.btn-icon-link>
    <x-atoms.button.btn-icon><i class="fa fa-note-sticky"></i></x-atoms.button.btn-icon>
    <x-atoms.button.btn-icon><i class="fa fa-trash"></i></x-atoms.button.btn-icon>
</x-atoms.table.td>
