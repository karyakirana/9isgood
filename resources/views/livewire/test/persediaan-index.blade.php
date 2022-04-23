<div>
    <x-molecules.card title="Persediaan Transaksi">
        <livewire:test.persediaan-index-test-table />
    </x-molecules.card>

    <x-molecules.modal size="xl" id="detailModal">
        <x-atoms.table>
            <x-slot name="head">
                <tr>
                    <th>Kode</th>
                    <th>Produk</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Sub Total</th>
                </tr>
            </x-slot>
            @forelse($data_detail as $row)
                <tr>
                    <x-atoms.table.td>
                        {{$row->kode_lokal}}
                    </x-atoms.table.td>
                    <x-atoms.table.td>
                        {{$row->produk}} <br>
                        {{$row->produk_kategori_harga}} {{$row->cover}}
                    </x-atoms.table.td>
                    <x-atoms.table.td>
                        {{($row->harga) ? rupiah_format($row->harga) : null}}
                    </x-atoms.table.td>
                    <x-atoms.table.td>
                        {{$row->jumlah}}
                    </x-atoms.table.td>
                    <x-atoms.table.td>
                        {{($row->sub_total) ? rupiah_format($row->sub_total) : null}}
                    </x-atoms.table.td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </x-atoms.table>
    </x-molecules.modal>

    @push('custom-scripts')
        <script>
            let detail_modal = document.getElementById('detailModal');
            let detailModal = new bootstrap.Modal(detail_modal);

            Livewire.on('detail', function (){
                detailModal.show();
            })
        </script>
    @endpush
</div>
