<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_histories', function (Blueprint $table) {
            $table->comment('テーブル変更履歴');

            $table->ulid('id')
                ->primary()
                ->comment('#ID');

            $table->string('source_type')
                ->comment('ソース種別');

            $table->string('source_id', 64)
                ->charset('binary')
                ->comment('ソースID');

            $table->index(['source_type', 'source_id']);

            $table->smallInteger('type')
                ->unsigned()
                ->comment('アクションタイプ');

            $table->longText('difference')
                ->comment('差分データ');

            $table->datetime('created_at', 6)
                ->nullable()
                ->comment('作成日時');

            $table->datetime('deleted_at', 6)
                ->nullable()
                ->comment('削除日時');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_histories');
    }
};
