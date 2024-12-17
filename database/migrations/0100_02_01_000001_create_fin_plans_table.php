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

            $table->binary('id', 25)
                ->primary()
                ->comment('#ID');

            $table->string('plan_name', 200)
                ->comment('プラン名');

            $table->string('description', 400)
                ->nullable()
                ->comment('プランの説明');

            $table->binary('shop_id', 13)
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

            $table->dateTime('created')
                ->comment('作成日');

            $table->dateTime('updated')
                ->nullable()
                ->comment('更新日');

            $table->datetimes(3);

            $table->softDeletesDatetime('deleted_at', 3);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_plans');
    }
};
