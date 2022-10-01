<?php

namespace App\Http\Livewire;

use App\Coingecko;
use App\Platform;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
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
        $cdcTransactions = Auth::user()
            ->transactions()
            ->where('platform', Platform::CDC)
            ->when($this->from, fn($q) => $q->where('created_at', '>=', Carbon::parse($this->from)->startOfDay()))
            ->when($this->to, fn($q) => $q->where('created_at', '<=', Carbon::parse($this->to)->endOfDay()))
            ->get()
            ->groupBy('kind');

        $rows = collect([
            'referral_card_cashback' => ['title' => 'Crpyo.com Cashback', 'amount' => 0, 'currency' => 'CRO'],
            'mco_stake_reward'       => ['title' => 'Crypto.com CRO Stake rewards', 'amount' => 0, 'currency' => 'CRO'],
            'reimbursement'          => ['title' => 'Crypto.com Reimbursement (Netflix / Spotify)', 'amount' => 0, 'currency' => 'CRO'],
        ])->map(function ($val, $key) use ($cdcTransactions) {
            $val['amount'] = $cdcTransactions->has($key) ? $cdcTransactions[$key]->sum('amount') : 0;
            $val['native'] = $cdcTransactions->has($key) ? $cdcTransactions[$key]->sum('native_amount') : 0;
            $val['currentNative'] = $cdcTransactions->has($key) ? $cdcTransactions[$key]->sum('amount') * Coingecko::price('cro') : 0;

            return $val;
        });

        $binanceCashback = Auth::user()
            ->transactions()
            ->where('platform', Platform::BINANCE_CARD)
            ->when($this->from, fn($q) => $q->where('created_at', '>=', Carbon::parse($this->from)->startOfDay()))
            ->when($this->to, fn($q) => $q->where('created_at', '<=', Carbon::parse($this->to)->endOfDay()))
            ->sum('amount');
        $rows['binance_cashback'] = [
            'title'         => 'Binance Cashback',
            'amount'        => $binanceCashback,
            'currency'      => 'BNB',
            'currentNative' => $binanceCashback * Coingecko::price('bnb'),
        ];

        $earn = collect([]);
        if ($cdcTransactions->has('crypto_earn_interest_paid')) {
            $earn['Crypto.com'] = $cdcTransactions['crypto_earn_interest_paid']
                ->groupBy('currency')
                ->map(fn($transactions, $coin) => [
                    'title'         => $coin,
                    'symbol'        => $coin,
                    'amount'        => $transactions->sum('amount'),
                    'native'        => $transactions->sum('native_amount'),
                    'currentNative' => $transactions->sum('amount') * Coingecko::price($coin),
                ]);
        }
        if ($cdcTransactions->has('crypto_earn_extra_interest_paid')) {
            $earn['Crypto.com'][] = [
                'title'         => 'Earn Extra Interest',
                'symbol'        => 'CRO',
                'amount'        => $cdcTransactions['crypto_earn_extra_interest_paid']->sum('amount'),
                'native'        => $cdcTransactions['crypto_earn_extra_interest_paid']->sum('native_amount'),
                'currentNative' => $cdcTransactions['crypto_earn_extra_interest_paid']->sum('amount') * Coingecko::price('cro'),
            ];
        }

        $earn['Nexo'] = $this->getEarn(Platform::NEXO, 'Interest');
        $earn['Binance'] = $this->getEarn(Platform::BINANCE_EARN, 'Interest');
        $earn['YouHodler'] = $this->getEarn(Platform::YOUHODLER, 'Interest');

        return view('livewire.dashboard')
            ->with('cdcTransactions', $cdcTransactions)
            ->with('rows', $rows)
            ->with('earn', $earn->filter(fn($p) => $p->count()));
    }

    private function getEarn($platform, $kind): Collection
    {
        return Auth::user()
            ->transactions()
            ->where('platform', $platform)
            ->where('kind', $kind)
            ->when($this->from, fn($q) => $q->where('created_at', '>=', Carbon::parse($this->from)->startOfDay()))
            ->when($this->to, fn($q) => $q->where('created_at', '<=', Carbon::parse($this->to)->endOfDay()))
            ->get()
            ->groupBy('currency')
            ->map(fn($transactions, $coin) => [
                'title'         => $coin,
                'symbol'        => $coin,
                'amount'        => $transactions->sum('amount'),
                'native'        => $transactions->sum('native_amount') ?? 0,
                'currentNative' => $transactions->sum('amount') * Coingecko::price($coin),
            ]);
    }
}
