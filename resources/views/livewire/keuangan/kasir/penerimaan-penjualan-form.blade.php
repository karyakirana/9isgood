<div>
    @if($errors->any())
        <x-molecules.alert-danger>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{$error}}</li>
                @endforeach
            </ul>
        </x-molecules.alert-danger>
    @endif
    <div class="row">
        <div class="col-8">
            <x-molecules.card>
                <div class="row mb-5">
                    <div class="col-6">
                        <x-atoms.input.group-horizontal label="Tanggal" name="tgl_penerimaan">
                            <x-atoms.input.singledaterange id="tgl_penerimaan" />
                        </x-atoms.input.group-horizontal>
                    </div>
                    <div class="col-6">
                        <x-atoms.input.group-horizontal label="Keterangan" name="keterangan">
                            <x-atoms.input.text wire:model.defer="keterangan" />
                        </x-atoms.input.group-horizontal>
                    </div>
                </div>
                <x-atoms.table>
                    <x-slot:head>
                        <tr>
                            <th>ID</th>
                            <th>Status</th>
                            <th>Jenis</th>
                            <th>Dibayar</th>
                            <th>Kurang Bayar</th>
                            <th></th>
                        </tr>
                    </x-slot:head>
                    @forelse($dataDetail as $index => $detail)
                        <tr>
                            <x-atoms.table.td>{{$detail['kode_penjualan']}}</x-atoms.table.td>
                            <x-atoms.table.td>{{$detail['status_bayar']}}</x-atoms.table.td>
                            <x-atoms.table.td>{{$detail['jenis_penjualan']}}</x-atoms.table.td>
                            <x-atoms.table.td>{{rupiah_format($detail['nominal_dibayar'])}}</x-atoms.table.td>
                            <x-atoms.table.td>{{rupiah_format($detail['kurang_bayar'])}}</x-atoms.table.td>
                            <x-atoms.table.td>
                                <x-atoms.button.btn-icon wire:click="editLine({{$index}})"><i class="fa fa-pen"></i></x-atoms.button.btn-icon>
                                <x-atoms.button.btn-icon wire:click="destroyLine({{$index}})"><i class="fa fa-trash"></i></x-atoms.button.btn-icon>
                            </x-atoms.table.td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Data Tidak Ada</td>
                        </tr>
                    @endforelse
                </x-atoms.table>

            </x-molecules.card>
        </div>
        <div class="col-4">
            <x-molecules.card>
                <div class="mb-2">
                    <x-atoms.input.group-horizontal label="Customer">
                        <x-atoms.input.plaintext>{{$customer_nama}}</x-atoms.input.plaintext>
                    </x-atoms.input.group-horizontal>
                </div>
                <div class="mb-2">
                    <x-atoms.input.group-horizontal label="Saldo Hutang">
                        <x-atoms.input.plaintext>{{rupiah_format($customer_saldo)}}</x-atoms.input.plaintext>
                    </x-atoms.input.group-horizontal>
                </div>
                <div class="mb-2">
                    <x-atoms.input.group-horizontal label="Dibayar">
                        <x-atoms.input.plaintext>{{rupiah_format($total_dibayar)}}</x-atoms.input.plaintext>
                    </x-atoms.input.group-horizontal>
                </div>
                <div class="row mb-5">
                    <div class="col-6 text-center">
                        <x-atoms.button.btn-primary data-bs-toggle="modal" data-bs-target="#customer_modal">Add Customer</x-atoms.button.btn-primary>
                    </div>
                    <div class="col-6 text-center">
                        <x-atoms.button.btn-primary color="info" wire:click.prevent="piutangPenjualanShow">Add Piutang</x-atoms.button.btn-primary>
                    </div>
                </div>
                <x-atoms.button.btn-primary class="w-100" wire:click.prevent="openPayment">PAYMENT</x-atoms.button.btn-primary>
            </x-molecules.card>
        </div>
    </div>

    <x-organisms.modals.daftar-customer />

    <x-organisms.modals.daftar-piutang-penjualan />

    <livewire:penjualan.penjualan-detail-view />

    <livewire:penjualan.penjualan-retur-detail-view />

    <x-molecules.modal id="modalFormPiutangPenjualan" title="Form Piutang Penjualan" size="xl" wire:ignore.self>
        <div class="mb-5">
            <x-atoms.input.group-horizontal label="ID">
                <x-atoms.input.plaintext>{{$kode_penjualan}}</x-atoms.input.plaintext>
            </x-atoms.input.group-horizontal>
        </div>
        <div class="mb-5">
            <x-atoms.input.group-horizontal label="Kurang Bayar">
                <x-atoms.input.plaintext>{{rupiah_format($kurang_bayar_sebelumnya)}}</x-atoms.input.plaintext>
            </x-atoms.input.group-horizontal>
        </div>
        <div class="mb-5">
            <x-atoms.input.group-horizontal label="Nominal Bayar" name="nominal_dibayar">
                <x-atoms.input.text wire:model.defer="nominal_dibayar" />
            </x-atoms.input.group-horizontal>
        </div>
        <div class="text-center pb-4 pt-5">
            <x-atoms.button.btn-modal color="danger" data-bs-dismiss="modal">Cancel</x-atoms.button.btn-modal>
            @if($update)
                <button type="button" class="btn btn-primary" wire:click="updateLine">update Data</button>
            @else
                <button type="button" class="btn btn-primary" wire:click="addLine">Save Data</button>
            @endif

        </div>
    </x-molecules.modal>

    <x-molecules.modal title="Payment" size="xl" id="modalPayment" wire:ignore.self>
        <x-atoms.input.group-horizontal label="Total Dibayar">
            <x-atoms.input.plaintext><span class="fw-bolder">{{rupiah_format($total_penerimaan)}}</span></x-atoms.input.plaintext>
        </x-atoms.input.group-horizontal>
        <x-atoms.table>
            @foreach($dataPayment as $index => $row)
                <tr class="align-middle">
                    <x-atoms.table.td>
                        <x-atoms.input.group-horizontal label="Akun Kas" name="dataPayment.{{$index}}.akun_id" required="required">
                            <x-atoms.input.select wire:model="dataPayment.{{$index}}.akun_id">
                                <x-molecules.select.akun-kas-list2 />
                            </x-atoms.input.select>
                        </x-atoms.input.group-horizontal>
                    </x-atoms.table.td>
                    <x-atoms.table.td>
                        <x-atoms.input.group-horizontal label="Nominal" name="dataPayment.{{$index}}.nominal" required="required">
                            <x-atoms.input.text wire:model.defer="dataPayment.{{$index}}.nominal" />
                        </x-atoms.input.group-horizontal>
                    </x-atoms.table.td>
                    <x-atoms.table.td>
                        <x-atoms.button.btn-info wire:click.prevent="deletePayment({{$index}})">delete</x-atoms.button.btn-info>
                        <x-atoms.button.btn-primary wire:click.prevent="addPayment({{$index}})">add</x-atoms.button.btn-primary>
                    </x-atoms.table.td>
                </tr>
            @endforeach
        </x-atoms.table>
        <x-slot:footer>
            @if($mode == 'create')
                <x-atoms.button.btn-primary wire:click.prevent="store">Simpan Semua</x-atoms.button.btn-primary>
            @else
                <x-atoms.button.btn-primary wire:click.prevent="update">Update Semua</x-atoms.button.btn-primary>
            @endif
        </x-slot:footer>
    </x-molecules.modal>

    @push('custom-scripts')
        <script>
            // initiate variable
            let modalFormPiutangPenjualan = document.getElementById('modalFormPiutangPenjualan');
            let modalFormPiutangPenjualanInstance = new bootstrap.Modal(modalFormPiutangPenjualan);
            // listen event piutang close
            Livewire.on('showFormPiutangPenjualan', function (){
                piutangPenjualanModal.hide();
                modalFormPiutangPenjualanInstance.show();
            })

            Livewire.on('hideFormPiutangPenjualan', function (){
                modalFormPiutangPenjualanInstance.hide();
            })

            Livewire.on('showPenjualanDetail', function (){
                modalFormPiutangPenjualanInstance.hide();
            })

            modal_penjualan_retur_detail.addEventListener('hidden.bs.modal', function (event) {
                modalFormPiutangPenjualanInstance.show()
            })

            modal_penjualan_detail.addEventListener('hidden.bs.modal', function (event) {
                modalFormPiutangPenjualanInstance.show();
            })

            let modalPayment = document.getElementById('modalPayment');
            let modalPaymenInstance = new bootstrap.Modal(modalPayment);

            Livewire.on('showPayment', function (){
                modalPaymenInstance.show();
            })

            $('#tgl_penerimaan').on('change', function (e) {
                let date = $(this).data("#tgl_penerimaan");
                // eval(date).set('tglLahir', $('#tglLahir').val())
                console.log(e.target.value);
                @this.tgl_penerimaan = e.target.value;
            })

        </script>
    @endpush
</div>
