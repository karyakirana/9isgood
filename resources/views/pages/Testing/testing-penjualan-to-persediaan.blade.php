<x-metronics-layout>
    <x-molecules.card title="Generate Penjualan To Persediaan">
        <x-slot name="toolbar">
            <div class="pb-4 pt-5">
                <x-atoms.button.btn-link-primary>Print Report</x-atoms.button.btn-link-primary>
                <x-atoms.button.btn-link-primary>New Data</x-atoms.button.btn-link-primary>
            </div>
        </x-slot>
        <livewire:datatables.testing.generate-penjualan-to-persediaan />
    </x-molecules.card>
    <livewire:penjualan.penjualan-detail-view />
</x-metronics-layout>

