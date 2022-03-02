@props(['customSecondaryHeader' => false, 'useHeaderAsFooter' => false, 'customFooter' => false])

<div class="{{ $this->responsive ? 'table-responsive' : '' }}">
    <table {{ $attributes->except(['wire:sortable', 'class']) }} class="{{ trim($attributes->get('class')) ?: 'table table-striped'}}">
        <thead>
            <tr class="border fs-4 fw-bold text-center align-middle">
                {{ $head }}
            </tr>
        </thead>

        <tbody {{ $attributes->only('wire:sortable') }}>
            @if ($customSecondaryHeader)
                {{ $customSecondaryHead }}
            @endif

            {{ $body }}
        </tbody>

        @if ($useHeaderAsFooter || $customFooter)
            <tfoot>
                @if ($useHeaderAsFooter)
                    <tr>
                        {{ $head }}
                    </tr>
                @elseif($customFooter)
                    {{ $foot }}
                @endif
            </tfoot>
        @endif
    </table>
</div>
