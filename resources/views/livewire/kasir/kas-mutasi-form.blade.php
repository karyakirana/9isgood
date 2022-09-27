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
    @if(session()->has('messages'))
        <x-molecules.alert-danger>
            {{session('messages')}}
        </x-molecules.alert-danger>
    @endif
    <div class="row">
        <div class="col-4">
            <x-molecules.card>
                <x-atoms.input.group label="Jenis" class="mb-5" name="jenis">
                    <x-atoms.input.select wire:model.defer="jenis">
                        <option>Dipilih</option>
                        <option value="keluar">Keluar</option>
                        <option value="masuk">Masuk</option>
                    </x-atoms.input.select>
                </x-atoms.input.group>
                <x-atoms.input.group label="Akun Kas" class="mb-5" name="akun_kas_id">
                    <x-atoms.input.select wire:model.defer="akun_kas_id">
                        <x-molecules.select.akun-kas-list2 />
                    </x-atoms.input.select>
                </x-atoms.input.group>
                <x-atoms.input.group label="Nominal" class="mb-5" name="keterangan">
                    <x-atoms.input.text wire:model.defer="nominal" />
                </x-atoms.input.group>
                <div class="mb-5">
                    @if($update)
                        <x-atoms.button.btn-primary class="w-100" wire:click.prevent="updateLine">Update Kas</x-atoms.button.btn-primary>
                    @else
                        <x-atoms.button.btn-primary class="w-100" wire:click.prevent="addLine">Add Kas</x-atoms.button.btn-primary>
                    @endif
                </div>
                <div class="mb-5">
                    @if($mode == 'create')
                    <x-atoms.button.btn-primary color="success" class="w-100" wire:click.prevent="store">Store</x-atoms.button.btn-primary>
                    @else
                    <x-atoms.button.btn-primary color="success" class="w-100" wire:click.prevent="update">Update</x-atoms.button.btn-primary>
                    @endif
                </div>
            </x-molecules.card>
        </div>
        <div class="col-8">
            <x-molecules.card title="Form Mutasi Kas">
                <div class="row mb-5">
                    <div class="col-6">
                        <x-atoms.input.group-horizontal label="Tanggal" name="tgl_mutasi" >
                            <x-atoms.input.singledaterange id="tgl_mutasi" />
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
                            <th>Jenis</th>
                            <th>Akun</th>
                            <th>Nominal Masuk</th>
                            <th>Nominal Keluar</th>
                            <th></th>
                        </tr>
                    </x-slot:head>
                    @forelse($dataDetail as $index=> $row)
                        <tr>
                            <x-atoms.table.td>{{$row['jenis']}}</x-atoms.table.td>
                            <x-atoms.table.td>{{$row['akun_kas_nama']}}</x-atoms.table.td>
                            <x-atoms.table.td>{{$row['nominal_masuk']}}</x-atoms.table.td>
                            <x-atoms.table.td>{{$row['nominal_keluar']}}</x-atoms.table.td>
                            <x-atoms.table.td>
                                <x-atoms.button.btn-icon wire:click.prevent="editLine({{$index}})"><i class="fa fa-edit"></i></x-atoms.button.btn-icon>
                                <x-atoms.button.btn-icon wire:click.prevent="destroyLine({{$index}})"><i class="fa fa-trash"></i></x-atoms.button.btn-icon>
                            </x-atoms.table.td>
                        </tr>
                    @empty
                        <tr>
                            <x-atoms.table.td colspan="5" align="center">Tidak Ada Data</x-atoms.table.td>
                        </tr>
                    @endforelse
                </x-atoms.table>
            </x-molecules.card>
        </div>
    </div>
    @push('custom-scripts')
        <script>
            $('#tgl_mutasi').on('change', function (e) {
                let date = $(this).data("#tgl_mutasi");
                // eval(date).set('tglLahir', $('#tglLahir').val())
                console.log(e.target.value);
                @this.tgl_mutasi = e.target.value;
            })
        </script>
    @endpush
</div>
