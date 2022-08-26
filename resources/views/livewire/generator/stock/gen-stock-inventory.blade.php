<div>
    @if(session()->has('error_message'))
        <x-molecules.alert-danger>
            {{ session('error_message') }}
        </x-molecules.alert-danger>
    @endif
    @if(session()->has('success'))
        <x-molecules.alert-danger>
            {{ session('success') }}
        </x-molecules.alert-danger>
    @endif
    <x-molecules.card title="Generate">
        <x-slot:toolbar>
            <x-atoms.button.btn-primary class="me-2" wire:click="generateStockOpname">Stock Opname</x-atoms.button.btn-primary>
            <x-atoms.button.btn-primary class="me-2" wire:click="generateStockMutasi">Stock Mutasi</x-atoms.button.btn-primary>
            <x-atoms.button.btn-primary class="me-2" wire:click="generatePembelian">Stock Pembelian</x-atoms.button.btn-primary>
            <x-atoms.button.btn-primary wire:click="generatePenjualan">Stock Penjualan</x-atoms.button.btn-primary>
        </x-slot:toolbar>
        <livewire:datatables.stock-inventory-log-table />
    </x-molecules.card>
</div>
