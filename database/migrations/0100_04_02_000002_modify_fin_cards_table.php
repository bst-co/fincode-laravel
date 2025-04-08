<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fin_cards', function (Blueprint $table) {
            $table->string('status', 32)
                ->after('card_no_hash')
                ->nullable()
                ->comment('決済手段ステータス');
        });
    }

    public function down(): void
    {
        Schema::table('fin_cards', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
