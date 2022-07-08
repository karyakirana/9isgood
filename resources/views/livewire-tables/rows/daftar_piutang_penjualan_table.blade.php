<x-atoms.table.td>
    {{$row->customer->nama}}
</x-atoms.table.td>
<x-atoms.table.td align="end">
    {{rupiah_format($row->saldo)}}
</x-atoms.table.td>
<x-atoms.table.td align="center" width="10%">
    <x-atoms.button.btn-icon-link :href="route('kasir.piutang.penjualan.detail', ['customer_id' => $row->customer_id])" color="primary"><i class="fas fa-indent"></i></x-atoms.button.btn-icon-link>
</x-atoms.table.td>
