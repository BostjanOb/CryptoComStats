<?php

namespace App\Imports;

use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TransactionsImport implements ToCollection, WithHeadingRow
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
            ->delete();

        $collection->each(function ($row) {
            Transaction::create([
                'user_id'         => $this->user->id,
                'description'     => $row['transaction_description'],
                'currency'        => $row['currency'],
                'amount'          => $row['amount'],
                'to_currency'     => $row['to_currency'] ?? null,
                'to_amount'       => $row['to_amount'] ?? null,
                'native_currency' => $row['native_currency'],
                'native_amount'   => $row['native_amount'],
                'kind'            => $row['transaction_kind'],
                'created_at'      => Carbon::parse($row['timestamp_utc'], 'UTC'),
            ]);
        });
    }
}
