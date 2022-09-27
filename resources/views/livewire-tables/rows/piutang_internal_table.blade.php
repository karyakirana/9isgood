<x-atoms.table.td>
    {{$row->kode}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->saldoPegawai->pegawai->nama}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->jenis_piutang}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{rupiah_format($row->nominal)}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->keterangan}}
</x-atoms.table.td>
<x-atoms.table.td>
    <x-atoms.button.btn-icon-link :href="route('kasir.piutanginternal.form.edit', $row->id)"><i class="far fa-edit"></i></x-atoms.button.btn-icon-link>
    <x-atoms.button.btn-icon><i class="fa fa-trash"></i></x-atoms.button.btn-icon>
</x-atoms.table.td>
