@props([
    'lastSession'=>false,
    'setPiutang'=>false,
])
<x-molecules.modal size="xl" title="Daftar Penjualan" id="modalDaftarPenjualan" wire:ignore.self>
    <livewire:datatables.penjualan-set-table :last-session="$lastSession" :set-piutang="$setPiutang" />
    <x-slot name="footer"></x-slot>
</x-molecules.modal>
<livewire:penjualan.penjualan-detail-view />
@push('custom-scripts')
    <script>
        let penjualan_modal = document.getElementById('modalDaftarPenjualan');
        let penjualanModal = new bootstrap.Modal(penjualan_modal);

        Livewire.on('setPenjualan', function (){
            penjualanModal.hide();
        })

        Livewire.on('showPenjualanDetail', function (){
            penjualanModal.hide();
        })

        let penjualanDetail = document.getElementById('penjualan-detail');

        Livewire.on('hidePenjualanDetail', function (){
            penjualanDetail.hide()
        })

        penjualanDetail.addEventListener('hide.bs.modal', function (event) {
            penjualanModal.show();
        })
    </script>
@endpush
