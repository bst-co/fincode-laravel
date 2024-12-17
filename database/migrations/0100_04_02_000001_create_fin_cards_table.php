<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_cards', function (Blueprint $table) {
            $table->binary('id', 25)
                ->primary()
                ->comment('#カードID (c_**)');

            $table->binary('customer_id', 60)
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

            $table->datetime('created', 3);
            $table->datetime('updated', 3);

            $table->datetimes(3);

            $table->softDeletesDatetime('deleted_at', 3);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_cards');
    }
};
