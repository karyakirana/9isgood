<div>
    @if(session()->has('generate'))
        <x-molecules.alert-danger>
            {{session('generate')}}
        </x-molecules.alert-danger>
    @endif
    <div class="pb-5">
        <x-molecules.card>
            <x-atoms.button.btn-primary wire:click="generateStockOpname">Refresh Stock Opname</x-atoms.button.btn-primary>
            <x-atoms.button.btn-primary wire:click="generateStockMasuk">Refresh Stock Masuk</x-atoms.button.btn-primary>
            <x-atoms.button.btn-primary wire:click="generateStockKeluar">Refresh Stock Keluar</x-atoms.button.btn-primary>
            <x-atoms.button.btn-danger wire:click="generateClean">Clean</x-atoms.button.btn-danger>
        </x-molecules.card>
    </div>

    <x-molecules.card>
        <livewire:datatables.stock-inventory-table/>
    </x-molecules.card>
</div>
