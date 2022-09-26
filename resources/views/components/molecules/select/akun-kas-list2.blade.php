<div>
    @php
        $akun = \App\Models\Keuangan\Akun::query()->whereRelation('akunTipe', 'deskripsi', 'like', '%kas%')->get();
    @endphp
    <option>Dipilih</option>
    @foreach($akun as $item)
        <option value="{{$item->id}}">{{$item->deskripsi}}</option>
    @endforeach
</div>
