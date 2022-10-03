<div class="col-4 border">
    <form >
        <div class="pb-5 pt-5">
            <x-atoms.input.group-horizontal name="produk_nama" label="Produk">
                <x-atoms.input.textarea  wire:model.defer="produk_nama"/>
            </x-atoms.input.group-horizontal>
        </div>
        <div class="pt-5">
            <x-atoms.input.group-horizontal name="harga_rupiah" label="Harga">
                <x-atoms.input.text wire:model.defer="harga_rupiah" class="text-end" readonly=""/>
            </x-atoms.input.group-horizontal>
        </div>
        <div class="pt-5">
            <x-atoms.input.group-horizontal name="diskon" label="Diskon">
                <div class="input-group">
                    <x-atoms.input.text wire:model="diskon"/>
                    <span class="input-group-text">%</span>
                </div>
            </x-atoms.input.group-horizontal>
        </div>
        <div class="pt-5">
            <x-atoms.input.group-horizontal name="harga_setelah_diskon" label="">
                <div class="input-group">
                    <span class="input-group-text">Rp. </span>
                    <x-atoms.input.text wire:model.defer="harga_setelah_diskon" readonly=""/>
                </div>
            </x-atoms.input.group-horizontal>
        </div>
        <div class="pt-5">
            <x-atoms.input.group-horizontal name="jumlah" label="Jumlah">
                <x-atoms.input.text wire:model="jumlah"/>
            </x-atoms.input.group-horizontal>
        </div>
        <div class="pt-5">
            <x-atoms.input.group-horizontal name="sub_total_rupiah" label="Sub Total">
                <x-atoms.input.text wire:model.defer="sub_total_rupiah" readonly="" />
            </x-atoms.input.group-horizontal>
        </div>
    </form>

    <div class="text-center pb-4 pt-5">
        <x-atoms.button.btn-modal color="info" target="#produk_modal">Add Produk</x-atoms.button.btn-modal>
        @if($update)
            <button type="button" class="btn btn-primary" wire:click="updateLine">update Data</button>
        @else
            <button type="button" class="btn btn-primary" wire:click="addLine">Save Data</button>
        @endif
    </div>
</div>
