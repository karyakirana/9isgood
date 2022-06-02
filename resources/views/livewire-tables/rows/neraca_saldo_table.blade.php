
<x-atoms.table.td width="30%" align="left">
    {{$row->akun->deskripsi}}
</x-atoms.table.td>
<x-atoms.table.td align="end">
    {{rupiah_format($row->debet)}}
</x-atoms.table.td>
<x-atoms.table.td align="end">
    {{rupiah_format($row->kredit)}}
</x-atoms.table.td>