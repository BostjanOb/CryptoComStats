<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('platform')
                ->after('user_id')
                ->nullable();

            $table->index(['platform', 'created_at']);
        });

        \Illuminate\Support\Facades\DB::table('transactions')
            ->update(['platform' => 'cdc']);
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('platform');
        });
    }
};
