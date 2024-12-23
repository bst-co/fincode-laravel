<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_cards', function (Blueprint $table) {
            $table->comment('Fincode 顧客カードデータ');

            $table->string('id', 25)
                ->charset('binary')
                ->primary()
                ->comment('#カードID (c_**)');

            $table->string('customer_id', 60)
                ->charset('binary')
                ->index()
                ->comment('Customer ID(cs_**)');

            $table->boolean('default_flag')
                ->default(false)
                ->comment('Default Flag');

            $table->string('card_no', 16)
                ->comment('Card No (Masked)');

            $table->date('expire')
                ->nullable()
                ->comment('有効期限');

            $table->string('holder_name', 50)
                ->nullable()
                ->comment('カード名義人名');

            $table->string('type', 16)
                ->nullable()
                ->comment('カード種別');

            $table->string('brand', 50)
                ->nullable()
                ->comment('カードブランドコード');

            $table->string('card_no_hash', 64)
                ->comment('カード番号ハッシュ値');

            $table->datetime('created', 3)
                ->comment('プラットフォーム上の作成日時');

            $table->datetime('updated', 3)
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
        Schema::dropIfExists('fin_cards');
    }
};
