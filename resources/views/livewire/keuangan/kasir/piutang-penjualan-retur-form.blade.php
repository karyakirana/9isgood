<div>
    <x-molecules.card title="Form Set Piutang Retur">
        <div class="row">
            <div class="col 6">
                <x-atoms.input.group-horizontal class="mb-4" label="Customer">
                    <x-atoms.input.text name="customer" wire:model.defer="customer_nama" data-bs-toggle="modal" data-bs-target="#customer_modal" readonly/>
                </x-atoms.input.group-horizontal>
                <x-atoms.input.group-horizontal class="mb-4" label="Keterangan">
                    <x-atoms.input.text name="keterangan" />
                </x-atoms.input.group-horizontal>
            </div>
            <div class="col 6">
                <x-atoms.input.group-horizontal class="mb-4" label="Tanggal">
                    <x-atoms.input.singledaterange name="tgl_jurnal" />
                </x-atoms.input.group-horizontal>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalDaftarPenjualanRetur">Add Retur</button>
                <button type="button" class="btn btn-danger btn-active-color-gray-200" wire:click="store">Simpan</button>
            </div>
        </div>
        <x-atoms.table>
            <x-slot:head>
                <tr>
                    <th>ID</th>
                    <th>Retur</th>
                    <th>PPN</th>
                    <th>Biaya Lain</th>
                    <th>Total Bayar</th>
                    <th></th>
                </tr>
            </x-slot:head>
            @forelse($data_detail as $index => $row)
                <tr>
                    <x-atoms.table.td align="center">{{$row['kode']}}</x-atoms.table.td>
                    <x-atoms.table.td align="center">{{$row['jenis']}}</x-atoms.table.td>
                    <x-atoms.table.td align="end">{{$row['ppn']}}</x-atoms.table.td>
                    <x-atoms.table.td align="end">{{$row['biaya_lain']}}</x-atoms.table.td>
                    <x-atoms.table.td align="end">{{rupiah_format($row['total_bayar'])}}</x-atoms.table.td>
                    <x-atoms.table.td>
                        <x-atoms.button.btn-icon wire:click="unsetRowTable({{$index}})"><i class="la la-trash fs-2"></i></x-atoms.button.btn-icon>
                    </x-atoms.table.td>
                </tr>
            @empty
                <tr>
                    <x-atoms.table.td colspan="6" align="center">Tidak Ada Data</x-atoms.table.td>
                </tr>
            @endforelse
        </x-atoms.table>
    </x-molecules.card>

    <x-organisms.modals.daftar-customer />
    <x-organisms.modals.daftar-penjualan-retur :lastsession="true"/>
</div>
