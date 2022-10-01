<?php

namespace App\Imports;

use App\Models\Transaction;
use App\Models\User;
use App\Platform;
use Illuminate\Support\Carbon;

class BinanceCardImport
{
    public static function import(User $user, $file): void
    {
        $data = collect(
            json_decode(file_get_contents($file))->data
        )->reject(fn($t) => is_null($t->cashbackDetail));

        Transaction::where('created_at', '>=', Carbon::createFromTimestampMs($data->min('transactionTime'), 'UTC'))
            ->where('created_at', '<=', Carbon::createFromTimestampMs($data->max('transactionTime'), 'UTC'))
            ->where('user_id', $user->id)
            ->where('platform', Platform::BINANCE_CARD)
            ->delete();

        $data->each(function ($row) use ($user) {
            Transaction::create([
                'user_id'         => $user->id,
                'platform'        => Platform::BINANCE_CARD,
                'description'     => 'CashBack',
                'currency'        => 'BNB',
                'amount'          => $row->cashbackDetail->cashbackInBNB,
                'to_currency'     => null,
                'to_amount'       => null,
                'native_currency' => null,
                'native_amount'   => null,
                'kind'            => 'CashBack',
                'created_at'      => Carbon::createFromTimestampMs($row->transactionTime, 'UTC'),
            ]);
        });
    }
}
