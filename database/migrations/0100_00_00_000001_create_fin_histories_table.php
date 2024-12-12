<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_histories', function (Blueprint $table) {
            $table->ulid('id')
                ->primary()
                ->comment('#ID');

            $table->string('source_type');
            $table->string('source_id', 64);

            $table->index(['source_type', 'source_id']);

            $table->smallInteger('type')
                ->unsigned()
                ->comment('アクションタイプ');

            $table->longText('difference')
                ->comment('差分データ');

            $table->datetime('created_at', 6);

            $table->softDeletesDatetime('deleted_at', 6);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_histories');
    }
};
