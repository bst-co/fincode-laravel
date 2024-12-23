<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_webhooks', function (Blueprint $table) {
            $table->string('id', 50)
                ->charset('binary')
                ->primary()
                ->comment('Webhook ID');

            $table->string('shop_id', 13)
                ->charset('binary')
                ->index()
                ->comment('ショップID');

            $table->text('url')
                ->comment('Webhook URL');

            $table->string('event', 40)
                ->index()
                ->comment('対象イベント');

            $table->text('signature')
                ->comment('署名');

            $table->dateTime('created', 3)
                ->comment('プラットフォーム上の作成日時');

            $table->dateTime('updated', 3)
                ->nullable()
                ->comment('プラットフォーム上の更新日時');

            $table->datetime('created_at', 3)
                ->nullable()
                ->comment('作成日時');

            $table->datetime('updated_at', 3)
                ->nullable()
                ->comment('更新日時');

            $table->datetime('deleted_at', 3)
                ->nullable()
                ->comment('削除日時');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_webhooks');
    }
};
