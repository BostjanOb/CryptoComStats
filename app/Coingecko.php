<?php

namespace App;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Coingecko
{
    public static function price($coin, $fiat = 'eur'): ?float
    {
        $coin = Str::lower($coin);

        return Cache::remember(
            "coingecko_price_{$coin}_{$fiat}",
            now()->addMinutes(10),
            function () use ($coin, $fiat) {
                $coinId = Arr::get(self::coinsList()->get($coin, []), 'id', null);
                if ($coinId === null) {
                    throw new \InvalidArgumentException("Coin $coin not found");
                }

                $price = Http::get('https://api.coingecko.com/api/v3/simple/price', [
                    'ids'           => $coinId,
                    'vs_currencies' => $fiat,
                ])->json();

                return Arr::get($price, "{$coinId}.{$fiat}", null);
            }
        );
    }

    public static function coinsList(): Collection
    {
        return Cache::remember(
            'coins_list',
            now()->addMinutes(60),
            fn() => Http::get('https://api.coingecko.com/api/v3/coins/list')
                ->collect()
                ->keyBy('symbol')
        );
    }
}
