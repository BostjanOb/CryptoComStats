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

class NexoTransactionsImport implements ToCollection, WithHeadingRow
{
    use Importable;

    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function collection(Collection $collection)
    {
        Transaction::where('created_at', '>=', Carbon::parse($collection->min('date_time')))
            ->where('created_at', '<=', Carbon::parse($collection->max('date_time')))
            ->where('user_id', $this->user->id)
            ->where('platform', Platform::NEXO)
            ->delete();

        $collection->each(function ($row) {
            Transaction::create([
                'user_id'         => $this->user->id,
                'platform'        => Platform::NEXO,
                'description'     => $row['type'],
                'currency'        => $row['output_currency'],
                'amount'          => $row['output_amount'],
                'to_currency'     => null,
                'to_amount'       => null,
                'native_currency' => 'USD',
                'native_amount'   => (float)substr($row['usd_equivalent'], 1),
                'kind'            => $row['type'],
                'created_at'      => Carbon::parse($row['date_time'], 'UTC'),
            ]);
        });
    }
}
