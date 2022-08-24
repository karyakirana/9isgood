<div>
    <div>
        {{-- alert store --}}
        @if(session()->has('storeMessage'))
            <x-molecules.alert-danger>
                {{ session('storeMessage') }}
            </x-molecules.alert-danger>
        @endif
        {{-- alert validation --}}
        @if($errors->all())
            <x-molecules.alert-danger>
                <ul>
                    @foreach($errors->all() as $messages)
                        <li>{{$messages}}</li>
                    @endforeach
                </ul>
            </x-molecules.alert-danger>
        @endif
        <x-molecules.card title="Form Penjualan">
            <div class="row">
                <div class="col-8">
                    <form>
                        <div class="row mb-6">
                            <div class="col-6">
                                <x-atoms.input.group-horizontal label="Customer" name="customerNama" required="required">
                                    <x-atoms.input.text name="customerNama" wire:model.defer="customerNama" data-bs-toggle="modal" data-bs-target="#customer_modal" readonly/>
                                </x-atoms.input.group-horizontal>
                            </div>
                            <div class="col-6">
                                <x-atoms.input.group-horizontal label="Jenis Bayar" name="jenisBayar" required="required">
                                    <x-atoms.input.select wire:model.defer="jenisBayar">
                                        <option>Dipilih</option>
                                        <option value="cash">Tunai</option>
                                        <option value="tempo">Tempo</option>
                                    </x-atoms.input.select>
                                </x-atoms.input.group-horizontal>
                            </div>
                        </div>

                        <div class="row mb-6">
                            <div class="col-6">
                                <x-atoms.input.group-horizontal label="Tgl Nota" name="tglNota" required="required">
                                    <div class="input-group">
                                        <x-atoms.input.singledaterange id="tglNota" name="tglNota" />
                                    </div>
                                </x-atoms.input.group-horizontal>
                            </div>
                            <div class="col-6">
                                <x-atoms.input.group-horizontal label="Tgl Tempo" name="tglTempo" required="required">
                                    <div class="input-group">
                                        <x-atoms.input.singledaterange id="tglTempo" name="tglTempo" />
                                    </div>
                                </x-atoms.input.group-horizontal>
                            </div>
                        </div>

                        <div class="row mb-6">
                            <div class="col-6">
                                <x-atoms.input.group-horizontal label="Gudang" name="gudangId" required="required">
                                    <x-atoms.input.select wire:model.defer="gudangId">
                                        <x-molecules.select.gudang-list />
                                    </x-atoms.input.select>
                                </x-atoms.input.group-horizontal>
                            </div>
                            <div class="col-6">
                                <x-atoms.input.group-horizontal label="Keterangan" name="keterangan">
                                    <x-atoms.input.text  wire:model.defer="keterangan"/>
                                </x-atoms.input.group-horizontal>
                            </div>
                        </div>

                    </form>

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
                                <td class="text-center">{{$row['produk_kode_lokal']}}</td>
                                <td>{{$row['produk_nama']}}</td>
                                <td class="text-end">{{rupiah_format($row['harga'])}}</td>
                                <td class="text-center">{{$row['jumlah']}}</td>
                                <td class="text-center">{{diskon_format($row['diskon'], 2)}}</td>
                                <td class="text-end">{{rupiah_format($row['sub_total'])}}</td>
                                <td>
                                    {{$index}}
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
                                    <x-atoms.input.text name="totalPenjualanRupiah" wire:model.defer="totalPenjualanRupiah" readonly=""/>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                                <td colspan="2">Biaya Lain</td>
                                <td colspan="2">
                                    <x-atoms.input.text name="biayaLain"  wire:model.defer="biayaLain"/>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                                <td colspan="2">PPN</td>
                                <td colspan="2">
                                    <x-atoms.input.text name="ppn" wire:model.defer="ppn" />
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                                <td colspan="2">Total Bayar</td>
                                <td colspan="2">
                                    <x-atoms.input.text name="totalBayarRupiah" wire:model.defer="totalBayarRupiah" readonly=""/>
                                </td>
                                <td></td>
                            </tr>
                        </x-slot>
                    </x-atoms.table>

                </div>
                <div class="col-4 border">
                    <form >
                        <div class="pb-5 pt-5">
                            <x-atoms.input.group-horizontal name="produkNama" label="Produk">
                                <x-atoms.input.textarea  wire:model.defer="produkNama"/>
                            </x-atoms.input.group-horizontal>
                        </div>
                        <div class="pt-5">
                            <x-atoms.input.group-horizontal name="hargaRupiah" label="Harga">
                                <x-atoms.input.text wire:model.defer="hargaRupiah" class="text-end" readonly=""/>
                            </x-atoms.input.group-horizontal>
                        </div>
                        <div class="pt-5">
                            <x-atoms.input.group-horizontal name="diskonProduk" label="Diskon">
                                <div class="input-group">
                                    <x-atoms.input.text wire:model.defer="diskon" wire:keyup="setSubTotal"/>
                                    <span class="input-group-text">%</span>
                                </div>
                            </x-atoms.input.group-horizontal>
                        </div>
                        <div class="pt-5">
                            <x-atoms.input.group-horizontal name="diskonProduk" label="">
                                <div class="input-group">
                                    <span class="input-group-text">Rp. </span>
                                    <x-atoms.input.text wire:model.defer="hargaDiskon" readonly=""/>
                                </div>
                            </x-atoms.input.group-horizontal>
                        </div>
                        <div class="pt-5">
                            <x-atoms.input.group-horizontal name="jumlah" label="Jumlah">
                                <x-atoms.input.text wire:model.defer="jumlah" wire:keyup="setSubTotal"/>
                            </x-atoms.input.group-horizontal>
                        </div>
                        <div class="pt-5">
                            <x-atoms.input.group-horizontal name="subTotalRupiah" label="Sub Total">
                                <x-atoms.input.text wire:model.defer="subTotalRupiah" readonly="" />
                            </x-atoms.input.group-horizontal>
                        </div>
                    </form>

                    <div class="text-center pb-4 pt-5">
                        <x-atoms.button.btn-modal color="info" target="#produk_modal">Add Produk</x-atoms.button.btn-modal>
                        @if($update)
                            <button type="button" class="btn btn-primary" wire:click="updateLine">update Data</button>
                        @else
                            <button type="button" class="btn btn-primary" wire:click="addLine">Save Data</button>
                        @endif
                    </div>
                </div>
            </div>

            <x-slot name="footer">
                <div class="d-flex justify-content-end">
                    @if($mode == 'update')
                        <x-atoms.button.btn-primary >Update All</x-atoms.button.btn-primary>
                    @else
                        <x-atoms.button.btn-primary wire:click="store">Save All</x-atoms.button.btn-primary>
                    @endif
                </div>
            </x-slot>

        </x-molecules.card>

        <x-molecules.modal title="Daftar Customer" id="customer_modal" size="xl" >
            <livewire:datatables.customer-set-table />
            <x-slot name="footer"></x-slot>
        </x-molecules.modal>

        <x-molecules.modal title="Daftar Produk" id="produk_modal" size="xl" >
            <livewire:datatables.produk-set-table />
            <x-slot name="footer"></x-slot>
        </x-molecules.modal>

        @push('custom-scripts')
            <script>
                let modal_customer = document.getElementById('customer_modal');
                let customerModal = new bootstrap.Modal(modal_customer);

                Livewire.on('set_customer', function (){
                    customerModal.hide();
                })

                let modal_produk = document.getElementById('produk_modal');
                let produkModal = new bootstrap.Modal(modal_produk);

                Livewire.on('set_produk', function (){
                    produkModal.hide();
                })

                $('#tglNota').on('change', function (e) {
                    let date = $(this).data("#tgl_nota");
                    // eval(date).set('tglLahir', $('#tglLahir').val())
                    console.log(e.target.value);
                    @this.tglNota = e.target.value;
                })

                $('#tglTempo').on('change', function (e) {
                    let date = $(this).data("#tglTempo");
                    // eval(date).set('tglLahir', $('#tglLahir').val())
                    console.log(e.target.value);
                    @this.tglTempo = e.target.value;
                })
            </script>
        @endpush
    </div>

</div>
