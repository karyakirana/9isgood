@aware(['error', 'name'])
@props([
    'type'=>'after',
    'name'=>null
])
<div {{$attributes->class('input-group')}}>
    @if($type == 'after')
        {{$slot}}
        {{$inputGroup}}
    @else
        {{$inputGroup}}
        {{$slot}}
    @endif
    @error($name)
    <span class="invalid-feedback">{{$message}}</span>
    @enderror
</div>
