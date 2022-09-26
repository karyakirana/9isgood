<x-atoms.table.td width="5%" align="center">
    {{$loop->iteration}}
</x-atoms.table.td>
<x-atoms.table.td width="15%" align="center">
    {{$row->jenisSupplier->jenis}}
</x-atoms.table.td>
<x-atoms.table.td width="20%">
    {{ucfirst($row->nama)}}
</x-atoms.table.td>
<x-atoms.table.td width="20%">
    {{$row->alamat}}
</x-atoms.table.td>
<x-atoms.table.td width="15%">
    {{$row->telepon}}
</x-atoms.table.td>
<x-atoms.table.td width="15%">
    {{$row->keterangan}} <br>
    {{$row->npwp}}
    {{$row->email}}
</x-atoms.table.td>
<x-atoms.table.td width="15%" align="center">
    <x-atoms.button.btn-icon onclick="Livewire.emit('setSupplier', {{$row->id}})">
        set
    </x-atoms.button.btn-icon>
</x-atoms.table.td>
