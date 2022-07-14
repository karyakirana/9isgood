@php
    $akun = (new \App\Haramain\Service\SistemKeuangan\Akun\AkunRepository())->getAkunByTipe('Hutang Jangka Pendek');
@endphp
<div>
    <option>Dipilih</option>
    @foreach($akun as $item)
        <option value="{{$item->id}}">{{$item->deskripsi}}</option>
    @endforeach
</div>
