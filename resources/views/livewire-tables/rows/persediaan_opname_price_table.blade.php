<x-atoms.table.td align="center" width="15%">{{$row->tgl_input}}</x-atoms.table.td>
<x-atoms.table.td width="10%">{{$row->kondisi}}</x-atoms.table.td>
<x-atoms.table.td width="10%">{{$row->gudang->nama}}</x-atoms.table.td>
<x-atoms.table.td>{{$row->produk->nama}}</x-atoms.table.td>
<x-atoms.table.td width="15%" align="end">{{rupiah_format($row->harga)}}</x-atoms.table.td>
