<div>
    <x-molecules.card :title="__('Stock Mutasi Baik ke Rusak')">
        <livewire:datatables.stock-mutasi-table :jenis_mutasi="__('baik_rusak')"/>
    </x-molecules.card>
    <livewire:stock.detail.stock-mutasi-detail-view />
</div>
