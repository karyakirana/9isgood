<div>
    @if(session()->has('error jumlah'))
        <x-molecules.alert-danger>
            {{session('error jumlah')}}
        </x-molecules.alert-danger>
    @endif
    <x-molecules.card title="Stock Mutasi {{\Livewire\str($jenisMutasi)->headline()}}">
        <div class="row">
            <div class="col-9">
                <form id="master">
                    <div class="row mb-5">
                        <div class="col-6">
                            <x-atoms.input.group-horizontal label="Gudang Asal" name="gudangAsalId">
                                <x-atoms.input.select wire:model="gudangAsalId">
                                    <option>Dipilih</option>
                                    @foreach($gudangData as $row)
                                        <option value="{{$row->id}}">{{ucfirst($row->nama)}}</option>
                                    @endforeach
                                </x-atoms.input.select>
                            </x-atoms.input.group-horizontal>
                        </div>
                        <div class="col-6">
                            <x-atoms.input.group-horizontal label="Gudang Tujuan" name="gudangTujuanId">
                                <x-atoms.input.select wire:model.defer="gudangTujuanId">
                                    <option>Dipilih</option>
                                    @foreach($gudangData as $row)
                                        <option value="{{$row->id}}">{{ucfirst($row->nama)}}</option>
                                    @endforeach
                                </x-atoms.input.select>
                            </x-atoms.input.group-horizontal>
                        </div>
                    </div>
                    <div class="row mb-5">
                        <div class="col-6">
                            <x-atoms.input.group-horizontal label="Tanggal" name="keterangan">
                                <x-atoms.input.singledaterange id="tglMutasi" wire:model.defer="tglMutasi" />
                            </x-atoms.input.group-horizontal>
                        </div>
                        <div class="col-6">
                            <x-atoms.input.group-horizontal label="Keterangan" name="keterangan">
                                <x-atoms.input.textarea wire:model.defer="keterangan" />
                            </x-atoms.input.group-horizontal>
                        </div>
                    </div>
                </form>
                <x-atoms.table>
                    <x-slot name="head">
                        <tr>
                            <th class="text-center" width="15%">ID</th>
                            <th class="text-center" width="40%">Produk</th>
                            <th class="text-center" width="25%">Jumlah</th>
                            <th width="20%"></th>
                        </tr>
                    </x-slot>
                    @forelse($data_detail as $index=>$row)
                        <tr>
                            <x-atoms.table.td align="center">
                                {{$row['kode_lokal']}}
                            </x-atoms.table.td>
                            <x-atoms.table.td>
                                {{$row['produk_nama']}}
                            </x-atoms.table.td>
                            <x-atoms.table.td align="center">
                                {{$row['jumlah']}}
                            </x-atoms.table.td>
                            <x-atoms.table.td align="center">
                                <x-atoms.button.btn-icon wire:click="editLine({{$index}})"><i class="la la-edit fs-2"></i></x-atoms.button.btn-icon>
                                <x-atoms.button.btn-icon wire:click="destroyLine({{$index}})"><i class="la la-trash fs-2"></i></x-atoms.button.btn-icon>
                            </x-atoms.table.td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Tidak ada data</td>
                        </tr>
                    @endforelse
                </x-atoms.table>
            </div>
            <div class="col-3">
                <form id="detail" class="border p-5">
                    <div class="pt-5">
                        <x-atoms.input.group label="Produk" name="produk_id">
                            <x-atoms.input.textarea wire:model.defer="produk_screen"></x-atoms.input.textarea>
                        </x-atoms.input.group>
                    </div>
                    <div class="pt-5">
                        <x-atoms.input.group label="Jumlah" name="jumlah">
                            <x-atoms.input.text wire:model.defer="jumlah"/>
                        </x-atoms.input.group>
                    </div>
                    <div class="row pt-5 mb-5">
                        <!--begin::Col-->
                        <div class="col">
                            <x-atoms.button.btn-modal color="info" class="w-100" target="#produk_modal">Add</x-atoms.button.btn-modal>
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col">
                            @if($update)
                                <button type="button" class="btn btn-primary w-100" wire:click="updateLine">Update</button>
                            @else
                                <button type="button" class="btn btn-primary w-100" wire:click="addLine">Save</button>
                            @endif
                        </div>
                        <!--end::Col-->
                    </div>
                    @if($mode == 'update')
                        <x-atoms.button.btn-primary class="w-100" wire:click="update">Update All</x-atoms.button.btn-primary>
                    @else
                        <x-atoms.button.btn-primary class="w-100" wire:click="store">Save All</x-atoms.button.btn-primary>
                    @endif
                </form>
            </div>
        </div>
    </x-molecules.card>

    <x-molecules.modal title="Daftar Produk" id="produk_modal" size="xl" wire:ignore.self>
        <livewire:datatable.produk-from-stock-inventory />
        <x-slot name="footer"></x-slot>
    </x-molecules.modal>

    @push('custom-scripts')
        <script>
            let modal_produk = document.getElementById('produk_modal');
            let produkModal = new bootstrap.Modal(modal_produk);

            Livewire.on('setProduk', function (){
                produkModal.hide();
            })

            $('#tglMutasi').on('change', function (e) {
                let date = $(this).data("#tglMutasi");
                // eval(date).set('tglLahir', $('#tglLahir').val())
                console.log(e.target.value);
                @this.tglMutasi = e.target.value;
            })
        </script>
    @endpush
</div>
