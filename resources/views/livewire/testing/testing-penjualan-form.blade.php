<div>
    <div>
        <x-molecules.card title="Form Penjualan">
            <div class="row">
                <div class="col-8">
                    <form>
                        <div class="row mb-6">
                            <div class="col-6">
                                <x-atoms.input.group-horizontal label="Customer" name="customer_id" required="required">
                                    <x-atoms.input.text name="customer_id" readonly="" data-bs-toggle="modal" data-bs-target="#customer_modal"/>
                                </x-atoms.input.group-horizontal>
                            </div>
                            <div class="col-6">
                                <x-atoms.input.group-horizontal label="Jenis Bayar" name="jenis_bayar" required="required">
                                    <x-atoms.input.select name="jenis_bayar" >
                                        <option>Dipilih</option>
                                        <option value="cash">Tunai</option>
                                        <option value="tempo">Tempo</option>
                                    </x-atoms.input.select>
                                </x-atoms.input.group-horizontal>
                            </div>
                        </div>

                        <div class="row mb-6">
                            <div class="col-6">
                                <x-atoms.input.group-horizontal label="Tgl Nota" name="tgl_nota" required="required">
                                    <div class="input-group">
                                        <x-atoms.input.singledaterange id="tgl_nota" name="tgl_nota" />
                                    </div>
                                </x-atoms.input.group-horizontal>
                            </div>
                            <div class="col-6">
                                <x-atoms.input.group-horizontal label="Tgl Tempo" name="tgl_tempo" required="required">
                                    <div class="input-group">
                                        <x-atoms.input.singledaterange id="tgl_tempo" name="tgl_tempo" />
                                    </div>
                                </x-atoms.input.group-horizontal>
                            </div>
                        </div>

                        <div class="row mb-6">
                            <div class="col-6">
                                <x-atoms.input.group-horizontal label="Gudang" name="gudang" required="required">
                                    <x-atoms.input.select name="gudang_id" >
                                        <option>Dipilih</option>

                                    </x-atoms.input.select>
                                </x-atoms.input.group-horizontal>
                            </div>
                            <div class="col-6">
                                <x-atoms.input.group-horizontal label="Keterangan" name="keterangan">
                                    <x-atoms.input.text  />
                                </x-atoms.input.group-horizontal>
                            </div>
                        </div>

                    </form>

                    <x-atoms.table>
                        <x-slot name="head">
                            <tr>
                                <th width="10%">ID</th>
                                <th width="25%">Item</th>
                                <th width="15%">Harga</th>
                                <th width="10%">Jumlah</th>
                                <th width="10%">Diskon</th>
                                <th width="20%">Sub Total</th>
                                <th width="10%"></th>
                            </tr>
                        </x-slot>

                        <x-slot name="footer">
                            <tr>
                                <td colspan="2"></td>
                                <td colspan="2">Total</td>
                                <td colspan="2">
                                    <x-atoms.input.text name="total_rupiah"  readonly=""/>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                                <td colspan="2">Biaya Lain</td>
                                <td colspan="2">
                                    <x-atoms.input.text name="biaya_lain"  />
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                                <td colspan="2">PPN</td>
                                <td colspan="2">
                                    <x-atoms.input.text name="ppn"  />
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                                <td colspan="2">Total Bayar</td>
                                <td colspan="2">
                                    <x-atoms.input.text name="total_bayar_rupiah"  readonly=""/>
                                </td>
                                <td></td>
                            </tr>
                        </x-slot>
                    </x-atoms.table>

                </div>
                <div class="col-4 border">
                    <form >
                        <div class="pb-5 pt-5">
                            <x-atoms.input.group-horizontal name="namaProduk" label="Produk">
                                <x-atoms.input.textarea  />
                            </x-atoms.input.group-horizontal>
                        </div>
                        <div class="pt-5">
                            <x-atoms.input.group-horizontal name="hargaRupiah" label="Harga">
                                <x-atoms.input.text  class="text-end" readonly=""/>
                            </x-atoms.input.group-horizontal>
                        </div>
                        <div class="pt-5">
                            <x-atoms.input.group-horizontal name="diskonProduk" label="Diskon">
                                <div class="input-group">
                                    <x-atoms.input.text />
                                    <span class="input-group-text">%</span>
                                </div>
                            </x-atoms.input.group-horizontal>
                        </div>
                        <div class="pt-5">
                            <x-atoms.input.group-horizontal name="diskonProduk" label="">
                                <div class="input-group">
                                    <span class="input-group-text">Rp. </span>
                                    <x-atoms.input.text  readonly=""/>
                                </div>
                            </x-atoms.input.group-horizontal>
                        </div>
                        <div class="pt-5">
                            <x-atoms.input.group-horizontal name="jumlahProduk" label="Jumlah">
                                <x-atoms.input.text />
                            </x-atoms.input.group-horizontal>
                        </div>
                        <div class="pt-5">
                            <x-atoms.input.group-horizontal name="detailSubTotal" label="Sub Total">
                                <x-atoms.input.text  readonly="" />
                            </x-atoms.input.group-horizontal>
                        </div>
                    </form>

                    <div class="text-center pb-4 pt-5">
                        <x-atoms.button.btn-modal color="info" target="#produk_modal">Add Produk</x-atoms.button.btn-modal>
{{--                        @if($update)--}}
                            <button type="button" class="btn btn-primary" >update Data</button>
{{--                        @else--}}
                            <button type="button" class="btn btn-primary" >Save Data</button>
{{--                        @endif--}}
                    </div>
                </div>
            </div>

            <x-slot name="footer">
                <div class="d-flex justify-content-end">
{{--                    @if($mode == 'update')--}}
                        <x-atoms.button.btn-primary >Update All</x-atoms.button.btn-primary>
{{--                    @else--}}
                        <x-atoms.button.btn-primary >Save All</x-atoms.button.btn-primary>
{{--                    @endif--}}
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

{{--        @push('custom-scripts')--}}
{{--            <script>--}}
{{--                let modal_customer = document.getElementById('customer_modal');--}}
{{--                let customerModal = new bootstrap.Modal(modal_customer);--}}

{{--                Livewire.on('set_customer', function (){--}}
{{--                    customerModal.hide();--}}
{{--                })--}}

{{--                let modal_produk = document.getElementById('produk_modal');--}}
{{--                let produkModal = new bootstrap.Modal(modal_produk);--}}

{{--                Livewire.on('set_produk', function (){--}}
{{--                    produkModal.hide();--}}
{{--                })--}}

{{--                $('#tglNota').on('change', function (e) {--}}
{{--                    let date = $(this).data("#tgl_nota");--}}
{{--                    // eval(date).set('tglLahir', $('#tglLahir').val())--}}
{{--                    console.log(e.target.value);--}}
{{--                    @this.tgl_nota = e.target.value;--}}
{{--                })--}}
{{--            </script>--}}
{{--        @endpush--}}
    </div>

</div>
