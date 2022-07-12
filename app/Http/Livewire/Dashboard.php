<?php

namespace App\Http\Livewire;

use App\Coingecko;
use App\Platform;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public ?string $from = null;
    public ?string $to = null;

    protected $queryString = ['from', 'to'];

    public function mount()
    {
        $this->from ??= today()->startOfMonth()->format('Y-m-d');
        $this->to ??= today()->endOfMonth()->format('Y-m-d');
    }

    public function prevMonth()
    {
        $this->from = Carbon::parse($this->from)->subMonth()->startOfMonth()->format('Y-m-d');
        $this->to = Carbon::parse($this->to)->startOfMonth()->subMonth()->endOfMonth()->format('Y-m-d');
    }

    public function nextMonth()
    {
        $this->from = Carbon::parse($this->from)->addMonth()->startOfMonth()->format('Y-m-d');
        $this->to = Carbon::parse($this->to)->startOfMonth()->addMonth()->endOfMonth()->format('Y-m-d');
    }

    public function render()
    {
        $transactions = Auth::user()
            ->transactions()
            ->where('platform', Platform::CDC)
            ->when($this->from, fn($q) => $q->where('created_at', '>=', Carbon::parse($this->from)->startOfDay()))
            ->when($this->to, fn($q) => $q->where('created_at', '<=', Carbon::parse($this->to)->endOfDay()))
            ->get()
            ->groupBy('kind');

        $rows = [
            'referral_card_cashback' => ['title' => 'Cashback',],
            'mco_stake_reward'       => ['title' => 'CRO Stake rewards',],
            'reimbursement'          => ['title' => 'Reimbursement (Netflix / Spotify)',],
        ];

        $sum = ['native' => 0, 'currentNative' => 0];

        foreach ($rows as $key => &$row) {
            $row['amount'] = $transactions->has($key) ? $transactions[$key]->sum('amount') : 0;
            $row['native'] = $transactions->has($key) ? $transactions[$key]->sum('native_amount') : 0;
            $row['currentNative'] = $transactions->has($key) ? $transactions[$key]->sum('amount') * Coingecko::price('cro') : 0;

            $sum['native'] += $row['native'];
            $sum['currentNative'] += $row['currentNative'];
        }

        $earnCdc = collect([]);
        if ($transactions->has('crypto_earn_interest_paid')) {
            $earnCdc = $transactions['crypto_earn_interest_paid']
                ->groupBy('currency')
                ->map(function ($transactions, $coin) use (&$sum) {
                    $row = [
                        'title'         => $coin,
                        'symbol'        => $coin,
                        'amount'        => $transactions->sum('amount'),
                        'native'        => $transactions->sum('native_amount'),
                        'currentNative' => $transactions->sum('amount') * Coingecko::price($coin),
                    ];

                    $sum['native'] += $row['native'];
                    $sum['currentNative'] += $row['currentNative'];

                    return $row;
                });
        }
        if ($transactions->has('crypto_earn_extra_interest_paid')) {
            $earnCdc[] = [
                'title'         => 'Earn Extra Interest',
                'symbol'        => 'CRO',
                'amount'        => $transactions['crypto_earn_extra_interest_paid']->sum('amount'),
                'native'        => $transactions['crypto_earn_extra_interest_paid']->sum('native_amount'),
                'currentNative' => $transactions['crypto_earn_extra_interest_paid']->sum('amount') * Coingecko::price('cro'),
            ];

            $sum['native'] += $transactions['crypto_earn_extra_interest_paid']->sum('native_amount');
            $sum['currentNative'] += ($transactions['crypto_earn_extra_interest_paid']->sum('amount') * Coingecko::price('cro'));
        }

        $nexoTransactions = Auth::user()
            ->transactions()
            ->where('platform', Platform::NEXO)
            ->where('kind', 'Interest')
            ->when($this->from, fn($q) => $q->where('created_at', '>=', Carbon::parse($this->from)->startOfDay()))
            ->when($this->to, fn($q) => $q->where('created_at', '<=', Carbon::parse($this->to)->endOfDay()))
            ->get();

        $earnNexo = collect([]);
        if ($nexoTransactions->count()) {
            $earnNexo = $nexoTransactions
                ->groupBy('currency')
                ->map(function ($transactions, $coin) use (&$sum) {
                    $row = [
                        'title'         => $coin,
                        'symbol'        => $coin,
                        'amount'        => $transactions->sum('amount'),
                        'native'        => $transactions->sum('native_amount'),
                        'currentNative' => $transactions->sum('amount') * Coingecko::price($coin),
                    ];

                    $sum['native'] += $row['native'];
                    $sum['currentNative'] += $row['currentNative'];

                    return $row;
                });
        }

        return view('livewire.dashboard')
            ->with('transactions', $transactions)
            ->with('rows', $rows)
            ->with('sum', $sum)
            ->with('earnCdc', $earnCdc)
            ->with('earnNexo', $earnNexo);
    }
}
