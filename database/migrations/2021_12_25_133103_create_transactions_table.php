<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();

            $table->string('description');
            $table->string('currency');
            $table->decimal('amount', 20, 8);
            $table->string('to_currency')->nullable();
            $table->decimal('to_amount', 20, 8)->nullable();
            $table->string('native_currency')->nullable();
            $table->decimal('native_amount', 20, 8)->nullable();
            $table->string('kind')->index();

            $table->timestamps();

            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
