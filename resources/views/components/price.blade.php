@props([
'price',
'currency',
'decimals' => null
])
<span class="whitespace-nowrap">
    @if($decimals === null)
        {{ $price }}
    @else
        {{ number_format($price, $decimals) }}
    @endif
    {{ $currency }}
</span>
