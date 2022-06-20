<div>
    <x-molecules.card title="Form Penerimaan Penjualan">
        <x-slot name="toolbar">
            @if($saldo_piutang)
                Saldo Piutang {{rupiah_format($saldo_piutang)}}
            @endif
        </x-slot>
        <div class="row">
            <div class="col-8">
                <form>
                    <div class="row mb-5">
                        <div class="col-6">
                            <x-atoms.input.group label="Tanggal" required="required">
                                <x-atoms.input.singledaterange />
                            </x-atoms.input.group>
                        </div>
                        <div class="col-6">
                            <x-atoms.input.group label="Customer" required="required">
                                <x-atoms.input.text name="customer_id" wire:model.defer="customer_nama" data-bs-toggle="modal" data-bs-target="#customer_modal" readonly/>
                            </x-atoms.input.group>
                        </div>
                    </div>
                    <div class="row mb-5">
                        <div class="col-6">
                            <x-atoms.input.group label="Akun Kas" required="required">
                                <x-atoms.input.select>
                                    <option>Dipilih</option>
                                    @foreach($akun_kas_data as $item)
                                        <option value="{{$item->id}}">{{$item->deskripsi}}</option>
                                    @endforeach
                                </x-atoms.input.select>
                            </x-atoms.input.group>
                        </div>
                        <div class="col-6">
                            <x-atoms.input.group label="Nominal Kas" required="required">
                                <x-atoms.input.text id="tgl_jurnal"/>
                            </x-atoms.input.group>
                        </div>
                    </div>
                    <div class="row mb-5">
                        <div class="col-6">
                            <x-atoms.input.group label="Akun Piutang" required="required">
                                <x-atoms.input.select>
                                    <option>Dipilih</option>
                                    @foreach($akun_piutang_data as $item)
                                        <option value="{{$item->id}}">{{$item->deskripsi}}</option>
                                    @endforeach
                                </x-atoms.input.select>
                            </x-atoms.input.group>
                        </div>
                        <div class="col-6">
                            <x-atoms.input.group label="Nominal Piutang" required="required">
                                <x-atoms.input.text />
                            </x-atoms.input.group>
                        </div>
                    </div>
                </form>

                <x-atoms.table>
                    <x-slot name="head">
                        <tr>
                            <th></th>
                            <th>Id</th>
                            <th>Total Penjualan</th>
                            <th>Biaya</th>
                            <th>PPN</th>
                            <th>Total Bayar</th>
                            <th></th>
                        </tr>
                    </x-slot>
                    @forelse($detail as $index=>$item)
                        <tr class="align-middle">
                            <x-atoms.table.td>{{$index}}</x-atoms.table.td>
                            <x-atoms.table.td>{{$item->penjualan_kode}}</x-atoms.table.td>
                            <x-atoms.table.td>{{$item->biaya}}</x-atoms.table.td>
                            <x-atoms.table.td>{{$item->ppn}}</x-atoms.table.td>
                            <x-atoms.table.td>{{$item->total_bayar}}</x-atoms.table.td>
                            <x-atoms.table.td>
                                <button type="button" class="btn btn-flush btn-active-color-info btn-icon" wire:click="editLine({{$index}})"><i class="la la-edit fs-2"></i></button>
                                <button type="button" class="btn btn-flush btn-active-color-info btn-icon" wire:click="destroyLine({{$index}})"><i class="la la-trash fs-2"></i></button>
                            </x-atoms.table.td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak Ada Data</td>
                        </tr>
                    @endforelse
                </x-atoms.table>
            </div>
            <div class="col-4">
                <form class="mt-5 p-5 border">
                    <div class="mb-5">
                        <x-atoms.input.group-horizontal label="Penjualan ID">
                            <x-atoms.input.text name="penjualan_id" wire:model.defer="kode_penjualan"/>
                        </x-atoms.input.group-horizontal>
                    </div>
                    <div class="mb-5">
                        <x-atoms.input.group-horizontal label="Total Penjualan">
                            <x-atoms.input.text wire:model.defer="total_penjualan_rupiah" readonly/>
                        </x-atoms.input.group-horizontal>
                    </div>
                    <div class="mb-5">
                        <x-atoms.input.group-horizontal label="Akun Biaya">
                            <x-atoms.input.select>
                                <option>Dipilih</option>
                                @foreach($akun_biaya_data as $item)
                                    <option value="{{$item->id}}">{{$item->deskripsi}}</option>
                                @endforeach
                            </x-atoms.input.select>
                        </x-atoms.input.group-horizontal>
                    </div>
                    <div class="mb-5">
                        <x-atoms.input.group-horizontal label="Biaya Lain">
                            <x-atoms.input.text name="biaya_lain" wire:model.defer="biaya_lain" readonly/>
                        </x-atoms.input.group-horizontal>
                    </div>
                    <div class="mb-5">
                        <x-atoms.input.group-horizontal label="Akun PPN">
                            <x-atoms.input.select>
                                <option>Dipilih</option>
                                @foreach($akun_ppn_data as $item)
                                    <option value="{{$item->id}}">{{$item->deskripsi}}</option>
                                @endforeach
                            </x-atoms.input.select>
                        </x-atoms.input.group-horizontal>
                    </div>
                    <div class="mb-5">
                        <x-atoms.input.group-horizontal label="PPN">
                            <x-atoms.input.text name="ppn" wire:model.defer="ppn" readonly/>
                        </x-atoms.input.group-horizontal>
                    </div>
                    <div class="mb-5">
                        <x-atoms.input.group-horizontal label="Total Bayar">
                            <x-atoms.input.text name="total_bayar" wire:model.defer="total_bayar_rupiah" readonly="" />
                        </x-atoms.input.group-horizontal>
                    </div>
                    <div class="text-center pb-4 pt-5">
                        <x-atoms.button.btn-modal color="info" target="#modalDaftarPenjualan">Add Penjualan</x-atoms.button.btn-modal>
                        @if($update)
                            <button type="button" class="btn btn-primary" wire:click="updateLine">update Data</button>
                        @else
                            <button type="button" class="btn btn-primary" wire:click="addLine">Save Data</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </x-molecules.card>

    <x-molecules.modal size="xl" title="Daftar Penjualan" id="modalDaftarPenjualan" wire:ignore.self>
        <livewire:datatables.penjualan-set-table />
        <x-slot name="footer"></x-slot>
    </x-molecules.modal>

    <x-molecules.modal title="Daftar Customer" id="customer_modal" size="xl" wire:ignore.self>
        <livewire:datatables.customer-set-table />
        <x-slot name="footer"></x-slot>
    </x-molecules.modal>

    <livewire:penjualan.penjualan-detail-view />

    @push('custom-scripts')
        <script>
            let customer_modal = document.getElementById('customer_modal');
            let customerModal = new bootstrap.Modal(customer_modal);

            Livewire.on('set_customer', function (){
                customerModal.hide();
            })

            let penjualan_modal = document.getElementById('modalDaftarPenjualan');
            let penjualanModal = new bootstrap.Modal(penjualan_modal);

            Livewire.on('setPenjualan', function (){
                penjualanModal.hide();
            })

            $('#tgl_jurnal').on('change', function (e) {
                let date = $(this).data("#tgl_jurnal");
                // eval(date).set('tglLahir', $('#tglLahir').val())
                console.log(e.target.value);
                @this.tgl_jurnal = e.target.value;
            })

        </script>
    @endpush

</div>
