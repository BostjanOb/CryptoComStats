<?php

namespace App\Http\Livewire;

use App\Coingecko;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $transactions = Auth::user()->transactions->groupBy('kind');

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

        $earn = [];
        if ($transactions->has('crypto_earn_interest_paid')) {
            $earn = $transactions['crypto_earn_interest_paid']->groupBy('currency')->map(function ($transactions, $coin) {
                return [
                    'title'         => $coin,
                    'symbol'        => $coin,
                    'amount'        => $transactions->sum('amount'),
                    'native'        => $transactions->sum('native_amount'),
                    'currentNative' => $transactions->sum('amount') * Coingecko::price($coin),
                ];
            });
        }
        if ($transactions->has('crypto_earn_extra_interest_paid')) {
            $earn[] = [
                'title'         => 'Earn Extra Intrest',
                'symbol'        => 'CRO',
                'amount'        => $transactions['crypto_earn_extra_interest_paid']->sum('amount'),
                'native'        => $transactions['crypto_earn_extra_interest_paid']->sum('native_amount'),
                'currentNative' => $transactions['crypto_earn_extra_interest_paid']->sum('amount') * Coingecko::price('cro'),
            ];
        }

        return view('livewire.dashboard')
            ->with('transactions', $transactions)
            ->with('rows', $rows)
            ->with('sum', $sum)
            ->with('earn', $earn);
    }
}
