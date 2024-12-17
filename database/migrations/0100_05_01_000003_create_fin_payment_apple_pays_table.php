<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_payment_apple_pays', function (Blueprint $table) {
            $table->comment('Fincode ApplePay決済データ');

            $table->ulid('id')
                ->primary()
                ->comment('#ID');

            $table->binary('card_id', 25)
                ->nullable()
                ->comment('カードID');

            $table->string('brand', 16)
                ->nullable()
                ->comment('カードブランド');

            $table->string('card_no', 16)
                ->nullable()
                ->comment('カード番号(マスク済み');

            $table->date('expire')
                ->nullable()
                ->comment('カード有効期限');

            $table->string('holder_name', 50)
                ->nullable()
                ->comment('カード名義人名');

            $table->string('card_no_hash', 64)
                ->nullable()
                ->comment('カードハッシュ値');

            $table->char('method', 1)
                ->comment('支払方法');

            $table->integer('pay_times')
                ->nullable()
                ->unsigned()
                ->comment('支払回数');

            $table->string('forward', 7)
                ->nullable()
                ->comment('仕向け先');

            $table->string('issuer', 7)
                ->nullable()
                ->comment('イシュア');

            $table->binary('transaction_id', 28)
                ->nullable()
                ->comment('トランザクションID');

            $table->string('approve', 7)
                ->nullable()
                ->comment('承認番号');

            $table->dateTime('auth_max_date', 3)
                ->nullable()
                ->comment('仮売上有効期限');

            $table->string('item_code', 7)
                ->nullable()
                ->comment('商品コード');

            $table->dateTime('created', 3)
                ->comment('作成日');

            $table->dateTime('updated', 3)
                ->nullable()
                ->comment('更新日');

            $table->datetimes(3);

            $table->softDeletesDatetime('deleted_at', 3);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_payment_apple_pays');
    }
};
