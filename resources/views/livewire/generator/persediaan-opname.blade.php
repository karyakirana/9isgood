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
            <x-atoms.button.btn-primary wire:click="generate">Generate</x-atoms.button.btn-primary>
        </x-slot:toolbar>
        <livewire:datatables.persediaan-table/>
    </x-molecules.card>
</div>
