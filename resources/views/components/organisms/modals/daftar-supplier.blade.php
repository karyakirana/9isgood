<x-molecules.modal size="xl" id="modalDaftarSupplier" title="Modal Supplier">
    <livewire:datatables.supplier-set-table />
</x-molecules.modal>

@push('custom-scripts')
    <script>
        // initiate modal supplier
        let modalSupplier = document.getElementById('modalDaftarSupplier');
        let modalSupplierInstance = new bootstrap.Modal(modalSupplier);

        // listen emit
        Livewire.on('hideModalSupplier', function (){
            modalSupplierInstance.hide()
        })
        Livewire.on('showModalSupplier', function (){
            modalSupplierInstance.show()
        })
    </script>
@endpush
