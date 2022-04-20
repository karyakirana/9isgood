<div>
    <x-molecules.card :title="__('Stock Mutasi Baik ke Baik')">
        <livewire:datatables.stock-mutasi-table :jenis_mutasi="__('baik_baik')"/>
    </x-molecules.card>
    <livewire:stock.detail.stock-mutasi-detail-view />
</div>
