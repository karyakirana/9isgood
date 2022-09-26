@aware(['error', 'name'])
@props(['error'=>null, 'name'=>null])
<textarea x-bind:id="$id('input')" name="{{$name}}" {{ $attributes->class(['form-control', 'is-invalid'=>$errors->has($name)])->merge() }} rows="3" class="form-control"></textarea>
