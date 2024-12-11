<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_cards', function (Blueprint $table) {
            $table->ulid('id')
                ->primary()
                ->comment('#ID');

            $table->string('card_id', 25)
                ->index()
                ->comment('Card ID (c_**)');

            $table->foreignUlid('customer_id')
                ->comment('Customer ID(cs_**)');

            $table->boolean('default_flag')
                ->default(false)
                ->comment('Default Flag');

            $table->string('card_no', 16)
                ->comment('Card No');

            $table->date('expire')
                ->comment('有効期限');

            $table->string('holder_name', 50)
                ->comment('カード名義人名');

            $table->string('brand', 50)
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
