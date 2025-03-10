<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_payment_cards', function (Blueprint $table) {
            $table->comment('Fincode カード決済データ');

            $table->ulid('id')
                ->primary()
                ->comment('#ID');

            $table->string('card_id', 25)
                ->charset('binary')
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
                ->nullable()
                ->comment('支払方法');

            $table->integer('pay_times')
                ->nullable()
                ->unsigned()
                ->comment('支払回数');

            $table->string('bulk_payment_id', 25)
                ->charset('binary')
                ->nullable()
                ->comment('一括決済ID');

            $table->string('subscription_id', 25)
                ->charset('binary')
                ->nullable()
                ->comment('サブスクリプションID');

            $table->char('tds_type', 1)
                ->nullable()
                ->comment('3Dセキュア認証利用');

            $table->char('tds2_type', 1)
                ->nullable()
                ->comment('3Dセキュア2.0非対応時の挙動設定');

            $table->string('tds2_ret_url', 256)
                ->nullable()
                ->comment('3Dセキュア認証における戻りURL');

            $table->string('return_url', 256)
                ->nullable()
                ->comment('加盟店戻りURL(成功時)');

            $table->string('return_url_on_failure', 256)
                ->nullable()
                ->comment('加盟店戻りURL(失敗時)');

            $table->string('tds2_status', 16)
                ->nullable()
                ->comment('3Dセキュア2.0認証処理 ステータス');

            $table->string('merchant_name', 25)
                ->nullable()
                ->comment('加盟店名');

            $table->string('forward', 7)
                ->nullable()
                ->comment('仕向け先');

            $table->string('issuer', 7)
                ->nullable()
                ->comment('イシュア');

            $table->string('transaction_id', 28)
                ->charset('binary')
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

            $table->text('acs_url')
                ->nullable()
                ->comment('3Dセキュア認証初期化URL');

            $table->text('redirect_url')
                ->nullable()
                ->comment('リダイレクトURL');

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
        Schema::dropIfExists('fin_payment_cards');
    }
};
