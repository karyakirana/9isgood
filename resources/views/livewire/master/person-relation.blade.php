<div>
    <x-molecules.card title="Daftar Pihak ke 3">
        <x-slot:toolbar>
            <x-atoms.button.btn-primary wire:click.prevent="$emit('showModalPerson')">Add New</x-atoms.button.btn-primary>
        </x-slot:toolbar>
        <livewire:person-relation-table />
    </x-molecules.card>
    <x-molecules.modal id="modalFormPerson" size="xl">
        <form>
            <x-atoms.input.group-horizontal label="Nama" name="nama" class="mb-5">
                <x-atoms.input.text wire:model.defer="nama" />
            </x-atoms.input.group-horizontal>
            <x-atoms.input.group-horizontal label="Telepon" name="telepon" class="mb-5">
                <x-atoms.input.text wire:model.defer="telepon" />
            </x-atoms.input.group-horizontal>
            <x-atoms.input.group-horizontal label="Alamat" name="alamat" class="mb-5">
                <x-atoms.input.text wire:model.defer="alamat" />
            </x-atoms.input.group-horizontal>
            <x-atoms.input.group-horizontal label="Keterangan" name="keterangan" class="mb-5">
                <x-atoms.input.textarea wire:model.defer="keterangan" />
            </x-atoms.input.group-horizontal>
        </form>
        <x-slot:footer>
            @if($update)
                <x-atoms.button.btn-primary wire:click.prevent="update">Update</x-atoms.button.btn-primary>
            @else
                <x-atoms.button.btn-primary wire:click.prevent="store">Store</x-atoms.button.btn-primary>
            @endif
        </x-slot:footer>
    </x-molecules.modal>

    @push('custom-scripts')
        <script>
            let modalFormPerson = document.getElementById('modalFormPerson');
            let modalFormPersonInstance = new bootstrap.Modal(modalFormPerson);

            Livewire.on('showModalPerson', function (){
                modalFormPersonInstance.show()
            })

            Livewire.on('hideModalPerson', function (){
                modalFormPersonInstance.hide()
            })
        </script>
    @endpush
</div>
