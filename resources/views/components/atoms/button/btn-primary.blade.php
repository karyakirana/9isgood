@props(['size'=>null, 'type'=>'button', 'color'=>'primary'])
<button type="{{$type}}" {{$attributes->class("btn btn-{$color} btn-active-color-gray-200")}}>{{$slot}}</button>
