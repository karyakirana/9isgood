<x-atoms.table.td width="10%">
    {{$row->kode}}
</x-atoms.table.td>
<x-atoms.table.td width="15%">
    {{$row->customer->nama}}
</x-atoms.table.td>
<x-atoms.table.td width="12%">
    {{tanggalan_format($row->tgl_nota)}}
</x-atoms.table.td>
<x-atoms.table.td width="12%">
    @if($row->tgl_tempo)
        {{tanggalan_format($row->tgl_tempo)}}
    @endif
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->jenis_bayar}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->status_bayar}}
</x-atoms.table.td>
<x-atoms.table.td width="10%" align="end">
    {{rupiah_format($row->total_bayar)}}
</x-atoms.table.td>
<x-atoms.table.td align="center">
    @if($row->status_bayar == 'belum')
        <x-atoms.button.btn-icon-link :href="route('penjualan.trans').'/'.$row->id" color="info"><i class="far fa-edit"></i></x-atoms.button.btn-icon-link>
        <x-atoms.button.btn-icon color="danger"><i class="fas fa-trash"></i></x-atoms.button.btn-icon>
    @endif
    <x-atoms.button.btn-icon color="dark" onclick="Livewire.emit('showPenjualanDetail', {{$row->id}})"><i class="fas fa-indent"></i></x-atoms.button.btn-icon>
    <x-atoms.button.btn-icon-link href="penjualan/print/{{$row->id}}" color="info"><i class="fas fa-print"></i></x-atoms.button.btn-icon-link>
    <x-atoms.button.btn-icon-link href="penjualan/pdf/{{$row->id}}" color="success"><i class="fas fa-file-powerpoint"></i></x-atoms.button.btn-icon-link>
</x-atoms.table.td>
