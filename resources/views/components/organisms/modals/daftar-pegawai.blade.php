<x-molecules.modal size="xl" title="Daftar Pegawai" id="modalDaftarPegawai">
    <livewire:datatables.pegawai-set-table />
</x-molecules.modal>
@push('custom-scripts')
    <script>
        let modalDaftarPegawai = document.getElementById('modalDaftarPegawai');
        let modalDaftarPegawaiInstance = new bootstrap.Modal(modalDaftarPegawai);

        Livewire.on('showModalDaftarPegawai', function (){
            modalDaftarPegawaiInstance.show()
        })

        Livewire.on('hideModalDaftarPegawai', function (){
            modalDaftarPegawaiInstance.hide()
        })

    </script>
@endpush
