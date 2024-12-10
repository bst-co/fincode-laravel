<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_payment_method', function (Blueprint $table) {
            $table->ulid('id')
                ->primary()
                ->comment('#ID');

            $table->foreignUlid('payment_id', 30)
                ->constrained('fin_payments')
                ->restrictOnDelete();

            $table->ulidMorphs('payment_method');

            $table->datetime('updated', 3);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_payment_method');
    }
};
