<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fin_cards', function (Blueprint $table) {
            $table->boolean('authorized')
                ->default(false)
                ->after('card_no_hash')
                ->comment('3Dセキュア認証済み');
        });
    }

    public function down(): void
    {
        Schema::table('fin_cards', function (Blueprint $table) {
            $table->dropColumn('authorized');
        });
    }
};
