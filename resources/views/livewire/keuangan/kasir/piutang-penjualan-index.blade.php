<div>
    @if(session()->has('storeMessage'))
        <x-molecules.alert-danger>
            {{ session('storeMessage') }}
        </x-molecules.alert-danger>
    @endif
    <x-molecules.card title="Daftar Piutang Penjualan Awal">
        <x-slot name="toolbar">
            <x-atoms.button.btn-link-primary class="m-4" href="{{route('keuangan.neraca.awal.piutang-penjualan.baru')}}">Piutang Penjualan Baru</x-atoms.button.btn-link-primary>
            <x-atoms.button.btn-link-primary href="{{route('keuangan.neraca.awal.piutang-retur.baru')}}">Piutang Retur Baru</x-atoms.button.btn-link-primary>
        </x-slot>
        <livewire:datatables.keuangan.jurnal-set-piutang-awal-table />
    </x-molecules.card>
</div>
