<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_shops', function (Blueprint $table) {
            $table->comment('Fincode ショップ情報');

            $table->char('id', 13)
                ->primary()
                ->comment('ショップID');

            $table->string('shop_name', 20)
                ->comment('ショップ名');

            $table->string('shop_type', 16)
                ->nullable()
                ->comment('ショップタイプ');

            $table->string('platform_id', 13)
                ->index()
                ->comment('プラットフォームID');

            $table->string('platform_name', 50)
                ->comment('プラットフォーム名');

            $table->boolean('shared_customer_flag')
                ->default(false)
                ->comment('顧客情報共有フラグ');

            $table->string('customer_group_id', 13)
                ->nullable()
                ->index()
                ->comment('顧客情報共有グループID');

            $table->text('platform_rate_list')
                ->comment('プラットフォーム手数料リスト');

            $table->string('send_mail_address', 255)
                ->nullable()
                ->comment('通知先メールアドレス');

            $table->string('shop_mail_address', 255)
                ->nullable()
                ->comment('ショップメールアドレス');

            $table->integer('log_keep_days')
                ->nullable()
                ->unsigned()
                ->comment('ログ保存日数');

            $table->string('api_version', 8)
                ->nullable()
                ->comment('APIバージョン');

            $table->boolean('api_key_display_flag')
                ->nullable()
                ->comment('APIキー表示フラグ');

            $table->dateTime('created', 3)
                ->comment('プラットフォーム上の作成日時');

            $table->dateTime('updated', 3)
                ->nullable()
                ->comment('プラットフォーム上の更新日時');

            $table->datetimes(3);

            $table->softDeletesDatetime('deleted_at', 3);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_shops');
    }
};
