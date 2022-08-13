<div>
    <x-molecules.card title="Testing Penjualan ">
        <x-slot name="toolbar">
            <div class="pb-4 pt-5">
                <x-atoms.button.btn-link-primary>Print Report</x-atoms.button.btn-link-primary>
                <x-atoms.button.btn-link-primary>New Data</x-atoms.button.btn-link-primary>
            </div>
        </x-slot>
        <livewire:datatables.penjualan-table />
    </x-molecules.card>
    <livewire:penjualan.penjualan-detail-view />
</div>

