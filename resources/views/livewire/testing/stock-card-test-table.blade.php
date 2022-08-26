<div>
    <x-molecules.card title="Kartu Stock Barang {{$gudang ?? ''}}">
        <x-atoms.table>
            <x-slot name="head">
                <tr>
                    <x-atoms.table.td>Tanggal</x-atoms.table.td>
                    <x-atoms.table.td>No. Bukti</x-atoms.table.td>
                    <x-atoms.table.td>Keterangan</x-atoms.table.td>
                    <x-atoms.table.td>Masuk</x-atoms.table.td>
                    <x-atoms.table.td>Keluar</x-atoms.table.td>
                    <x-atoms.table.td>Sisa</x-atoms.table.td>
                </tr>
            </x-slot>
            @php
                $saldo = 0;
            @endphp
            @forelse($queryData as $row)
                <tr>
                    <x-atoms.table.td align="center">{{tanggalan_format($row->tanggal)}}</x-atoms.table.td>
                    <x-atoms.table.td align="center">{{$row->kode}}</x-atoms.table.td>
                    <x-atoms.table.td>{{$row->nama}} ({{$row->nama_keterangan}})</x-atoms.table.td>
                    <x-atoms.table.td align="end">{{$row->jumlah_masuk}}</x-atoms.table.td>
                    <x-atoms.table.td align="end">{{$row->jumlah_keluar}}</x-atoms.table.td>
                    <x-atoms.table.td align="end">{{rupiah_format($saldo += $row->jumlah_masuk - $row->jumlah_keluar)}}</x-atoms.table.td>
                </tr>
            @empty
                <tr>
                    <td class="text-center" colspan="6">Tidak ada data</td>
                </tr>
            @endforelse
        </x-atoms.table>
    </x-molecules.card>
</div>
