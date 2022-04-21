<div>
    <x-molecules.card title="Data Retur Penjualan">
        <x-slot name="toolbar">
            <x-atoms.button.btn-link-primary :href="route('penjualan.piutangretur.trans')">New Data</x-atoms.button.btn-link-primary>
        </x-slot>
        <livewire:datatables.keuangan.jurnal-set-piutang-retur-table />
    </x-molecules.card>
</div>
