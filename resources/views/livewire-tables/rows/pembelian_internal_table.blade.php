<x-atoms.table.td>
    {{$row->stockMasukMorph->kode}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->supplier->nama}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{ucwords($row->gudang->nama)}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{tanggalan_format($row->tgl_nota)}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->nomor_surat_jalan}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{ucwords($row->users->name)}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->keterangan}}
</x-atoms.table.td>
<x-atoms.table.td>
    <x-atoms.button.btn-icon-link :href="route('stock.masuk.trans.edit', $row->id)" color="info"><i class="far fa-edit"></i></x-atoms.button.btn-icon-link>
    <x-atoms.button.btn-icon color="dark" onclick="Livewire.emit('showStockDetail', {{$row->stockMasukMorph->id}})"><i class="fas fa-indent"></i></x-atoms.button.btn-icon>
    <x-atoms.button.btn-icon color="danger"><i class="fas fa-trash"></i></x-atoms.button.btn-icon>
</x-atoms.table.td>
