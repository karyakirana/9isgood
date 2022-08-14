<x-metronics-layout>
    <x-molecules.card title="Daftar Persediaan">
        <x-slot:toolbar>
            <x-atoms.button.btn-primary class="me-2">Gudang</x-atoms.button.btn-primary>
            <x-atoms.button.btn-primary class="me-2" color="info">Filter</x-atoms.button.btn-primary>
            <x-atoms.button.btn-primary class="me-2" color="danger">Reset</x-atoms.button.btn-primary>
        </x-slot:toolbar>
        <livewire:datatables.persediaan-table />
    </x-molecules.card>
</x-metronics-layout>
