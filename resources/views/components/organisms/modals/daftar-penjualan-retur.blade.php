@props([
    'lastsession'=>false
])
<x-molecules.modal size="xl" title="Daftar Penjualan Retur" id="modalDaftarPenjualanRetur" wire:ignore.self>
    <livewire:datatables.penjualan-retur-set-table :last-session="$lastsession" :set-piutang="true" />
    <x-slot name="footer"></x-slot>
</x-molecules.modal>
<livewire:penjualan.penjualan-retur-detail-view />
@push('custom-scripts')
    <script>
        let penjualan_retur_modal = document.getElementById('modalDaftarPenjualanRetur');
        let penjualanReturModal = new bootstrap.Modal(penjualan_retur_modal);

        Livewire.on('setPenjualanRetur', function (){
            penjualanReturModal.hide();
        })

        Livewire.on('showPenjualanReturDetail', function (){
            penjualanReturModal.hide();
        })

        let penjualanReturDetail = document.getElementById('penjualan-retur-detail');

        Livewire.on('hidePenjualanDetail', function (){
            penjualanReturDetail.hide()
        })

        penjualanReturDetail.addEventListener('hide.bs.modal', function (event) {
            penjualanReturModal.show();
        })
    </script>
@endpush
