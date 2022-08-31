<div>
    <x-molecules.card title="Piutang Penjualan">
        <x-slot:toolbar>
            <x-atoms.button.btn-primary class="me-2" wire:click="generateFromPenjualan">From Penjualan</x-atoms.button.btn-primary>
            <x-atoms.button.btn-primary wire:click="generateFromPenjualanRetur">From Penjualan Retur</x-atoms.button.btn-primary>
        </x-slot:toolbar>
        <livewire:datatables.piutang-penjualan-set-table />
    </x-molecules.card>
</div>
