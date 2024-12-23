<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_subscriptions', function (Blueprint $table) {
            $table->comment('Fincode サブスクリプション');

            $table->string('id', 25)
                ->charset('binary')
                ->primary()
                ->comment('サブスクリプションID');

            $table->string('shop_id', 13)
                ->charset('binary')
                ->index()
                ->comment('ショップID');

            $table->string('pay_type', 32)
                ->index()
                ->comment('決済種別');

            $table->string('plan_id', 25)
                ->charset('binary')
                ->index()
                ->comment('プランID');

            $table->string('plan_name', 200)
                ->comment('プラン名');

            $table->string('customer_id', 60)
                ->charset('binary')
                ->index()
                ->comment('顧客ID');

            $table->string('card_id', 25)
                ->charset('binary')
                ->index()
                ->comment('カードID');

            $table->string('payment_method_id', 25)
                ->charset('binary')
                ->index()
                ->comment('決済手段ID');

            $table->bigInteger('amount')
                ->unsigned()
                ->comment('利用金額');

            $table->bigInteger('tax')
                ->unsigned()
                ->comment('税送料');

            $table->bigInteger('total_amount')
                ->unsigned()
                ->comment('合計金額');

            $table->bigInteger('initial_amount')
                ->unsigned()
                ->comment('利用金額');

            $table->bigInteger('initial_tax')
                ->unsigned()
                ->comment('税送料');

            $table->bigInteger('initial_total_amount')
                ->unsigned()
                ->comment('合計金額');

            $table->string('status', 10)
                ->index()
                ->comment('ステータス');

            $table->dateTime('start_date', 3)
                ->comment('課金開始日');

            $table->dateTime('next_charge_date', 3)
                ->comment('次回課金日');

            $table->dateTime('stop_date', 3)
                ->nullable()
                ->comment('課金停止日');

            $table->boolean('end_month_flag')
                ->comment('月末課金フラグ');

            $table->string('error_code', 11)
                ->nullable()
                ->comment('最新エラー');

            $table->string('client_field_1', 100)
                ->nullable()
                ->comment('加盟店自由項目1');

            $table->string('client_field_2', 100)
                ->nullable()
                ->comment('加盟店自由項目2');

            $table->string('client_field_3', 100)
                ->nullable()
                ->comment('加盟店自由項目3');

            $table->string('remarks', 9)
                ->nullable()
                ->comment('ご利用明細表示内容');

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
        Schema::dropIfExists('fin_subscriptions');
    }
};
