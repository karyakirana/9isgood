<x-atoms.table.td align="center">{{$loop->iteration}}</x-atoms.table.td>
<x-atoms.table.td>{{$row->customer->nama}}</x-atoms.table.td>
<x-atoms.table.td align="end">{{rupiah_format($row->saldo)}}</x-atoms.table.td>
