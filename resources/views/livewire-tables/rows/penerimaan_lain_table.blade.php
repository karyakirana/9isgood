<x-atoms.table.td>
    {{$row->kode}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->tgl_penerimaan}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->pegawai->nama ?? ''}}
    {{$row->asal}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{ucfirst($row->users->name)}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{rupiah_format($row->nominal)}}
</x-atoms.table.td>
<x-atoms.table.td>
    <x-atoms.button.btn-icon-link :href="route('kasir.penerimaan.lain.form.edit', $row->id)"><i class="fa fa-edit"></i></x-atoms.button.btn-icon-link>
    <x-atoms.button.btn-icon wire:click.prevent="$emit('destroy', {{$row->id}})"><i class="fa fa-trash"></i></x-atoms.button.btn-icon>
</x-atoms.table.td>
