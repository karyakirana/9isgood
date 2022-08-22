@aware(['error', 'name'])
@props(['error'=>null, 'name'=>null, 'disabled'=>false])
<select name="{{$name}}" x-bind:id="$id('input')" {{ $attributes->class(['form-control', 'is-invalid'=>$errors->has($name)])->merge() }} {{($disabled) ? 'disabled' : ''}}>
    {{$slot}}
</select>
