<x-molecules.modal size="xl" title="Daftar Piutang" id="modalPiutangPenjualan">
    <livewire:datatables.piutang-penjualan-set-table />
</x-molecules.modal>
@push('custom-scripts')
    <script>
        let piutang_penjualan_modal = document.getElementById('modalPiutangPenjualan');
        let piutangPenjualanModal = new bootstrap.Modal(piutang_penjualan_modal);

        Livewire.on('setPenjualanRetur', function (){
            piutangPenjualanModal.hide();
        })
    </script>
@endpush
