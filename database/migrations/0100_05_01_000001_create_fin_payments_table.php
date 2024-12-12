<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_payments', function (Blueprint $table) {
            $table->comment('Fincode決済データ');

            $table->char('id', 30)
                ->primary()
                ->comment('#オーダーID (o_***)');

            $table->char('shop_id', 13)
                ->index()
                ->comment('#ショップID (s_***)');

            $table->string('pay_type', 32)
                ->comment('決済種別');

            $table->ulidMorphs('pay_method');

            $table->string('job_code', 16)
                ->comment('取引種別');

            $table->string('status', 32)
                ->comment('決済ステータス');

            $table->string('access_id', 24)
                ->comment('アクセスID');

            $table->integer('amount')
                ->unsigned()
                ->comment('利用金額');

            $table->integer('tax')
                ->unsigned()
                ->comment('税送料');

            $table->bigInteger('total_amount')
                ->unsigned()
                ->comment('合計金額');

            $table->string('client_field_1', 100)
                ->nullable()
                ->comment('加盟店自由項目1');

            $table->string('client_field_2', 100)
                ->nullable()
                ->comment('加盟店自由項目2');

            $table->string('client_field_3', 100)
                ->nullable()
                ->comment('加盟店自由項目3');

            $table->dateTime('process_date', 3)
                ->comment('決済 処理日時');

            $table->string('customer_id', 60)
                ->index()
                ->nullable()
                ->comment('顧客ID');

            $table->string('customer_group_id', 13)
                ->index()
                ->nullable()
                ->comment('顧客情報共有グループID');

            $table->string('error_code', 11)
                ->nullable()
                ->comment('最新エラーコード');

            $table->datetime('created', 3)
                ->comment('作成日');

            $table->datetime('updated', 3)
                ->nullable()
                ->comment('更新日');

            $table->datetimes(3);

            $table->softDeletesDatetime('deleted_at', 3);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_payments');
    }
};
