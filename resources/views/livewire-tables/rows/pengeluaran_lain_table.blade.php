<x-atoms.table.td>
    {{$row->kode}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->tgl_penerimaan}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->personRelation->nama ?? null}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->users->name}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{rupiah_format($row->nominal)}}
</x-atoms.table.td>
<x-atoms.table.td>
    <x-atoms.button.btn-icon-link :href="route('pengeluaran.lain.form.edit', $row->id)" ><i class="fa fa-edit"></i></x-atoms.button.btn-icon-link>
    <x-atoms.button.btn-icon><i class="fa fa-trash"></i></x-atoms.button.btn-icon>
</x-atoms.table.td>
