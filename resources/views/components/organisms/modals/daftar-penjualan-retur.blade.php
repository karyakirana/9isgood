<x-molecules.modal size="xl" title="Daftar Penjualan" id="modalDaftarPenjualanRetur" wire:ignore.self>
    <livewire:datatables.penjualan-retur-set-table />
    <x-slot name="footer"></x-slot>
</x-molecules.modal>
@push('custom-scripts')
    <script>
        let penjualan_retur_modal = document.getElementById('modalDaftarPenjualanRetur');
        let penjualanReturModal = new bootstrap.Modal(penjualan_retur_modal);

        Livewire.on('setPenjualanRetur', function (){
            penjualanReturModal.hide();
        })
    </script>
@endpush
