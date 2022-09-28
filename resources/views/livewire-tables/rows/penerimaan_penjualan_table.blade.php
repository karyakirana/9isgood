<x-atoms.table.td>
    {{$row->kode}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->tgl_penerimaan}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->customer->nama}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{rupiah_format($row->total_penerimaan)}}
</x-atoms.table.td>
<x-atoms.table.td>
    <x-atoms.button.btn-icon-link :href="route('kasir.penerimaan.penjualan.edit', $row->id)"><i class="fa fa-edit"></i></x-atoms.button.btn-icon-link>
</x-atoms.table.td>
