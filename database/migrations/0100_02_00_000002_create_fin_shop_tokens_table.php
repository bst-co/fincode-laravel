<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_shop_tokens', function (Blueprint $table) {
            $table->string('id', 13)
                ->charset('binary')
                ->primary()
                ->comment('#ショップID');

            $table->string('tenant_name', 25)
                ->nullable()
                ->comment('3Dセキュア表示用店舗名');

            $table->text('public_key')
                ->nullable()
                ->comment('公開APIキー');

            $table->text('secret_key')
                ->nullable()
                ->comment('秘密APIキー');

            $table->string('client_field', 100)
                ->nullable()
                ->comment('加盟店自由項目');

            $table->boolean('live')
                ->nullable()
                ->comment('本番環境利用フラグ');

            $table->datetime('created_at', 3)
                ->nullable()
                ->comment('作成日時');

            $table->datetime('updated_at', 3)
                ->nullable()
                ->comment('更新日時');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_shop_tokens');
    }
};
