<x-atoms.table.td>
    {{$row->kode}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{tanggalan_format($row->created_at)}}
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
    <x-atoms.button.btn-icon-link :href="route('test.persediaan.transaksi.transaksiId', $row->id)" color="info"><i class="far fa-edit"></i></x-atoms.button.btn-icon-link>
    <x-atoms.button.btn-icon color="dark" onclick="Livewire.emit('detail', {{$row->id}})"><i class="fas fa-indent"></i></x-atoms.button.btn-icon>
    <x-atoms.button.btn-icon color="danger"><i class="fas fa-trash"></i></x-atoms.button.btn-icon>
</x-atoms.table.td>
