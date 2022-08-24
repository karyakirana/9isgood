<div>
    <div class="row">
        <div class="col-3">
            <x-atoms.input.group name="produk_id" label="Produk Id">
                <x-atoms.input.text wire:model="produk_id" />
            </x-atoms.input.group>
        </div>
        <div class="col-3">
            <x-atoms.input.group name="jumlah" label="Jumlah">
                <x-atoms.input.text wire:model="jumlah" />
            </x-atoms.input.group>
        </div>
        <div class="col-3">
            <x-atoms.input.group name="jumlah" label="Jumlah">
                <x-atoms.input.select wire:model="gudangId">
                    <x-molecules.select.gudang-list />
                </x-atoms.input.select>
            </x-atoms.input.group>
        </div>
        <div class="col-3">
            <x-atoms.input.group name="kondisi" label="Kondisi">
                <x-atoms.input.select wire:model="kondisi">
                    <option value="baik">Baik</option>
                    <option value="rusak">Rusak</option>
                </x-atoms.input.select>
            </x-atoms.input.group>
        </div>
    </div>
    <div class="col-12">
        {{$hasil}}
    </div>
</div>
