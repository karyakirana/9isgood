<div>
    @if(session()->has('message'))
        <x-molecules.alert-danger>
            <span>{{session('message')}}</span>
        </x-molecules.alert-danger>
    @endif

    <x-molecules.card title="Form Piutang Retur Penjualan">
        <div class="row mb-5">
            <div class="col-6">
                <x-atoms.input.group-horizontal data-bs-toggle="modal" data-bs-target="#customer_modal" label="Customer" name="customer_id">
                    <x-atoms.input.text wire:model.defer="customer_nama"/>
                </x-atoms.input.group-horizontal>
            </div>
            <div class="col-6">
                <x-atoms.input.group-horizontal label="Tgl Jurnal" name="tgl_jurnal">
                    <x-atoms.input.singledaterange wire:model.defer="tgl_jurnal" id="tglJurnal"/>
                </x-atoms.input.group-horizontal>
            </div>
        </div>
        <div class="row mb-5">
            <div class="col-6">
                <x-atoms.input.group-horizontal label="Keterangan" name="keterangan">
                    <x-atoms.input.text wire:model.defer="keterangan" />
                </x-atoms.input.group-horizontal>
            </div>
            <div class="col-6">
                <x-atoms.button.btn-modal target="#penjualan_modal">Add Nota</x-atoms.button.btn-modal>
                @if($mode == 'update')
                    <x-atoms.button.btn-danger wire:click="update">Update</x-atoms.button.btn-danger>
                @else
                    <x-atoms.button.btn-danger wire:click="store">Simpan</x-atoms.button.btn-danger>
                @endif
            </div>
        </div>
    </x-molecules.card>
</div>
