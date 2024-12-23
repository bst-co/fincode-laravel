<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_plans', function (Blueprint $table) {
            $table->comment('Fincode サブスクリプションプラン');

            $table->string('id', 25)
                ->charset('binary')
                ->primary()
                ->comment('#ID');

            $table->string('plan_name', 200)
                ->comment('プラン名');

            $table->string('description', 400)
                ->nullable()
                ->comment('プランの説明');

            $table->string('shop_id', 13)
                ->charset('binary')
                ->index()
                ->comment('ショップID');

            $table->bigInteger('amount')
                ->unsigned()
                ->comment('利用金額');

            $table->bigInteger('tax')
                ->unsigned()
                ->comment('税送料');

            $table->bigInteger('total_amount')
                ->unsigned()
                ->comment('合計金額');

            $table->string('interval_pattern', 8)
                ->comment('課金間隔');

            $table->smallInteger('interval_count')
                ->unsigned()
                ->comment('課金間隔数');

            $table->boolean('used_flag')
                ->comment('利用済みフラグ');

            $table->boolean('delete_flag')
                ->comment('削除フラグ');

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
        Schema::dropIfExists('fin_plans');
    }
};
