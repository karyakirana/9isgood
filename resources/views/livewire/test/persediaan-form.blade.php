<div>
    @if(session()->has('error jumlah'))
        <x-molecules.alert-danger>
            {{session('error jumlah')}}
        </x-molecules.alert-danger>
    @endif
    <x-molecules.card title="Persediaan Transaksi">
        <div class="row">
            <div class="col-9">
                <!--begin:form-->
                <form>
                    <div class="row mb-5">
                        <div class="col-6">
                            <x-atoms.input.group-horizontal label="Gudang" name="gudang_id">
                                <x-atoms.input.select wire:model.defer="gudang_id" :disabled="$disabled">
                                    <option>Dipilih</option>
                                    @foreach($gudang_data as $row)
                                        <option value="{{$row->id}}">{{ucfirst($row->nama)}}</option>
                                    @endforeach
                                </x-atoms.input.select>
                            </x-atoms.input.group-horizontal>
                        </div>
                        <div class="col-6">
                            <x-atoms.input.group-horizontal label="Kondisi" name="kondisi">
                                <x-atoms.input.select wire:model.defer="kondisi" :disabled="$disabled">
                                    <option>Dipilih</option>
                                    <option value="baik">Baik</option>
                                    <option value="rusak">Rusak</option>
                                </x-atoms.input.select>
                            </x-atoms.input.group-horizontal>
                        </div>
                    </div>
                    <div class="row mb-5">
                        <div class="col-6">
                            <x-atoms.input.group-horizontal label="Jenis" name="jenis">
                                <x-atoms.input.select wire:model.defer="jenis" :disabled="$disabled">
                                    <option>Dipilih</option>
                                    <option value="masuk">Masuk</option>
                                    <option value="keluar">Keluar</option>
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
                        <x-atoms.input.textarea data-bs-toggle="modal" data-bs-target="#modalProduk" wire:model.defer="produk_nama"></x-atoms.input.textarea>
                    </x-atoms.input.group>
                    <x-atoms.input.group class="mb-5" label="Harga Jual" name="produk_harga">
                        <x-atoms.input.text wire:model.defer="produk_harga" />
                    </x-atoms.input.group>
                    <x-atoms.input.group class="mb-5" label="Harga" name="harga">
                        <x-atoms.input.text wire:model.defer="harga" />
                    </x-atoms.input.group>
                    <x-atoms.input.group class="mb-5" label="Jumlah" name="jumlah">
                        <x-atoms.input.text wire:model.defer="jumlah" wire:keyup="hitungSubTotal" wire:key="jumlah"/>
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

    <!-- modal Produk -->
    <x-molecules.modal size="xl" id="modalProduk">
        <livewire:datatables.produk-set-table />
    </x-molecules.modal>
    <!-- modal Produk -->

    @push('custom-scripts')
        <script>
            let modalProduk = new bootstrap.Modal(document.getElementById('modalProduk'));

            Livewire.on('set_produk', function (){
                modalProduk.hide();
            })

            let input1, input2, input3;

            $('document').ready(function(){
                input1 = document.getElementById('input-1');
                input2 = document.getElementById('input-2');
                input3 = document.getElementById('input-3');
                console.log(input1)

                Livewire.on('disabledSelect', function (){
                    input1.disabled = true;
                    input2.disabled = true;
                    input3.disabled = true;
                })
            });
        </script>
    @endpush
</div>
