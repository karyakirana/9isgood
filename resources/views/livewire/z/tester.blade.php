<div>
    <x-molecules.card>
        @foreach($stockData as $item)
            Produk_id adalah {{$item->produk_id}} <br>
            Harga adalah {{$item->harga}} <br>
            Jumlah adalah {{$item->jumlah}} <br>
            Jumlah adalah {{$item->keterangan}} <br>
        @endforeach
    </x-molecules.card>
</div>
