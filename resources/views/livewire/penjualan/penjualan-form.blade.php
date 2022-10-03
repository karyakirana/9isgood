<div>
    <div>
        {{-- alert store --}}
        @if(session()->has('storeMessage'))
            <x-molecules.alert-danger>
                {{ session('storeMessage') }}
            </x-molecules.alert-danger>
        @endif
        {{-- alert validation --}}
        @if($errors->all())
            <x-molecules.alert-danger>
                <ul>
                    @foreach($errors->all() as $messages)
                        <li>{{$messages}}</li>
                    @endforeach
                </ul>
            </x-molecules.alert-danger>
        @endif
        <x-molecules.card title="Form Penjualan">
            <div class="row">
                <div class="col-8">
                    <form>
                        <div class="row mb-6">
                            <div class="col-6">
                                <x-atoms.input.group-horizontal label="Customer" name="customer_nama" required="required">
                                    <x-atoms.input.text name="customerNama" wire:model.defer="customer_nama" data-bs-toggle="modal" data-bs-target="#customer_modal" readonly/>
                                </x-atoms.input.group-horizontal>
                            </div>
                            <div class="col-6">
                                <x-atoms.input.group-horizontal label="Jenis Bayar" name="jenis_bayar" required="required">
                                    <x-atoms.input.select wire:model.defer="jenis_bayar">
                                        <option>Dipilih</option>
                                        <option value="cash">Tunai</option>
                                        <option value="tempo">Tempo</option>
                                    </x-atoms.input.select>
                                </x-atoms.input.group-horizontal>
                            </div>
                        </div>

                        <div class="row mb-6">
                            <div class="col-6">
                                <x-atoms.input.group-horizontal label="Tgl Nota" name="tgl_nota" required="required">
                                    <div class="input-group">
                                        <x-atoms.input.singledaterange id="tgl_nota" wire:model.defer="tgl_nota"/>
                                    </div>
                                </x-atoms.input.group-horizontal>
                            </div>
                            <div class="col-6">
                                <x-atoms.input.group-horizontal label="Tgl Tempo" name="tgl_tempo" required="required">
                                    <div class="input-group">
                                        <x-atoms.input.singledaterange id="tgl_tempo" wire:model.defer="tgl_tempo"/>
                                    </div>
                                </x-atoms.input.group-horizontal>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-6">
                                <x-atoms.input.group-horizontal label="Gudang" name="gudang_id" required="required">
                                    <x-atoms.input.select wire:model.defer="gudang_id">
                                        <x-molecules.select.gudang-list />
                                    </x-atoms.input.select>
                                </x-atoms.input.group-horizontal>
                            </div>
                            <div class="col-6">
                                <x-atoms.input.group-horizontal label="Keterangan" name="keterangan">
                                    <x-atoms.input.text  wire:model.defer="keterangan"/>
                                </x-atoms.input.group-horizontal>
                            </div>
                        </div>
                    </form>
                    <x-organisms.form.penjualan-table-form :data-detail="$dataDetail" />

                </div>
                <x-organisms.form.penjualan-produk-form :update="$update" />
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

        <x-molecules.modal title="Daftar Customer" id="customer_modal" size="xl" >
            <livewire:datatables.customer-set-table />
            <x-slot name="footer"></x-slot>
        </x-molecules.modal>

        <x-molecules.modal title="Daftar Produk" id="produk_modal" size="xl" >
            <livewire:datatables.produk-set-table />
            <x-slot name="footer"></x-slot>
        </x-molecules.modal>

        @push('custom-scripts')
            <script>
                let modal_customer = document.getElementById('customer_modal');
                let customerModal = new bootstrap.Modal(modal_customer);

                Livewire.on('set_customer', function (){
                    customerModal.hide();
                })

                let modal_produk = document.getElementById('produk_modal');
                let produkModal = new bootstrap.Modal(modal_produk);

                Livewire.on('set_produk', function (){
                    produkModal.hide();
                })

                $('#tgl_nota').on('change', function (e) {
                    let date = $(this).data("#tgl_nota");
                    // eval(date).set('tglLahir', $('#tglLahir').val())
                    console.log(e.target.value);
                    @this.tgl_nota = e.target.value;
                })

                $('#tgl_tempo').on('change', function (e) {
                    let date = $(this).data("#tgl_tempo");
                    // eval(date).set('tglLahir', $('#tglLahir').val())
                    console.log(e.target.value);
                    @this.tgl_tempo = e.target.value;
                })
            </script>
        @endpush
    </div>

</div>
