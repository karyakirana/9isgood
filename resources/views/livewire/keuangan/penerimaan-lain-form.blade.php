<div>
    <div class="row">
        <div class="col-4">
            <x-molecules.card>
                <div class="mb-5">
                    <x-atoms.input.group label="Akun" name="akun_id">
                        <x-atoms.input.text wire:model.defer="akun_nama" wire:click.prevent="$emit('showModalAkun')" readonly/>
                    </x-atoms.input.group>
                </div>
                <div class="mb-5">
                    <x-atoms.input.group label="Nominal" name="nominal_detail">
                        <x-atoms.input.text wire:model.defer="nominal_detail" />
                    </x-atoms.input.group>
                </div>
                <div class="mb-5">
                    @if($update)
                        <x-atoms.button.btn-primary class="w-100" wire:click.prevent="addLine">Update Akun</x-atoms.button.btn-primary>
                    @else
                        <x-atoms.button.btn-primary class="w-100" wire:click.prevent="updateLine">Add Akun</x-atoms.button.btn-primary>
                    @endif
                </div>
                <div class="mb-5">
                    <x-atoms.button.btn-primary class="w-100" color="success" wire:click.prevent="payment">Payment</x-atoms.button.btn-primary>
                </div>
            </x-molecules.card>
        </div>
        <div class="col-8">
            <x-molecules.card>
                <div class="row mb-5">
                    <div class="col-6">
                        <x-atoms.input.group-horizontal label="Tanggal" name="tgl_penerimaan">
                            <x-atoms.input.singledaterange id="tgl_penerimaan" />
                        </x-atoms.input.group-horizontal>
                    </div>
                    <div class="col-6">
                        <x-atoms.input.group-horizontal label="Pihak 3">
                            <x-atoms.input.text wire:model.defer="person_relation_nama" wire:click.prevent="$emit('showModalPerson')" readonly/>
                        </x-atoms.input.group-horizontal>
                    </div>
                </div>
                <div class="row mb-5">
                    <div class="col-6">
                        <x-atoms.input.group-horizontal label="Diterima Dari" name="asal">
                            <x-atoms.input.text wire:model.defer="asal"/>
                        </x-atoms.input.group-horizontal>
                    </div>
                    <div class="col-6">
                        <x-atoms.input.group-horizontal label="Keterangan" name="keterangan">
                            <x-atoms.input.text wire:model.defer="keterangan" />
                        </x-atoms.input.group-horizontal>
                    </div>
                </div>
            </x-molecules.card>
        </div>
    </div>
    <x-organisms.modals.daftar-akun />
    <x-organisms.modals.daftar-person-relation />
    <x-molecules.modal title="Payment" id="modalPayment" size="xl" wire:ignore.self>
        <x-atoms.input.group-horizontal label="Total Dibayar">
            <x-atoms.input.plaintext><span class="fw-bolder">{{rupiah_format($nominal)}}</span></x-atoms.input.plaintext>
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
    </x-molecules.modal>

    @push('custom-scripts')
        <script>
            let modalPayment = document.getElementById('modalPayment');
            let modalPaymenInstance = new bootstrap.Modal(modalPayment);

            Livewire.on('showPayment', function (){
                modalPaymenInstance.show()
            })
        </script>
    @endpush
</div>
