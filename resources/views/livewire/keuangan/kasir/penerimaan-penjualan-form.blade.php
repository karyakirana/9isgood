<div>
    <div class="row mb-5">
        <div class="col-6">
            <x-molecules.card></x-molecules.card>
        </div>
        <div class="col-6">
            <x-molecules.card></x-molecules.card>
        </div>
    </div>
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
                                    <x-molecules.select.akun-kas-list />
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
                                    <x-molecules.select.akun-piutang-list />
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
                            <th>Id</th>
                            <th>Total Penjualan</th>
                            <th>Biaya</th>
                            <th>PPN</th>
                            <th>Total Bayar</th>
                            <th style="width: 15%"></th>
                        </tr>
                    </x-slot>
                    @forelse($detail as $index=>$item)
                        <tr class="align-middle">
                            <x-atoms.table.td>{{$item['penjualan_kode']}}</x-atoms.table.td>
                            <x-atoms.table.td align="end">{{$item['total_penjualan']}}</x-atoms.table.td>
                            <x-atoms.table.td align="end">{{$item['biaya_lain']}}</x-atoms.table.td>
                            <x-atoms.table.td align="end">{{$item['ppn']}}</x-atoms.table.td>
                            <x-atoms.table.td align="end">{{$item['total_bayar']}}</x-atoms.table.td>
                            <x-atoms.table.td>
                                <button type="button" class="btn btn-flush btn-active-color-info" wire:click="editLine({{$index}})"><i class="la la-edit fs-2"></i></button>
                                <button type="button" class="btn btn-flush btn-active-color-info" wire:click="destroyLine({{$index}})"><i class="la la-trash fs-2"></i></button>
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
                        <x-atoms.input.group-horizontal label="ID">
                            <x-atoms.input.text name="penjualan_id" wire:model.defer="penjualan_kode"/>
                        </x-atoms.input.group-horizontal>
                    </div>
                    <div class="mb-5">
                        <x-atoms.input.group-horizontal label="Tipe">
                            <x-atoms.input.text name="penjualan_type" wire:model.defer="penjualan_type"/>
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
                                <x-molecules.select.akun-biaya-usaha-list />
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
                                <x-molecules.select.akun-ppn-list />
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
                        <x-atoms.button.btn-modal color="info" target="#modalDaftarPenjualan">Penjualan</x-atoms.button.btn-modal>
                        <x-atoms.button.btn-modal color="info" target="#modalDaftarPenjualanRetur">Retur</x-atoms.button.btn-modal>

                    </div>
                    <div class="text-center pb-4 pt-5">
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

    <x-organisms.modals.daftar-penjualan />

    <x-organisms.modals.daftar-penjualan-retur />

    <x-organisms.modals.daftar-customer />

    <livewire:penjualan.penjualan-detail-view />

    <livewire:penjualan.penjualan-retur-detail-view />

    @push('custom-scripts')
        <script>

            $('#tgl_jurnal').on('change', function (e) {
                let date = $(this).data("#tgl_jurnal");
                // eval(date).set('tglLahir', $('#tglLahir').val())
                console.log(e.target.value);
                @this.tgl_jurnal = e.target.value;
            })

        </script>
    @endpush

</div>
