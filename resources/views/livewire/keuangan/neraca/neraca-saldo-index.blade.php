<div>
    <x-molecules.card title="Data Neraca Saldo">
       
        <livewire:datatables.keuangan.neraca-saldo-table />

        
    <x-atoms.table>
    <x-slot name="head">
        <tr>
        <th width="20%">Sub Total Debet</th>
        <th width="20%">Sub Total Kredit</th>
        </tr>
    </x-slot>
    <td>
        {{ $total_debet }}
    </td>
    <td>
        {{ $total_kredit }}
    </td>

        
    </x-atoms.table>

    </x-molecules.card>

    

</div>
