<x-molecules.modal size="xl" title="Daftar Hutang Pembelian" id="modalDaftarHutangPembelian">
    <livewire:datatables.hutang-pembelian-set-table />
</x-molecules.modal>
@push('custom-scripts')
    <script>
        let modalDaftarHutangPembelian = document.getElementById('modalDaftarHutangPembelian');
        let modalDaftarHutangPembelianInstance = new bootstrap.Modal(modalDaftarHutangPembelian);

        // listen emit
        Livewire.on('hideModalHutangPembelian', function (){
            modalDaftarHutangPembelianInstance.hide()
        })
        Livewire.on('showModalHutangPembelian', function (){
            modalDaftarHutangPembelianInstance.show()
        })
    </script>
@endpush
