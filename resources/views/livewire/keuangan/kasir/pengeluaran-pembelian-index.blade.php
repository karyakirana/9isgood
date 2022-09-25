<div>
    @if(session()->has('message'))
        <x-molecules.alert-danger>{{session('message')}}</x-molecules.alert-danger>
    @endif
    <x-molecules.card title="Daftar Pengeluaran Pembelian">
        <livewire:datatables.pengeluaran-pembelian-table />
    </x-molecules.card>
    <x-molecules.modal-notifications />
</div>
