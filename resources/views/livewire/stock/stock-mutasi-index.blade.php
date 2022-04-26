<div>
    <x-molecules.card title="Stock Mutasi {{\Livewire\str($jenisMutasi)->headline()}}">
        <livewire:datatables.stock.stock-mutasi-table :jenis-mutasi="$jenisMutasi"/>
    </x-molecules.card>
    <livewire:stock.detail.stock-mutasi-detail-view />
</div>
