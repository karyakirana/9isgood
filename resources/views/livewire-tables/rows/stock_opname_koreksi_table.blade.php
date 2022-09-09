<x-atoms.table.td>
    {{$row->kode}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->tgl_input}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->jenis}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->kondisi}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->gudang->nama}}
</x-atoms.table.td>
<x-atoms.table.td>
    <x-atoms.button.btn-icon-link :href="url('/').'/stock/opname/koreksi/form/'.$row->jenis.'/'.$row->id" color="info"><i class="far fa-edit"></i></x-atoms.button.btn-icon-link>
    <x-atoms.button.btn-icon color="danger"><i class="fas fa-trash"></i></x-atoms.button.btn-icon>
</x-atoms.table.td>
