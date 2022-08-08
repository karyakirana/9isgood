@php
    $jurnalType = class_basename($row->jurnal_type);
@endphp
<x-atoms.table.td>
    {{Str::headline($jurnalType)}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->jurnalable_transaksi->kode}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{$row->akun->deskripsi}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{($row->nominal_debet) ? rupiah_format($row->nominal_debet) : null}}
</x-atoms.table.td>
<x-atoms.table.td>
    {{($row->nominal_kredit) ? rupiah_format($row->nominal_kredit) : null}}
</x-atoms.table.td>
