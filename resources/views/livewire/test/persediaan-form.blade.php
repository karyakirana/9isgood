<div>
    <x-molecules.card title="Persediaan Transaksi">
        <div class="row">
            <div class="col-9">
                <!--begin:form-->
                <form>
                    <div class="row mb-5">
                        <div class="col-6">
                            <x-atoms.input.group-horizontal label="Gudang" name="gudang_id">
                                <x-atoms.input.select wire:model.defer="gudang_id"></x-atoms.input.select>
                            </x-atoms.input.group-horizontal>
                        </div>
                        <div class="col-6">
                            <x-atoms.input.group-horizontal label="Kondisi" name="kondisi">
                                <x-atoms.input.select wire:model.defer="kondisi">
                                    <option>Dipilih</option>
                                    <option value="baik">Baik</option>
                                    <option value="rusak">Rusak</option>
                                </x-atoms.input.select>
                            </x-atoms.input.group-horizontal>
                        </div>
                    </div>
                </form>
                <!--end:form-->
                <!--begin:table-->
                <x-atoms.table>
                    <x-slot name="head">
                        <tr>
                            <th>ID</th>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th>Harga</th>
                            <th>Sub Total</th>
                            <th></th>
                        </tr>
                    </x-slot>
                    @forelse($data_detail as $row)
                        <tr>
                            <x-atoms.table.td>
                                {{$row['produk_kode_lokal']}}
                            </x-atoms.table.td>
                            <x-atoms.table.td>
                                {{$row['produk_nama']}}
                            </x-atoms.table.td>
                            <x-atoms.table.td>
                                {{$row['jumlah']}}
                            </x-atoms.table.td>
                            <x-atoms.table.td>
                                {{$row['harga']}}
                            </x-atoms.table.td>
                            <x-atoms.table.td>
                                {{$row['sub_total']}}
                            </x-atoms.table.td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak Ada data</td>
                        </tr>
                    @endforelse
                </x-atoms.table>
                <!--end:table-->
            </div>
            <div class="col-3">
                <form class="border p-5">
                    <x-atoms.input.group class="mb-5" label="Produk" name="produk_nama">
                        <x-atoms.input.textarea wire:model.defer="produk_nama"></x-atoms.input.textarea>
                    </x-atoms.input.group>
                    <x-atoms.input.group class="mb-5" label="Harga Jual" name="produk_harga">
                        <x-atoms.input.text wire:model.defer="produk_harga" />
                    </x-atoms.input.group>
                    <x-atoms.input.group class="mb-5" label="Harga" name="harga">
                        <x-atoms.input.text wire:model.defer="harga" />
                    </x-atoms.input.group>
                    <x-atoms.input.group class="mb-5" label="Jumlah" name="jumlah">
                        <x-atoms.input.text wire:model.defer="jumlah" />
                    </x-atoms.input.group>
                    <x-atoms.input.group class="mb-5" label="Sub Total" name="sub_total">
                        <x-atoms.input.text wire:model.defer="sub_total" />
                    </x-atoms.input.group>
                    <div class="separator separator-dashed mb-8"></div>
                    <div class="row mb-5">
                        <!--begin::Col-->
                        <div class="col">
                            @if($update==true)
                                <x-atoms.button.btn-info wire:click="update" class="w-100">Update</x-atoms.button.btn-info>
                            @else
                                <x-atoms.button.btn-info wire:click="add" class="w-100">Add</x-atoms.button.btn-info>
                            @endif
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col">
                            <x-atoms.button.btn-danger wire:click="setJurnalTransaksi" class="w-100">RESET</x-atoms.button.btn-danger>
                        </div>
                        <!--end::Col-->
                    </div>
                    @if($mode=='update')
                        <x-atoms.button.btn-primary wire:click="put" class="w-100">UPDATE</x-atoms.button.btn-primary>
                    @else
                        <x-atoms.button.btn-primary wire:click="store" class="w-100">SIMPAN</x-atoms.button.btn-primary>
                    @endif
                </form>
            </div>
        </div>


    </x-molecules.card>
</div>
