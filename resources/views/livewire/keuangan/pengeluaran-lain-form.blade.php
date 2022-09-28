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
                            <x-atoms.button.btn-primary class="w-100" wire:click.prevent="updateLine">Update Akun</x-atoms.button.btn-primary>
                        @else
                            <x-atoms.button.btn-primary class="w-100" wire:click.prevent="addLine">Add Akun</x-atoms.button.btn-primary>
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
                            <x-atoms.input.group-horizontal label="Tanggal" name="tgl_pengeluaran">
                                <x-atoms.input.singledaterange id="tgl_pengeluaran" />
                            </x-atoms.input.group-horizontal>
                        </div>
                        <div class="col-6">
                            <x-atoms.input.group-horizontal label="Pihak 3" name="person_relation_id">
                                <x-atoms.input.text wire:model.defer="person_relation_nama" wire:click.prevent="$emit('showModalPerson')" readonly/>
                            </x-atoms.input.group-horizontal>
                        </div>
                    </div>
                    <div class="row mb-5">
                        <div class="col-6">
                            <x-atoms.input.group-horizontal label="Diberikan Ke" name="asal">
                                <x-atoms.input.text wire:model.defer="tujuan"/>
                            </x-atoms.input.group-horizontal>
                        </div>
                        <div class="col-6">
                            <x-atoms.input.group-horizontal label="Keterangan" name="keterangan">
                                <x-atoms.input.text wire:model.defer="keterangan" />
                            </x-atoms.input.group-horizontal>
                        </div>
                    </div>
                    <x-atoms.table>
                        <tr>
                            <th>Kode</th>
                            <th>Akun</th>
                            <th>Nominal</th>
                            <th></th>
                        </tr>
                        @forelse($dataDetail as $indexDetail => $row)
                            <tr>
                                <x-atoms.table.td>{{$row['akun_kode']}}</x-atoms.table.td>
                                <x-atoms.table.td>{{$row['akun_nama']}}</x-atoms.table.td>
                                <x-atoms.table.td>{{rupiah_format($row['nominal'])}}</x-atoms.table.td>
                                <x-atoms.table.td>
                                    <x-atoms.button.btn-icon wire:click.prevent="editLine({{$indexDetail}})"><i class="fa fa-edit"></i></x-atoms.button.btn-icon>
                                    <x-atoms.button.btn-icon wire:click.prevent="destroyLine({{$indexDetail}})"><i class="fa fa-trash"></i></x-atoms.button.btn-icon>
                                </x-atoms.table.td>
                            </tr>
                        @empty
                            <tr>
                                <x-atoms.table.td colspan="4" align="center">Data Tidak Ada</x-atoms.table.td>
                            </tr>
                        @endforelse
                    </x-atoms.table>
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
            <x-slot:footer>
                @if($mode=='create')
                    <x-atoms.button.btn-primary wire:click.prevent="store">SIMPAN</x-atoms.button.btn-primary>
                @else
                    <x-atoms.button.btn-primary wire:click.prevent="update">UPDATE</x-atoms.button.btn-primary>
                @endif
            </x-slot:footer>
        </x-molecules.modal>

        @push('custom-scripts')
            <script>
                let modalPayment = document.getElementById('modalPayment');
                let modalPaymenInstance = new bootstrap.Modal(modalPayment);

                Livewire.on('showPayment', function (){
                    modalPaymenInstance.show()
                })

                $('#tgl_pengeluaran').on('change', function (e) {
                    let date = $(this).data("#tgl_pengeluaran");
                    // eval(date).set('tglLahir', $('#tgl_penerimaan').val())
                    console.log(e.target.value);
                    @this.tgl_pengeluaran = e.target.value;
                })
            </script>
        @endpush
</div>
