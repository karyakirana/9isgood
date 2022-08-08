<div>
    <x-molecules.card title="Piutang Penjualan Retur Awal">
        <x-slot:toolbar>
            <x-atoms.button.btn-link-primary class="m-5" href="{{route('keuangan.neraca.awal.piutang-retur.baru')}}">Piutang Retur Baru</x-atoms.button.btn-link-primary>
        </x-slot:toolbar>
        <livewire:datatables.keuangan.piutang-penjualan-retur-awal-table />
    </x-molecules.card>
</div>
