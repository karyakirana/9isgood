@props(['title'=>null])
<div {{$attributes->class('card')->merge()}}>
    @if($title)
        <div class="card-header">
            <div class="card-title">{{$title}}</div>
            @isset($toolbar)
                <div class="card-toolbar">
                    {{$toolbar}}
                </div>
            @endisset
        </div>
    @endif
    <div class="card-body">
        {{$slot}}
    </div>
    @isset($footer)
        <div class="card-footer">
            {{$footer}}
        </div>
    @endisset
</div>
