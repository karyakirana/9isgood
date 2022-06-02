<div>
    <x-molecules.card title="Data Neraca Saldo">
       
        <livewire:datatables.keuangan.neraca-saldo-table />
    </x-molecules.card>
    <x-atoms.table>
        <x-slot name="head">
        <tr>
        <th width="20%">Sub Total</th>
        </tr>
        <tr>
         <td class="text-end" wire:model.defer="pipe">
            
         </td>
        </tr>
        </x-slot>
    </x-atoms.table>
</div>
