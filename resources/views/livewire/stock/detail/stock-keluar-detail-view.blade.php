<div>
    <x-molecules.modal title="Detail Stock Keluar : {{isset($stock_data) ? $stock_data->kode : ''}}" size="xl" id="stock-detail" wire:ignore.self>
        <form>
            <div class="row">
                <div class="col-6">
                    <x-atoms.input.group-horizontal label="User">
                        <x-atoms.input.plaintext>{{$stock_data->user->nama ?? ''}}</x-atoms.input.plaintext>
                    </x-atoms.input.group-horizontal>
                </div>
                <div class="col-6">
                    <x-atoms.input.group-horizontal label="Kondisi">
                        <x-atoms.input.plaintext>{{$stock_data->kondisi ?? ''}}</x-atoms.input.plaintext>
                    </x-atoms.input.group-horizontal>
                </div>  
            </div>
            <div class="row">
                <div class="col-6">
                    <x-atoms.input.group-horizontal label="Tgl Keluar">
                        <x-atoms.input.plaintext>{{ isset($stock_data->tgl_keluar) ? tanggalan_format($stock_data->tgl_keluar) : ''}}</x-atoms.input.plaintext>
                    </x-atoms.input.group-horizontal>
                </div>
                <div class="col-6">
                    <x-atoms.input.group-horizontal label="Supplier">
                        <x-atoms.input.plaintext>{{$stock_data->supplier->nama ?? ''}}</x-atoms.input.plaintext>
                    </x-atoms.input.group-horizontal>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <x-atoms.input.group-horizontal label="Gudang">
                        <x-atoms.input.plaintext>{{ $stock_data->gudang->nama ?? ''}}</x-atoms.input.plaintext>
                    </x-atoms.input.group-horizontal>
                </div>
                <div class="col-6">
                    <x-atoms.input.group-horizontal label="Keterangan">
                        <x-atoms.input.plaintext>{{ $stock_data->keterangan ?? ''  }}</x-atoms.input.plaintext>
                    </x-atoms.input.group-horizontal>
                </div>
            </div>
        </form>
        <x-atoms.table>
            <x-slot name="head">
                <tr>
                    <th>Kode</th>
                    <th>Item</th>
                    <th>Jumlah</th>
                </tr>
            </x-slot>
            @isset($stock_detail_data)
                @foreach($stock_detail_data as $item)
                    <tr>
                        <x-atoms.table.td align="center">
                            {{$item->produk->kode_lokal}}
                        </x-atoms.table.td>
                        <x-atoms.table.td>
                            {{$item->produk->nama}}
                        </x-atoms.table.td>
                        <x-atoms.table.td align="center">
                            {{$item->jumlah}}
                        </x-atoms.table.td>
                    </tr>
                @endforeach
            @endisset
        </x-atoms.table>
    </x-molecules.modal>

    @push('custom-scripts')
        <script>
            let modal_stock_detail = document.getElementById('stock-detail');
            let modalStockDetail = new bootstrap.Modal(modal_stock_detail);

            Livewire.on('hideStockDetail', function (){
                modalStockDetail.hide()
            })

            Livewire.on('showStockDetail', function (){
                modalStockDetail.show()
            })
        </script>
    @endpush
</div>
