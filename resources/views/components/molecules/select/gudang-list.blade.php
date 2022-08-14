@php
    $gudang = \App\Models\Master\Gudang::all();
@endphp

<option>Dipilih</option>
@foreach($gudang as $item)
    <option value="{{$item->id}}">{{ucwords($item->nama)}}</option>
@endforeach
