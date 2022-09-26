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
                <x-atoms.table>
                    <x-slot:head>
                        <tr>
                            <th>ID</th>
                            <th>Jenis</th>
                            <th>Dibayar</th>
                            <th>Kurang Bayar</th>
                            <th></th>
                        </tr>
                    </x-slot:head>
                    @forelse($dataDetail as $index => $detail)
                        <tr>
                            @php
                                $selisih_kurang_bayar = $detail['kurang_bayar'] - $detail['nominal_dibayar'];
                            @endphp
                            <x-atoms.table.td>{{$detail['kode_pembelian']}}</x-atoms.table.td>
                            <x-atoms.table.td>{{$detail['jenis_pembelian']}} {{$detail['pembelian']}}</x-atoms.table.td>
                            <x-atoms.table.td>{{rupiah_format($detail['nominal_dibayar'])}}</x-atoms.table.td>
                            <x-atoms.table.td>{{rupiah_format($selisih_kurang_bayar)}}</x-atoms.table.td>
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
                    <x-atoms.input.group-horizontal label="Supplier">
                        <x-atoms.input.plaintext>{{$supplier_nama}}</x-atoms.input.plaintext>
                    </x-atoms.input.group-horizontal>
                </div>
                <div class="mb-2">
                    <x-atoms.input.group-horizontal label="Saldo Hutang">
                        <x-atoms.input.plaintext>{{rupiah_format($supplier_saldo)}}</x-atoms.input.plaintext>
                    </x-atoms.input.group-horizontal>
                </div>
                <div class="mb-2">
                    <x-atoms.input.group-horizontal label="Dibayar">
                        <x-atoms.input.plaintext>{{rupiah_format($total_dibayar)}}</x-atoms.input.plaintext>
                    </x-atoms.input.group-horizontal>
                </div>
                <div class="mb-5">
                    <x-atoms.input.group-horizontal label="Keterangan" name="keterangan">
                        <x-atoms.input.textarea wire:model.defer="keterangan"/>
                    </x-atoms.input.group-horizontal>
                </div>
                <div class="row mb-5">
                    <div class="col-6 text-center">
                        <x-atoms.button.btn-primary data-bs-toggle="modal" data-bs-target="#modalDaftarSupplier">Add Supplier</x-atoms.button.btn-primary>
                    </div>
                    <div class="col-6 text-center">
                        <x-atoms.button.btn-primary color="info" wire:click.prevent="addHutang">Add Hutang</x-atoms.button.btn-primary>
                    </div>
                </div>
                <x-atoms.button.btn-primary class="w-100" wire:click.prevent="openPayment">SIMPAN</x-atoms.button.btn-primary>
            </x-molecules.card>
        </div>
    </div>

    <x-organisms.modals.daftar-supplier />

    <x-organisms.modals.daftar-hutang-pembelian />

    <livewire:purchase.pembelian-detail-view />

    <livewire:purchase.pembelian-retur-detail-view />

    <x-molecules.modal id="modalFormHutangPembelian" title="Form Hutang Pembelian" size="xl" wire:ignore.self>
        <div class="mb-5">
            <x-atoms.input.group-horizontal label="ID">
                <x-atoms.input.plaintext>{{$kode_pembelian}}</x-atoms.input.plaintext>
            </x-atoms.input.group-horizontal>
        </div>
        <div class="mb-5">
            <x-atoms.input.group-horizontal label="Kurang Bayar">
                <x-atoms.input.plaintext>{{rupiah_format($kurang_bayar)}}</x-atoms.input.plaintext>
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

    <x-molecules.modal title="Payment" size="xl" id="modalPayment">
        <table>
            @foreach($dataPayment as $index => $row)
                <tr>
                    <td>
                        <x-atoms.input.group label="Akun Kas" name="dataPayment.{{$index}}.akun_id" required="required">
                            <x-atoms.input.select>
                                <x-molecules.select.akun-kas-list wire:model.defer="dataPayment.{{$index}}.akun_id" />
                            </x-atoms.input.select>
                        </x-atoms.input.group>
                    </td>
                    <td>
                        <x-atoms.input.group label="Nominal" name="dataPayment.{{$index}}.nominal" required="required">
                            <x-atoms.input.text wire:model.defer="dataPayment.{{$index}}.nominal" />
                        </x-atoms.input.group>
                    </td>
                    <td>
                        <x-atoms.button.btn-info>delete</x-atoms.button.btn-info>
                    </td>
                </tr>
            @endforeach
        </table>
    </x-molecules.modal>

    @push('custom-scripts')
        <script>
            // initiate variable
            let modalFormHutangPembelian = document.getElementById('modalFormHutangPembelian');
            let modalFormHutangPembelianInstance = new bootstrap.Modal(modalFormHutangPembelian);
            // listen event piutang close
            Livewire.on('showFormHutangPembelian', function (){
                modalDaftarHutangPembelianInstance.hide();
                modalFormHutangPembelianInstance.show();
            })

            Livewire.on('showPembelianDetail', function (){
                modalDaftarHutangPembelianInstance.hide();
            })

            Livewire.on('hideFormHutangPembelian', function (){
                modalFormHutangPembelianInstance.hide();
            })

            modalPembelianReturDetail.addEventListener('hidden.bs.modal', function (event) {
                modalDaftarHutangPembelianInstance.show()
            })

            modal_penjualan_detail.addEventListener('hidden.bs.modal', function (event) {
                modalDaftarHutangPembelianInstance.show();
            })

            let modalPayment = document.getElementById('modalPayment');
            let modalPaymenInstance = new bootstrap.Modal(modalPayment);

            Livewire.on('showPayment', function (){
                modalPaymenInstance.show();
            })

        </script>
    @endpush
</div>
