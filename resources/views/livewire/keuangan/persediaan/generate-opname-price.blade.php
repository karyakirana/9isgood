<div>
    @if(session()->has('message'))
        <x-molecules.alert-danger>
            {{session('message')}}
        </x-molecules.alert-danger>
    @endif
    <x-molecules.card title="Persediaan Price">
        <x-slot name="toolbar">
            <x-atoms.button.btn-primary wire:click="generate">Generate</x-atoms.button.btn-primary>
        </x-slot>
        <livewire:datatables.keuangan.persediaan-opname-price-table />
    </x-molecules.card>
</div>
