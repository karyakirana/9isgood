<x-molecules.modal title="Daftar Customer" id="customer_modal" size="xl" wire:ignore.self>
    <livewire:datatables.customer-set-table />
    <x-slot name="footer"></x-slot>
</x-molecules.modal>
@push('custom-scripts')
    <script>
        let customer_modal = document.getElementById('customer_modal');
        let customerModal = new bootstrap.Modal(customer_modal);

        Livewire.on('hideModalCustomer', function (){
            customerModal.hide();
        })
    </script>
@endpush
