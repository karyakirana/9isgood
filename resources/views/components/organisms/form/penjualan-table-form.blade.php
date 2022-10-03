@props(['$dataDetail'=>[]])
<x-atoms.table>
    <x-slot name="head">
        <tr>
            <th width="12%">ID</th>
            <th width="25%">Item</th>
            <th width="15%">Harga</th>
            <th width="10%">Jumlah</th>
            <th width="10%">Diskon</th>
            <th width="15%">Sub Total</th>
            <th width="13%"></th>
        </tr>
    </x-slot>
    @forelse($dataDetail as $index => $row)
        <tr class="align-middle">
            <td class="text-center">{{$row['kode_lokal']}}</td>
            <td>{{$row['produk_nama']}}</td>
            <td class="text-end">{{rupiah_format($row['harga'])}}</td>
            <td class="text-center">{{$row['jumlah']}}</td>
            <td class="text-center">{{diskon_format($row['diskon'], 2)}}</td>
            <td class="text-end">{{rupiah_format($row['sub_total'])}}</td>
            <td>
                <button type="button" class="btn btn-flush btn-active-color-info btn-icon" wire:click="editLine({{$index}})"><i class="la la-edit fs-2"></i></button>
                <button type="button" class="btn btn-flush btn-active-color-info btn-icon" wire:click="removeLine({{$index}})"><i class="la la-trash fs-2"></i></button>
        </tr>
    @empty
        <tr>
            <x-atoms.table.td colspan="7" class="text-center">Tidak Ada Data</x-atoms.table.td>
        </tr>
    @endforelse

    <x-slot name="footer">
        <tr>
            <td colspan="2"></td>
            <td colspan="2">Total</td>
            <td colspan="2">
                <x-atoms.input.text name="total_penjualan_rupiah" wire:model.defer="total_penjualan_rupiah" readonly=""/>
            </td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td colspan="2">Biaya Lain</td>
            <td colspan="2">
                <x-atoms.input.text name="biaya_lain"  wire:model="biaya_lain"/>
            </td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td colspan="2">PPN</td>
            <td colspan="2">
                <x-atoms.input.text name="ppn" wire:model="ppn" />
            </td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td colspan="2">Total Bayar</td>
            <td colspan="2">
                <x-atoms.input.text name="total_bayar_rupiah" wire:model.defer="total_bayar_rupiah" readonly=""/>
            </td>
            <td></td>
        </tr>
    </x-slot>
</x-atoms.table>
