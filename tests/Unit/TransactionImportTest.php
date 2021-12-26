<?php

use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Faker\faker;

uses(RefreshDatabase::class, Tests\TestCase::class);

it('inserts data', function () {
    $user = User::factory()->create();

    (new \App\Imports\TransactionsImport($user))
        ->import(base_path('tests/Fixtures/transactions.csv'));

    expect(\App\Models\Transaction::count())->toEqual(14);
});

it('removes old data before insert', function () {
    $user = User::factory()->create();

    Transaction::create([
        'user_id'         => $user->id,
        'description'     => 'Card Cashback',
        'currency'        => 'CRO',
        'amount'          => faker()->randomFloat(2, -100, 100),
        'native_currency' => 'EUR',
        'native_amount'   => faker()->randomFloat(2, -100, 100),
        'kind'            => 'referral_card_cashback',
        'created_at'      => Carbon::parse('2021-12-20 15:00:24'),
    ]);

    (new \App\Imports\TransactionsImport($user))
        ->import(base_path('tests/Fixtures/transactions.csv'));

    expect(\App\Models\Transaction::count())->toEqual(14);
});
