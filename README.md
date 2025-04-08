# 概要

Laravel上でFincodeを動かすためのライブラリです。

# セットアップ

必要であれば設定ファイルをパブリッシュしてください。

```bash
php ./artisan vendor:publish --tag=laravel-fincode-config
```

データベースのマイグレーションファイルをコピーします

```bash
php ./artisan vendor:publish --tag=laravel-fincode-migrations
```

`database/migrations` にマイグレーションファイルが複製されます。
不必要なものは消しても問題ありません。

| テーブル名                  | モデル名               | 親モデル                                |
|------------------------|--------------------|-------------------------------------|
| fin_histories          | FinHistory         |                                     |
| fin_customers          | FinCustomer        | FinHistory                          |
| fin_cards              | FinCard            | FinCustomer, FinHistory             |
| fin_payments           | FinPayment         | FinCustomer, FinHistory             |
| fin_payment_cards      | FinCard            | FinCustomer, FinHistory, FinPayment |
| fin_payment_konbinis   | FinPaymentKonbini  | FinCustomer, FinHistory, FinPayment |
| fin_payment_apple_pays | FinPaymentApplePay | FinCustomer, FinHistory, FinPayment |
| fin_webhooks           | FinWebhook         |                                     |

