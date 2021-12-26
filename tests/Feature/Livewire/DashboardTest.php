<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;
use function Pest\Faker\faker;

uses(RefreshDatabase::class);

it('redirects for guest', function () {
    get('/')->assertStatus(\Illuminate\Http\Response::HTTP_FOUND);
});

it('shows dashboard for user', function () {
    $this->actingAs(User::factory()->create())
        ->get('/')
        ->assertOk()
        ->assertSee('Dashboard');
});

it('set correct sum', function () {
    $user = User::factory()->create();

    (new \App\Imports\TransactionsImport($user))
        ->import(base_path('tests/Fixtures/transactions.csv'));

    $this->actingAs($user);

    $this->get('/')
        ->assertOk();

    \Livewire\Livewire::test(\App\Http\Livewire\Dashboard::class)
        ->assertViewHas('sum', [
            'native'        => 12.91,
            'currentNative' => 15.629025327163708,
        ]);
});
