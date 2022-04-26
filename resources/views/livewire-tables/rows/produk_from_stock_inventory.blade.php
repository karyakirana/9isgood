<x-atoms.table.td>
    {{$row->produk->kode_lokal}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{ucwords($row->gudang->nama)}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->produk->nama}}<br>
    {{$row->produk->cover}} {{$row->produk->kategoriHarga->nama}}
</x-atoms.table.td>
<x-atoms.table.td align="end">
    {{rupiah_format($row->stock_saldo)}}
</x-atoms.table.td>
<x-atoms.table.td>
    <x-atoms.button.btn-icon onclick="Livewire.emit('setProduk', {{$row->produk_id}})">SET</x-atoms.button.btn-icon>
</x-atoms.table.td>
