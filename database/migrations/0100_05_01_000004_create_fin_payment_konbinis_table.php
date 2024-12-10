<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_payment_konbinis', function (Blueprint $table) {
            $table->comment('Fincode コンビニ決済データ');

            $table->ulid('id')
                ->primary()
                ->comment('#ID');

            $table->string('payment_id', 30)
                ->index()
                ->comment('#オーダーID');

            $table->bigInteger('payment_term_day')
                ->unsigned()
                ->nullable()
                ->comment('支払い期限日数');

            $table->dateTime('payment_term', 3)
                ->nullable()
                ->comment('支払い期限日時');

            $table->dateTime('payment_date', 3)
                ->nullable()
                ->comment('支払日時');

            $table->text('barcode')
                ->nullable()
                ->comment('バーコード画像 Base64エンコード済み画像データ');

            $table->string('barcode_format', 6)
                ->nullable()
                ->comment('バーコード画像 フォーマット');

            $table->string('barcode_width', 9)
                ->nullable()
                ->comment('バーコード画像 横幅(px)');

            $table->string('barcode_height', 9)
                ->nullable()
                ->comment('バーコード画像 縦幅(px)');

            $table->char('overpayment_flag', 1)
                ->comment('多重支払フラグ');

            $table->char('cancel_overpayment_flag', 1)
                ->comment('キャンセル後支払フラグ');

            $table->string('konbini_code', 5)
                ->nullable()
                ->comment('コンビニ事業者コード');

            $table->string('konbini_store_code', 8)
                ->nullable()
                ->comment('コンビニ店舗コード');

            $table->string('device_name', 20)
                ->nullable()
                ->comment('デバイス名');

            $table->string('os_version', 10)
                ->nullable()
                ->comment('OSバージョン');

            $table->integer('win_width')
                ->unsigned()
                ->nullable()
                ->comment('デバイス画面幅');

            $table->integer('win_height')
                ->unsigned()
                ->nullable()
                ->comment('デバイス画面の高さ');

            $table->integer('xdpi')
                ->unsigned()
                ->nullable()
                ->comment('画面横幅のDPI');

            $table->integer('ydpi')
                ->unsigned()
                ->nullable()
                ->comment('画面縦幅のDPI');

            $table->string('result', 3)
                ->comment('コンビニ事業者の決済処理結果コード');

            $table->string('order_serial', 18)
                ->nullable()
                ->comment('注文管理ID');

            $table->string('invoice_id', 20)
                ->nullable()
                ->comment('請求ID');

            $table->dateTime('created', 3)
                ->comment('作成日');

            $table->dateTime('updated', 3)
                ->nullable()
                ->comment('更新日');

            $table->dateTime('created_at', 3);

            $table->softDeletesDatetime('deleted_at', 3);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_payment_konbinis');
    }
};
