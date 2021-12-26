<?php

use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/', \App\Http\Livewire\Dashboard::class)
        ->name('dashboard');

    Route::get('/import', \App\Http\Livewire\Import::class)
        ->name('import');

    Route::get('/transactions', \App\Http\Livewire\Transactions::class)
        ->name('transactions');
});



