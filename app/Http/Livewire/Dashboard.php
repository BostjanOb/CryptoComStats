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

        return view('livewire.dashboard')
            ->with('transactions', $transactions)
            ->with('cashback', $transactions->has('referral_card_cashback') ? [
                'amount'        => $transactions['referral_card_cashback']->sum('amount'),
                'native'        => $transactions['referral_card_cashback']->sum('native_amount'),
                'currentNative' => $transactions['referral_card_cashback']->sum('amount') * Coingecko::price('cro'),
            ] : [])
            ->with('croStake', $transactions->has('mco_stake_reward') ? [
                'amount'        => $transactions['mco_stake_reward']->sum('amount'),
                'native'        => $transactions['mco_stake_reward']->sum('native_amount'),
                'currentNative' => $transactions['mco_stake_reward']->sum('amount') * Coingecko::price('cro'),
            ] : [])
            ->with('earn', $transactions->has('crypto_earn_interest_paid') ? $transactions['crypto_earn_interest_paid']->groupBy('currency')->map(function ($transactions, $coin) {
                return [
                    'symbol'        => $coin,
                    'amount'        => $transactions->sum('amount'),
                    'native'        => $transactions->sum('native_amount'),
                    'currentNative' => $transactions->sum('amount') * Coingecko::price($coin),
                ];
            }) : collect([]));
    }
}
