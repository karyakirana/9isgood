<x-molecules.modal size="xl" title="Daftar Piutang" id="modalPiutangPenjualan" wire:ignore>
    <livewire:datatables.piutang-penjualan-set-table />
</x-molecules.modal>

<livewire:penjualan.penjualan-detail-view />

<livewire:penjualan.penjualan-retur-detail-view />
@push('custom-scripts')
    <script>
        let piutang_penjualan_modal = document.getElementById('modalPiutangPenjualan');
        let piutangPenjualanModal = new bootstrap.Modal(piutang_penjualan_modal);

        Livewire.on('showPiutangPenjualanModal',() =>{
            piutangPenjualanModal.show();
        });

        Livewire.on('setPenjualanRetur', function (){
            piutangPenjualanModal.hide();
        });
        Livewire.on('setPenjualan', function (){
            piutangPenjualanModal.hide();
        });

        // initiate penjualan detail view
        let penjualanDetailView = document.getElementById('penjualan-detail');
        // initiate penjualan retur detail view
        let penjualanReturDetailView = document.getElementById('penjualan-retur-detail');

        // hide modal piutang if detail show
        penjualanDetailView.addEventListener('show.bs.modal', function () {
            piutangPenjualanModal.hide();
        })
        penjualanReturDetailView.addEventListener('show.bs.modal', function () {
            piutangPenjualanModal.hide();
        })

        // show modal piutang if detail hide
        penjualanDetailView.addEventListener('hide.bs.modal', function () {
            piutangPenjualanModal.show();
        })
        penjualanReturDetailView.addEventListener('hide.bs.modal', function () {
            piutangPenjualanModal.show();
        })
    </script>
@endpush
