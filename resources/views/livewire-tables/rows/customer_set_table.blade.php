<x-atoms.table.td width="10%" align="center">
    {{$row->kode}}
</x-atoms.table.td>
<x-atoms.table.td width="20%">
    {{$row->nama}}
</x-atoms.table.td>
<x-atoms.table.td width="15%">
    @if($row->telepon == 0)
    @else
        {{$row->telepon}}
    @endif
</x-atoms.table.td>
<x-atoms.table.td width="10%" align="center">
    @if($row->diskon == 0)
    @else
        {{$row->diskon}}%
    @endif
</x-atoms.table.td>
<x-atoms.table.td width="30%">
    {{$row->alamat}}
</x-atoms.table.td>
<x-atoms.table.td width="15%" align="center">
    <x-atoms.button.btn-icon onclick="Livewire.emit('set_customer', {{$row->id}})">
        set
    </x-atoms.button.btn-icon>
</x-atoms.table.td>
