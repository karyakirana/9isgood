<div>
    <x-molecules.modal id="modalPembelianReturDetail" title="Nomor Nota : {{$pembelianRetur->kode ?? ''}}" size="xl" wire:ignore.self>
        @if($pembelianRetur)
            <form>
                <div class="row">
                    <div class="col-6">
                        <x-atoms.input.group-horizontal label="Customer">
                            <x-atoms.input.plaintext>{{$pembelianRetur->supplier->nama}}</x-atoms.input.plaintext>
                        </x-atoms.input.group-horizontal>
                    </div>
                    <div class="col-6">
                        <x-atoms.input.group-horizontal label="Jenis">
                            <x-atoms.input.plaintext>{{$pembelianRetur->jenis_bayar}}</x-atoms.input.plaintext>
                        </x-atoms.input.group-horizontal>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <x-atoms.input.group-horizontal label="Tgl Nota">
                            <x-atoms.input.plaintext>{{ isset($pembelianRetur->tgl_nota) ? tanggalan_format($pembelianRetur->tgl_nota) : ''}}</x-atoms.input.plaintext>
                        </x-atoms.input.group-horizontal>
                    </div>
                    <div class="col-6">
                        <x-atoms.input.group-horizontal label="Tgl Tempo">
                            <x-atoms.input.plaintext>{{isset($pembelianRetur->tgl_tempo) ? tanggalan_format($pembelianRetur->tgl_tempo) : ''}}</x-atoms.input.plaintext>
                        </x-atoms.input.group-horizontal>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <x-atoms.input.group-horizontal label="Gudang">
                            <x-atoms.input.plaintext>{{ $pembelianRetur->gudang->nama}}</x-atoms.input.plaintext>
                        </x-atoms.input.group-horizontal>
                    </div>
                    <div class="col-6">
                        <x-atoms.input.group-horizontal label="Keterangan">
                            <x-atoms.input.plaintext>{{ $pembelianRetur->keterangan }}</x-atoms.input.plaintext>
                        </x-atoms.input.group-horizontal>
                    </div>
                </div>
            </form>
            <x-atoms.table>
                <x-slot name="head">
                    <tr>
                        <th>Kode</th>
                        <th>Item</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Diskon</th>
                        <th>Sub Total</th>
                    </tr>
                </x-slot>
                @foreach($pembelianReturDetail as $item)
                    <tr>
                        <x-atoms.table.td align="center">
                            {{$item->produk->kode_lokal}}
                        </x-atoms.table.td>
                        <x-atoms.table.td>
                            {{$item->produk->nama}}
                        </x-atoms.table.td>
                        <x-atoms.table.td align="right">
                            {{$item->harga}}
                        </x-atoms.table.td>
                        <x-atoms.table.td align="center">
                            {{$item->jumlah}}
                        </x-atoms.table.td>
                        <x-atoms.table.td align="center">
                            {{$item->diskon}}
                        </x-atoms.table.td>
                        <x-atoms.table.td align="end">
                            {{rupiah_format($item->sub_total)}}
                        </x-atoms.table.td>
                    </tr>
                @endforeach
                <x-slot name="footer">
                    <tr>
                        <td colspan="4"></td>
                        <td>Total</td>
                        <td class="text-end">
                            {{ rupiah_format($pembelianReturDetail->sum('sub_total')) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4"></td>
                        <td>Biaya Lain</td>
                        <td class="text-end">
                            {{rupiah_format($pembelianRetur->biaya_lain)}}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4"></td>
                        <td>PPN</td>
                        <td class="text-end">
                            {{rupiah_format($pembelianRetur->ppn)}}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4"></td>
                        <td>Total Bayar</td>
                        <td class="text-end">
                            {{rupiah_format($pembelianRetur->total_bayar)}}
                        </td>
                    </tr>
                </x-slot>
            </x-atoms.table>
        @endif
    </x-molecules.modal>
    @push('custom-scripts')
        <script>
            let modalPembelianReturDetail = document.getElementById('modalPembelianReturDetail');
            let modalPembelianReturDetailInstance = new bootstrap.Modal(modalPembelianReturDetail);

            Livewire.on('showPembelianReturDetail', function (){
                modalPembelianReturDetailInstance.show()
            })

            Livewire.on('hidePembelianReturDetail', function (){
                modalPembelianReturDetailInstance.hide()
            })
        </script>
    @endpush
</div>
