<x-molecules.modal title="Daftar Akun" id="modalDaftarAkun" size="xl">
    <livewire:datatables.akun-set-table />
</x-molecules.modal>
@push('custom-scripts')
    <script>
        let modalDaftarAkun = document.getElementById('modalDaftarAkun');
        let modalDaftarAkunInstance = new bootstrap.Modal(modalDaftarAkun);

        Livewire.on('showModalAkun', function (){
            modalDaftarAkunInstance.show()
        })

        Livewire.on('hideModalAkun', function (){
            modalDaftarAkunInstance.hide()
        })
    </script>
@endpush
