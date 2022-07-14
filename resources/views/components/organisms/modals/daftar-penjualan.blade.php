<x-molecules.modal size="xl" title="Daftar Penjualan" id="modalDaftarPenjualan" wire:ignore.self>
    <livewire:datatables.penjualan-set-table />
    <x-slot name="footer"></x-slot>
</x-molecules.modal>
@push('custom-scripts')
    <script>
        let penjualan_modal = document.getElementById('modalDaftarPenjualan');
        let penjualanModal = new bootstrap.Modal(penjualan_modal);

        Livewire.on('setPenjualan', function (){
            penjualanModal.hide();
        })
    </script>
@endpush
