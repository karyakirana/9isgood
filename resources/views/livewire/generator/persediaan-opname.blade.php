<div>
    {{-- alert store --}}
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
    <x-molecules.card title="Persediaan">
        <x-slot:toolbar>
            <x-atoms.button.btn-primary class="me-2" wire:click="generateStockOpname">Stock Opname</x-atoms.button.btn-primary>
            <x-atoms.button.btn-primary class="me-2" wire:click="generateStockOpname">Pembelian</x-atoms.button.btn-primary>
            <x-atoms.button.btn-primary class="me-2" wire:click="generateMutasi">Mutasi</x-atoms.button.btn-primary>
            <x-atoms.button.btn-primary class="me-2" wire:click="generatePenjualan">Penjualan</x-atoms.button.btn-primary>
            <x-atoms.button.btn-primary wire:click="generatePenjualanRetur">Penjualan Retur</x-atoms.button.btn-primary>
        </x-slot:toolbar>
        <livewire:datatables.persediaan-table/>
    </x-molecules.card>
</div>
