<x-atoms.table.td>{{$row->kode}}</x-atoms.table.td>
<x-atoms.table.td>{{$row->jenis}}</x-atoms.table.td>
<x-atoms.table.td>{{$row->tgl_pengeluaran}}</x-atoms.table.td>
<x-atoms.table.td>{{$row->supplier->nama}}</x-atoms.table.td>
<x-atoms.table.td>{{$row->users->name}}</x-atoms.table.td>
<x-atoms.table.td>{{$row->total_pengeluaran}}</x-atoms.table.td>
<x-atoms.table.td>
    <x-atoms.button.btn-icon-link href="{{route('kasir.pengeluaran.pembelian.form.edit', $row->id)}}">edit</x-atoms.button.btn-icon-link>
    <x-atoms.button.btn-icon>delete</x-atoms.button.btn-icon>
</x-atoms.table.td>
