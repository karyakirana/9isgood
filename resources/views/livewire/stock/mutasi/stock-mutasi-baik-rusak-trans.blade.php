<div>

    <div class="d-flex flex-column flex-lg-row">
        <!-- begin:table cards-->
        <div class="flex-lg-row-fluid mb-10 mb-lg-0 me-lg-7 me-xl-10">
            <x-molecules.card title="Form Stock Mutasi Baik ke Rusak">
                <form>
                    <div class="row">
                        <div class="col-6 mb-5">
                            <x-atoms.input.group-horizontal label="Gudang Asal" name="gudang" required="required">
                                <x-atoms.input.select name="gudang_id" wire:model.defer="gudang_id">
                                    <option>Dipilih</option>
                                    {{-- @forelse($gudang_data as $row)
                                        <option value="{{$row->id}}">{{$row->nama}}</option>
                                    @empty
                                        <option>Tidak Ada Data</option>
                                    @endforelse --}}
                                </x-atoms.input.select>
                            </x-atoms.input.group-horizontal>
                        </div>
                        <div class="col-6 mb-5">
                            <x-atoms.input.group-horizontal label="Gudang Tujuan" name="gudang" required="required">
                                <x-atoms.input.select name="gudang_id" wire:model.defer="gudang_id">
                                    <option>Dipilih</option>
                                    {{-- @forelse($gudang_data as $row)
                                        <option value="{{$row->id}}">{{$row->nama}}</option>
                                    @empty
                                        <option>Tidak Ada Data</option>
                                    @endforelse --}}
                                </x-atoms.input.select>
                            </x-atoms.input.group-horizontal>
                        </div>
                    </div>     
                    <div class="row">
                        <div class="col-6 mb-5">
                            <x-atoms.input.group-horizontal label="Tgl Mutasi" name="tgl_mutasi">
                                <x-atoms.input.singledaterange wire:model.defer="tgl_mutasi" id="tgl_mutasi" readonly/>
                            </x-atoms.input.group-horizontal>
                        </div>
                        <div class="col-6 mb-5">
                            <x-atoms.input.group-horizontal label="Keterangan" name="keterangan">
                                <x-atoms.input.text wire:model.defer="keterangan" />
                            </x-atoms.input.group-horizontal>
                        </div>
                    </div>
                </form>

                <x-atoms.table>
                    <x-slot name="head">
                        <tr>
                            <x-atoms.table.td align="center" width="15%">ID</x-atoms.table.td>
                            <x-atoms.table.td align="center" width="50%">Produk</x-atoms.table.td>
                            <x-atoms.table.td align="center" width="20%">Jumlah</x-atoms.table.td>
                            <x-atoms.table.td align="center" width="15%"></x-atoms.table.td>
                        </tr>
                    </x-slot>
                    {{-- @forelse($data_detail as $index=> $row)
                        <tr>
                            <x-atoms.table.td align="center">
                                {{$row['produk_kode_lokal']}}
                            </x-atoms.table.td>
                            <x-atoms.table.td>
                                {{$row['produk_nama']}}
                            </x-atoms.table.td>
                            <x-atoms.table.td align="center">
                                {{$row['jumlah']}}
                            </x-atoms.table.td>
                            <x-atoms.table.td align="center">
                                <x-atoms.button.btn-icon wire:click="editLine({{$index}})"><i class="la la-edit fs-3"></i></x-atoms.button.btn-icon>
                                <x-atoms.button.btn-icon wire:click="destroyLine({{$index}})"><i class="la la-trash fs-3"></i></x-atoms.button.btn-icon>
                            </x-atoms.table.td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Tidak Ada Data</td>
                        </tr>
                    @endforelse --}}
                </x-atoms.table>
            </x-molecules.card>
        </div>
        <!-- end:table cards-->

        <!-- begin:form cards-->
        <div class="flex-lg-auto min-w-lg-300px">
            <x-molecules.card>
                <x-atoms.input.group label="Produk" name="produk_nama">
                    <x-atoms.input.textarea
                        data-bs-toggle="modal"
                        data-bs-target="#modal_produk"
                        wire:model.defer="produk_nama"
                        readonly>
                    </x-atoms.input.textarea>
                </x-atoms.input.group>
                <x-atoms.input.group label="Jumlah" name="jumlah">
                    <x-atoms.input.text class="text-end" wire:model.defer="jumlah" />
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

    <!-- begin:modal_produk -->
    <x-molecules.modal title="Daftar Produk" id="modal_produk" size="xl" wire:ignore.self>
        <livewire:datatables.produk-set-table />
        <x-slot name="footer"></x-slot>
    </x-molecules.modal>
    <!-- end:modal_produk -->


    @push('custom-scripts')
        <script>
            let produkModal = new bootstrap.Modal(document.getElementById('modal_produk'));

            Livewire.on('set_produk', function (){
                produkModal.hide();
            })

            $('#tgl_nota').on('change', function (e) {
                let date = $(this).data("#tgl_nota");
                // eval(date).set('tglLahir', $('#tglLahir').val())
                console.log(e.target.value);
                @this.tgl_nota = e.target.value;
            })
        </script>
    @endpush
</div>
