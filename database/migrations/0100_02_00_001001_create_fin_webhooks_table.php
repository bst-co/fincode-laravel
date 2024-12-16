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
                ->primary()
                ->comment('Webhook ID');

            $table->string('shop_id', 13)
                ->comment('ショップID');

            $table->text('url')
                ->comment('Webhook URL');

            $table->string('event', 40)
                ->comment('対象イベント');

            $table->text('signature')
                ->comment('署名');

            $table->dateTime('created', 3)
                ->comment('作成日');

            $table->dateTime('updated', 3)
                ->nullable()
                ->comment('更新日');

            $table->unique(['shop_id', 'event']);

            $table->datetimes(3);
            $table->softDeletesDatetime('deleted_at', 3);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_webhooks');
    }
};
