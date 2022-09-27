<div>
    @if(session()->has('message'))
        <x-molecules.alert-danger>
            {{session('message')}}
        </x-molecules.alert-danger>
    @endif
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
                <x-atoms.input.group-horizontal label="Tanggal" class="mb-5" name="tgl_transaksi">
                    <x-atoms.input.singledaterange id="tgl_transaksi"/>
                </x-atoms.input.group-horizontal>
                <x-atoms.input.group-horizontal label="Jenis" class="mb-5" name="jenis_piutang">
                    <x-atoms.input.select wire:model.defer="jenis_piutang">
                        <option>Dipilih</option>
                        <option value="penerimaan">Penerimaan</option>
                        <option value="pengeluaran">Pengeluaran</option>
                    </x-atoms.input.select>
                </x-atoms.input.group-horizontal>
                <x-atoms.input.group-horizontal label="Nominal" class="mb-5" name="nominal">
                    <x-atoms.input.text wire:model="nominal" />
                </x-atoms.input.group-horizontal>
                <x-atoms.input.group-horizontal label="Keterangan" class="mb-5" name="keterangan">
                    <x-atoms.input.textarea wire:model.defer="keterangan"></x-atoms.input.textarea>
                </x-atoms.input.group-horizontal>
                <x-slot:footer>
                    <x-atoms.button.btn-primary wire:click.prevent="openPayment">Payment</x-atoms.button.btn-primary>
                </x-slot:footer>
            </x-molecules.card>
        </div>
        <div class="col-4">
            <x-molecules.card>
                <div class="mb-5">
                    <x-atoms.input.group-horizontal label="Pegawai">
                        <x-atoms.input.plaintext>{{$pegawai_nama}}</x-atoms.input.plaintext>
                    </x-atoms.input.group-horizontal>
                </div>
                <div class="mb-5">
                    <x-atoms.input.group-horizontal label="Saldo Hutang">
                        <x-atoms.input.plaintext>{{$pegawai_saldo}}</x-atoms.input.plaintext>
                    </x-atoms.input.group-horizontal>
                </div>
                <div class="mb-5">
                    <x-atoms.button.btn-primary wire:click="$emit('showModalDaftarPegawai')">Set Pegawai</x-atoms.button.btn-primary>
                </div>
            </x-molecules.card>
        </div>
    </div>
    <x-organisms.modals.daftar-pegawai />

    <x-molecules.modal title="Payment" size="xl" id="modalPayment" wire:ignore.self>
        <x-atoms.table>
            <x-atoms.input.group-horizontal label="Total Tagihan">
                <x-atoms.input.plaintext><span class="fw-bold">{{rupiah_format($nominal)}}</span></x-atoms.input.plaintext>
            </x-atoms.input.group-horizontal>
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
            $('#tgl_transaksi').on('change', function (e) {
                let date = $(this).data("#tgl_transaksi");
                // eval(date).set('tglLahir', $('#tglLahir').val())
                console.log(e.target.value);
                @this.tgl_transaksi = e.target.value;
            })

            let modalPayment = document.getElementById('modalPayment');
            let modalPaymenInstance = new bootstrap.Modal(modalPayment);

            Livewire.on('showPayment', function (){
                modalPaymenInstance.show();
            })
        </script>
    @endpush
</div>
