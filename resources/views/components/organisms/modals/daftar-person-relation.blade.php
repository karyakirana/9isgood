<x-molecules.modal size="xl" title="Daftar Pihak ke 3" id="modalPersonRelation">
    <livewire:datatables.person-relation-set-table />
</x-molecules.modal>

@push('custom-scripts')
    <script>
        let modalPersonRelation = document.getElementById('modalPersonRelation');
        let modalPersonRelationInstance = new bootstrap.Modal(modalPersonRelation);

        Livewire.on('showModalPerson', function (){
            modalPersonRelationInstance.show()
        })

        Livewire.on('hideModalPerson', function (){
            modalPersonRelationInstance.hide()
        })
    </script>
@endpush
