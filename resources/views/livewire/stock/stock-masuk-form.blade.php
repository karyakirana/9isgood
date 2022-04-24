<div>
    <x-molecules.card title="Transaksi Stock Masuk {{ucwords($kondisi)}}">
        <div class="row">
            <div class="col-8">
                <form>
                    <div class="row mb-6">
                        <div class="col-6">
                            <x-atoms.input.group-horizontal label="Supplier" name="supplier_id" required="required">
                                <x-atoms.input.text name="supplier_id" wire:model.defer="supplier_nama" readonly="" data-bs-toggle="modal" data-bs-target="#supplier_modal"/>
                            </x-atoms.input.group-horizontal>
                        </div>
                        <div class="col-6">
                            <x-atoms.input.group-horizontal label="Tgl Masuk" name="tgl_masuk" required="required">
                                <div class="input-group">
                                    <x-atoms.input.singledaterange id="tgl_masuk" name="tgl_masuk" wire:model.defer="tgl_masuk"/>
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

                    <div class="row mb-6">
                        <div class="col-6">
                            <x-atoms.input.group-horizontal label="Pre-Order" name="nomor_po">
                                <x-atoms.input.text wire:model.defer="nomor_po" />
                            </x-atoms.input.group-horizontal>
                        </div>
                    </div>

                </form>

                <x-atoms.table>
                    <x-slot name="head">
                        <tr>
                            <th width="10%">ID</th>
                            <th width="25%">Item</th>
                            <th width="10%">Jumlah</th>
                            <th width="10%"></th>
                        </tr>
                    </x-slot>
                    @forelse($data_detail as $row)
                        <tr class="align-middle">
                            <td class="text-center">{{$row['kode_lokal']}}</td>
                            <td>{{$row['nama_produk']}}</td>
                            <td class="text-center">{{$row['jumlah']}}</td>
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
                </x-atoms.table>
            </div>
            <div class="col-4 border">
                <form wire:ignore.self>
                    <div class="pb-5 pt-5">
                        <x-atoms.input.group-horizontal name="idProduk" label="ID Produk">
                            <x-atoms.input.textarea wire:model.defer="idProduk" />
                        </x-atoms.input.group-horizontal>
                    </div>
                    <div class="pb-5 pt-5">
                        <x-atoms.input.group-horizontal name="namaProduk" label="Produk">
                            <x-atoms.input.textarea wire:model.defer="namaProduk" />
                        </x-atoms.input.group-horizontal>
                    </div>
                    <div class="pt-5">
                        <x-atoms.input.group-horizontal name="jumlahProduk" label="Jumlah">
                            <x-atoms.input.text wire:model.defer="jumlahProduk" wire:key="jumlahProduk"/>
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

    <x-molecules.modal title="Daftar Supplier" id="supplier_modal" size="xl" wire:ignore.self>
        <livewire:datatables.supplier-set-table />
        <x-slot name="footer"></x-slot>
    </x-molecules.modal>

    <x-molecules.modal title="Daftar Produk" id="produk_modal" size="xl" wire:ignore.self>
        <livewire:datatables.produk-set-table />
        <x-slot name="footer"></x-slot>
    </x-molecules.modal>

    @push('custom-scripts')
    <script>
        let modal_supplier = document.getElementById('supplier_modal');
        let supplierModal = new bootstrap.Modal(modal_supplier);

        Livewire.on('set_supplier', function (){
            supplierModal.hide();
        })

        let modal_produk = document.getElementById('produk_modal');
        let produkModal = new bootstrap.Modal(modal_produk);

        Livewire.on('set_produk', function (){
            produkModal.hide();
        })

        $('#tgl_masuk').on('change', function (e) {
            let date = $(this).data("#tgl_masuk");
            // eval(date).set('tglLahir', $('#tglLahir').val())
            console.log(e.target.value);
            @this.tgl_masuk = e.target.value;
        })
    </script>
@endpush


</div>
