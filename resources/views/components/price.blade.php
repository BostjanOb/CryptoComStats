@props([
'price',
'currency',
'decimals' => null
])
<span class="whitespace-nowrap">
    @if($decimals === null)
        @php
            $decimals = strlen($price) - strpos($price, '.');
        @endphp
    @endif

    {{ number_format($price, $decimals, ',', '.') }} {{ $currency }}
</span>
