<div>
    <x-molecules.modal title="Detail Stock Masuk : {{isset($stock_data) ? $stock_data->kode : ''}}" size="xl" id="stock-detail" wire:ignore.self>
        @isset($stock_data)
        <form>
            <div class="row">
                <div class="col-6">
                    <x-atoms.input.group-horizontal label="User">
                        <x-atoms.input.plaintext>{{ucfirst($stock_data->users->name)}}</x-atoms.input.plaintext>
                    </x-atoms.input.group-horizontal>
                </div>
                <div class="col-6">
                    <x-atoms.input.group-horizontal label="Kondisi">
                        <x-atoms.input.plaintext>{{ucwords($stock_data->kondisi)}}</x-atoms.input.plaintext>
                    </x-atoms.input.group-horizontal>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <x-atoms.input.group-horizontal label="Tgl Masuk">
                        <x-atoms.input.plaintext>{{ tanggalan_format($stock_data->tgl_masuk)}}</x-atoms.input.plaintext>
                    </x-atoms.input.group-horizontal>
                </div>
                <div class="col-6">
                    <x-atoms.input.group-horizontal label="Supplier">
                        <x-atoms.input.plaintext>{{ucwords($stock_data->supplier->nama)}}</x-atoms.input.plaintext>
                    </x-atoms.input.group-horizontal>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <x-atoms.input.group-horizontal label="Gudang">
                        <x-atoms.input.plaintext>{{ ucwords($stock_data->gudang->nama) }}</x-atoms.input.plaintext>
                    </x-atoms.input.group-horizontal>
                </div>
                <div class="col-6">
                    <x-atoms.input.group-horizontal label="Keterangan">
                        <x-atoms.input.plaintext>{{ ucwords($stock_data->keterangan)  }}</x-atoms.input.plaintext>
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
        @endisset
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
