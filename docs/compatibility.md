# Laravel 9 / 13 の応答結合の互換性

## 変更内容

決済登録時に未確定の支払情報を受け取れるよう、FinModelBinding の規則を変更した。

- カード決済: method を nullable に変更。指定された値の Enum 検証は維持。
- Apple Pay: カード詳細を nullable に変更し、応答の brand を検証。method にも nullable + Enum を指定。
- sanitize() の検証方法、既存モデルとのマージ処理、DB 定義、Composer の対応バージョン指定は変更していない。
- ショップ名の nullable 対応は DB 移行も必要なため、この変更には含めていない。

## 実行済みの確認

| Laravel | PHP | Testbench | PHPUnit | 結果 |
| --- | --- | --- | --- | --- |
| 9.52.23 | 8.3.35 | 7.58.0 | 9.6.38 | 13 tests / 91 assertions 成功 |
| 13.38.0 | 8.5.11 | 11.3.0 | 13.4.4 | 13 tests / 91 assertions 成功 |

Laravel 9 は別フォルダーに Composer で構築した専用の vendor を使用した。前回の調査で行った Illuminate ソースの差し替えや Carbon の回避設定は使っていない。SDK は両環境とも 20241017.2.2。

検証範囲:
- カード決済登録時の method 欠落・明示 null
- 配列・JSON・SDK 応答オブジェクトの受け入れ
- create/retrieve/capture の応答クラスと、実際のクライアントが使う共通応答クラス
- retrieve/capture 時の既存支払方法、ブランド、名義人名の保持と関連モデルの再利用
- 不正な method/brand の拒否
- Apple Pay の登録・取得・売上確定でカード詳細が未設定の応答
- 顧客・登録カードの任意項目が欠落または null の応答
- 既存のカード変更履歴と履歴モデルの初期化

## 継続的な確認

`.github/workflows/fin-model-binding.yml` に次のジョブを追加した。

- Laravel 9.52 系 / PHP 8.1
- Laravel 9.52 系 / PHP 8.3
- Laravel 13.38 系 / PHP 8.5

各ジョブで対応する Testbench/PHPUnit をインストールし、同じ `phpunit.compatibility.xml` を実行する。SDK は今回確認した 20241017.2.2 に固定する。SDK の公開リポジトリーには HTTPS で接続する。

Laravel 9 を依存解決できるよう、履歴バージョン用のジョブ内に限って Composer の advisory によるインストールブロックを解除している。製品側の composer.json は変更しない。

この作業では GitHub 上のジョブは未実行。PHP 8.1 の実行確認は CI 実行後になる。

## 再実行

現在の vendor で実行:

```sh
vendor/bin/phpunit --configuration phpunit.compatibility.xml
```

別フォルダーの Laravel 9 環境で同じソースを検証する場合、その Composer の PSR-4 設定から本リポジトリーの src / tests / database/factories を参照させ、対応する PHPUnit と autoload を使う:

```sh
php8.3 /path/to/laravel9/vendor/bin/phpunit \
  --configuration phpunit.compatibility.xml \
  --bootstrap /path/to/laravel9/vendor/autoload.php
```

## 保証範囲

今回変更した応答結合について、上記の Laravel 9.52.23 と 13.38.0 で互換性を確認した。Laravel 9 の全マイナーバージョン、全 PHP バージョン、外部 API を伴う全機能の稼働を一括して保証するものではない。

テストは SQLite :memory: と合成した SDK 応答を使用し、fincode の実 API は呼ばない。実際の注文完了を保証するには、利用アプリと fincode テスト環境を接続した確認が別途必要。既存の外部 API テストは実行していない。
