<div>
    <x-molecules.card title="Form Penjualan">
        <div class="row">
            <div class="col-8">
                <form>
                    <div class="row mb-6">
                        <div class="col-6">
                            <x-atoms.input.group-horizontal label="Customer" name="customer_id" required="required">
                                <x-atoms.input.text name="customer_id" wire:model.defer="customer_nama" readonly="" data-bs-toggle="modal" data-bs-target="#customer_modal"/>
                            </x-atoms.input.group-horizontal>
                        </div>
                        <div class="col-6">
                            <x-atoms.input.group-horizontal label="Jenis Bayar" name="jenis_bayar" required="required">
                                <x-atoms.input.select name="jenis_bayar" wire:model.defer="jenis_bayar">
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
                                    <x-atoms.input.singledaterange id="tgl_nota" name="tgl_nota" wire:model.defer="tgl_nota"/>
                                </div>
                            </x-atoms.input.group-horizontal>
                        </div>
                        <div class="col-6">
                            <x-atoms.input.group-horizontal label="Tgl Tempo" name="tgl_tempo" required="required">
                                <div class="input-group">
                                    <x-atoms.input.singledaterange id="tgl_tempo" name="tgl_tempo" wire:model.defer="tgl_tempo"/>
                                </div>
                            </x-atoms.input.group-horizontal>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <div class="col-6">
                            <x-atoms.input.group-horizontal label="Gudang" name="gudang" required="required">
                                <x-atoms.input.select name="gudang_id" wire:model.defer="gudang_id">
                                    <option>Dipilih</option>
                                    @forelse($gudang_data as $row)
                                        <option value="{{$row->id}}">{{$row->nama}}</option>
                                    @empty
                                        <option>Tidak Ada Data</option>
                                    @endforelse
                                </x-atoms.input.select>
                            </x-atoms.input.group-horizontal>
                        </div>
                        <div class="col-6">
                            <x-atoms.input.group-horizontal label="Keterangan" name="keterangan">
                                <x-atoms.input.text wire:model.defer="keterangan" />
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
                    @forelse($data_detail as $index => $row)
                        <tr class="align-middle">
                            <td class="text-center">{{$row['kode_lokal']}}</td>
                            <td>{{$row['nama_produk']}}</td>
                            <td class="text-end">{{rupiah_format($row['harga'])}}</td>
                            <td class="text-center">{{$row['jumlah']}}</td>
                            <td class="text-center">{{diskon_format($row['diskon'], 2)}}</td>
                            <td class="text-end">{{rupiah_format($row['sub_total'])}}</td>
                            <td>
                                <button type="button" class="btn btn-flush btn-active-color-info btn-icon" wire:click="editLine({{$index}})"><i class="la la-edit fs-2"></i></button>
                                <button type="button" class="btn btn-flush btn-active-color-info btn-icon" wire:click="destroyLine({{$index}})"><i class="la la-trash fs-2"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak Ada Data</td>
                        </tr>
                    @endforelse
                    <x-slot name="footer">
                        <tr>
                            <td colspan="2"></td>
                            <td colspan="2">Total</td>
                            <td colspan="2">
                                <x-atoms.input.text name="total_rupiah" wire:model="total_rupiah" readonly=""/>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                            <td colspan="2">Biaya Lain</td>
                            <td colspan="2">
                                <x-atoms.input.text name="biaya_lain" wire:model="biaya_lain" wire:keyup="hitungTotalBayar" />
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                            <td colspan="2">PPN</td>
                            <td colspan="2">
                                <x-atoms.input.text name="ppn" wire:model="ppn" wire:keyup="hitungTotalBayar" />
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                            <td colspan="2">Total Bayar</td>
                            <td colspan="2">
                                <x-atoms.input.text name="total_bayar_rupiah" wire:model="total_bayar_rupiah" wire:keyup="hitungTotalBayar" readonly=""/>
                            </td>
                            <td></td>
                        </tr>
                    </x-slot>
                </x-atoms.table>

            </div>
            <div class="col-4 border">
                <form wire:ignore.self>
                    <div class="pb-5 pt-5">
                        <x-atoms.input.group-horizontal name="namaProduk" label="Produk">
                            <x-atoms.input.textarea wire:model.defer="namaProduk" />
                        </x-atoms.input.group-horizontal>
                    </div>
                    <div class="pt-5">
                        <x-atoms.input.group-horizontal name="hargaRupiah" label="Harga">
                            <x-atoms.input.text wire:model.defer="hargaRupiah" wire:key="hargaRupiah" class="text-end" readonly=""/>
                        </x-atoms.input.group-horizontal>
                    </div>
                    <div class="pt-5">
                        <x-atoms.input.group-horizontal name="diskonProduk" label="Diskon">
                            <div class="input-group">
                                <x-atoms.input.text wire:model.defer="diskonProduk" wire:keyup="hitungSubTotal" wire:key="diskonProduk"/>
                                <span class="input-group-text">%</span>
                            </div>
                        </x-atoms.input.group-horizontal>
                    </div>
                    <div class="pt-5">
                        <x-atoms.input.group-horizontal name="diskonProduk" label="">
                            <div class="input-group">
                                <span class="input-group-text">Rp. </span>
                                <x-atoms.input.text wire:model.defer="hargaSetelahDiskonRupiah" wire:keyup="hitungSubTotal" readonly=""/>
                            </div>
                        </x-atoms.input.group-horizontal>
                    </div>
                    <div class="pt-5">
                        <x-atoms.input.group-horizontal name="jumlahProduk" label="Jumlah">
                            <x-atoms.input.text wire:model.defer="jumlahProduk" wire:keyup="hitungSubTotal" wire:key="jumlahProduk"/>
                        </x-atoms.input.group-horizontal>
                    </div>
                    <div class="pt-5">
                        <x-atoms.input.group-horizontal name="detailSubTotal" label="Sub Total">
                            <x-atoms.input.text wire:model.defer="subTotalHargaRupiah" readonly="" />
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
                    <x-atoms.button.btn-primary wire:click="update">Update All</x-atoms.button.btn-primary>
                @else
                    <x-atoms.button.btn-primary wire:click="store">Save All</x-atoms.button.btn-primary>
                @endif
            </div>
        </x-slot>

    </x-molecules.card>

    <x-molecules.modal title="Daftar Customer" id="customer_modal" size="xl" wire:ignore.self>
        <livewire:datatables.customer-set-table />
        <x-slot name="footer"></x-slot>
    </x-molecules.modal>

    <x-molecules.modal title="Daftar Produk" id="produk_modal" size="xl" wire:ignore.self>
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
                @this.tgl_nota = e.target.value;
            })

            \
        </script>
    @endpush
</div>
