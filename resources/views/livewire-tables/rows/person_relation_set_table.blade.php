<x-atoms.table.td>
    {{$row->kode}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->nama}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->telepon}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->alamat}}
</x-atoms.table.td>
<x-atoms.table.td>
    <x-atoms.button.btn-icon wire:click.prevent="$emit('setPersonRelation', {{$row->id}})"><i class="fa fa-edit"></i></x-atoms.button.btn-icon>
</x-atoms.table.td>
