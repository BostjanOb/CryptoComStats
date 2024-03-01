<?php

namespace App\Imports;

use App\Models\Transaction;
use App\Models\User;
use App\Platform;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BinanceEarnImport implements ToCollection, WithHeadingRow
{
    use Importable;

    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function collection(Collection $collection)
    {
        Transaction::where('created_at', '>=', Carbon::parse($collection->min('timestamp_utc')))
            ->where('created_at', '<=', Carbon::parse($collection->max('timestamp_utc')))
            ->where('user_id', $this->user->id)
            ->where('platform', Platform::BINANCE_EARN)
            ->delete();

        $collection->each(function ($row) {
            Transaction::create([
                'user_id' => $this->user->id,
                'platform' => Platform::BINANCE_EARN,
                'description' => 'Interest',
                'currency' => $row['coin'],
                'amount' => $row['amount'],
                'to_currency' => null,
                'to_amount' => null,
                'native_currency' => null,
                'native_amount' => null,
                'kind' => 'Interest',
                'created_at' => Carbon::parse($row['dateutc'], 'UTC'),
            ]);
        });
    }
}
