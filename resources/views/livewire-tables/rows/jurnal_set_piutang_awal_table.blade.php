<x-atoms.table.td>
    {{$row->kode}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{tanggalan_format($row->tgl_jurnal)}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->customer->nama}}
</x-atoms.table.td>
<x-atoms.table.td align="center">
    {{ucfirst($row->users->name)}}
</x-atoms.table.td>
<x-atoms.table.td align="end">
    {{rupiah_format($row->total_piutang)}}
</x-atoms.table.td>
<x-atoms.table.td align="center">
    @if($row->jenis == 'penjualan')
        <x-atoms.button.btn-icon-link :href="route('keuangan.neraca.awal.piutang-penjualan.edit', $row->id)" color="info"><i class="far fa-edit"></i></x-atoms.button.btn-icon-link>
    @else
        <x-atoms.button.btn-icon-link :href="route('keuangan.neraca.awal.piutang-retur.edit', $row->id)" color="info"><i class="far fa-edit"></i></x-atoms.button.btn-icon-link>
    @endif
    <x-atoms.button.btn-icon color="danger"><i class="fas fa-trash"></i></x-atoms.button.btn-icon>
</x-atoms.table.td>
