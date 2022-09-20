<div>
    @if(session()->has('message'))
        <x-molecules.alert-danger>
            {{session('message')}}
        </x-molecules.alert-danger>
    @endif
    <x-molecules.card title="Persediaan Opname">
        <x-slot:toolbar>
            <x-atoms.button.btn-danger class="me-2" wire:click="destroy">Delete</x-atoms.button.btn-danger>
            <x-atoms.button.btn-primary wire:click="generate">Generate</x-atoms.button.btn-primary>
        </x-slot:toolbar>
        <livewire:datatables.keuangan.persediaan-opname-table />
    </x-molecules.card>
</div>
