<div>
    <option>Dipilih</option>
    @foreach($akun as $item)
        <option value="{{$item->id}}">{{$item->deskripsi}}</option>
    @endforeach
</div>
