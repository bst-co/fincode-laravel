<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_customers', function (Blueprint $table) {
            $table->comment('Fincode 顧客データ');

            $table->string('id', 60)
                ->charset('binary')
                ->primary()
                ->comment('#顧客ID');

            $table->string('name', 384)
                ->nullable()
                ->comment('顧客の名前');

            $table->string('email', 254)
                ->nullable()
                ->comment('顧客のメールアドレス');

            $table->string('phone_cc', 3)
                ->nullable()
                ->comment('顧客の電話番号の国コード');

            $table->string('phone_no', 15)
                ->nullable()
                ->comment('顧客の電話番号');

            $table->string('addr_country', 3)
                ->nullable()
                ->comment('顧客の住所の国コード(ISO 3166-1 numeric)');

            $table->string('addr_state', 3)
                ->nullable()
                ->comment('顧客の住所の州コードまたは都道府県コード');

            $table->string('addr_city', 50)
                ->nullable()
                ->comment('顧客の住所の都市名');

            $table->string('addr_line_1', 50)
                ->nullable()
                ->comment('顧客の住所の番地・区画');

            $table->string('addr_line_2', 50)
                ->nullable()
                ->comment('顧客の住所の建物名・部屋番号');

            $table->string('addr_line_3', 50)
                ->nullable()
                ->comment('顧客の住所 その他');

            $table->string('addr_post_code', 16)
                ->nullable()
                ->comment('顧客の住所の郵便番号');

            $table->boolean('card_registration')
                ->default(false)
                ->comment('決済手段（カード）登録状況');

            $table->boolean('directdebit_registration')
                ->default(false)
                ->comment('決済手段（口座振替）登録状況');

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
        Schema::dropIfExists('fin_customers');
    }
};
