<div>
    <div class="d-flex flex-column flex-lg-row">
        <!-- begin:table cards-->
        <div class="flex-lg-row-fluid mb-10 mb-lg-0 me-lg-7 me-xl-10">
            <x-molecules.card title="Persediaan Transaksi">
                <!--begin:form-->
                <form>
                    <div class="row mb-5">
                        <div class="col-6">
                            <x-atoms.input.group-horizontal label="Gudang">
                                <x-atoms.input.select></x-atoms.input.select>
                            </x-atoms.input.group-horizontal>
                        </div>
                        <div class="col-6">
                            <x-atoms.input.group-horizontal label="Kondisi">
                                <x-atoms.input.select>
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
                            <td colspan="5">Tidak Ada data</td>
                        </tr>
                    @endforelse
                </x-atoms.table>
                <!--end:table-->
            </x-molecules.card>
        </div>
        <!-- end:table cards-->

        <!-- begin:form cards-->
        <div class="flex-lg-auto min-w-lg-300px">
            <x-molecules.card>
                <x-atoms.input.group class="mb-5" label="Produk">
                    <x-atoms.input.text />
                </x-atoms.input.group>
                <x-atoms.input.group class="mb-5" label="Harga Jual">
                    <x-atoms.input.text />
                </x-atoms.input.group>
                <x-atoms.input.group class="mb-5" label="Harga">
                    <x-atoms.input.text />
                </x-atoms.input.group>
                <x-atoms.input.group class="mb-5" label="Jumlah">
                    <x-atoms.input.text />
                </x-atoms.input.group>
                <x-atoms.input.group class="mb-5" label="Sub Total">
                    <x-atoms.input.text />
                </x-atoms.input.group>
                <div class="separator separator-dashed mb-8"></div>
                <div class="row mb-5">
                    <!--begin::Col-->
                    <div class="col">
                        @if($update==true)
                            <x-atoms.button.btn-info wire:click="updateLine" class="w-100">Update</x-atoms.button.btn-info>
                        @else
                            <x-atoms.button.btn-info wire:click="addLine" class="w-100">Add</x-atoms.button.btn-info>
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
                    <x-atoms.button.btn-primary wire:click="update" class="w-100">UPDATE</x-atoms.button.btn-primary>
                @else
                    <x-atoms.button.btn-primary wire:click="store" class="w-100">SIMPAN</x-atoms.button.btn-primary>
                @endif
            </x-molecules.card>
        </div>
        <!-- end:form cards-->
    </div>
</div>
