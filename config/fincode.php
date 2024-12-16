<?php

return [
    /*
     * ------------------------------------------------------------
     * Default Platform Prop
     * ------------------------------------------------------------
     */
    'default' => 'default',

    /*
     * ------------------------------------------------------------
     * Platforms Props
     * ------------------------------------------------------------
     */
    'platforms' => [
        'default' => [
            // Fincodeに登録されているテナントIDを指定してください (必須)
            'shop_id' => env('FINCODE_TENANT_ID', ''),
            // このテナントで3Dセキュアを行う際に表示される、店舗名
            // デフォルトでは、テナントIDが表示されます
            'tenant_name' => env('FINCODE_TENANT_NAME', null),
            // Fincodeにてテナントに割り当てられている公開APIキーを適用 (必須)
            'public_key' => env('FINCODE_PUBLIC_KEY', ''),
            // Fincodeにてテナントに割り当てられている秘密APIキーを適用 (必須)
            'secret_key' => env('FINCODE_SECRET_KEY', ''),
            'client_field' => env('FINCODE_CLIENT_FIELD'),
            // Fincodeの本番環境利用フラグ / デフォルト設定のままにするには null にしてください
            'live' => null,
        ],
    ],

    /*
     * ------------------------------------------------------------
     * Config Optional
     * ------------------------------------------------------------
     */
    'options' => [
        // Fincodeに送信するUserAgentを設定する、空の場合はデフォルト値が使用されます
        'user_agent' => env('FINCODE_USER_AGENT'),
        // Fincode で3Dセキュアを実施する際の認証有効期限(秒数)
        // 未使用の場合はFincodeデフォルトの30分が適用されます
        '3d_secure_ttl' => env('FINCODE_3D_SECURE_TTL', null),
        // Fincodeの本番環境利用フラグ / テスト環境を使う場合は false に設定
        'live' => env('FINCODE_LIVE_MODE', env('APP_ENV') === 'production'),
    ],

    'route' => [
        'domain' => env('FINCODE_DOMAIN'),
        'path' => env('FINCODE_PATH', 'fincode'),
        'middleware' => env('FINCODE_MIDDLEWARE', 'api'),
    ],

    'dummies' => [
        'card' => [
            [
                'label' => '正常カード（VISA）',
                'number' => '4111 1111 1111 1111',
            ],
            [
                'label' => '正常カード（MASTER）',
                'number' => '5111 1111 1111 1111',
            ],
            [
                'label' => '正常カード（JCB）',
                'number' => '3531 1111 1111 1111',
            ],
            [
                'label' => '正常カード（AMEX）',
                'number' => '3711 111111 11111',
            ],
            [
                'label' => '正常カード（DINERS）',
                'number' => '3011 111111 1111',
            ],
            [
                'label' => '正常カード（Discover）',
                'number' => '6011 0111 1111 1111',
            ],
            [
                'label' => '残高不足',
                'number' => '4999 0000 0000 0002',
            ],
            [
                'label' => '限度額オーバー',
                'number' => '4999 0000 0000 0005',
            ],
            [
                'label' => '取扱不可',
                'number' => '4999 0000 0000 0012',
            ],
            [
                'label' => '保留判定',
                'number' => '4999 0000 0000 0030',
            ],
            [
                'label' => '暗証番号エラー',
                'number' => '4999 0000 0000 0042',
            ],
            [
                'label' => 'セキュリティコード誤り',
                'number' => '4999 0000 0000 0044',
            ],
            [
                'label' => '事故カード',
                'number' => '4999 0000 0000 0060',
            ],
            [
                'label' => '無効カード',
                'number' => '4999 0000 0000 0061',
            ],
            [
                'label' => 'カード番号誤り',
                'number' => '4999 0000 0000 0065',
            ],
            [
                'label' => '金額誤り',
                'number' => '4999 0000 0000 0068',
            ],
            [
                'label' => '支払開始月誤り',
                'number' => '4999 0000 0000 0073',
            ],
            [
                'label' => '分割回数誤り',
                'number' => '4999 0000 0000 0074',
            ],
            [
                'label' => '分割金額誤り',
                'number' => '4999 0000 0000 0075',
            ],
            [
                'label' => '有効期限誤り',
                'number' => '4999 0000 0000 0083',
            ],
            [
                'label' => '3DS 認証成功(VISA:1)',
                'number' => '4100 0000 0000 0100',
            ],
            [
                'label' => '3DS 認証成功(VISA:2)',
                'number' => '4100 0000 0000 0118',
            ],
            [
                'label' => '3DS チャレンジ要求されず認証失敗(VISA:N)',
                'number' => '4100 0000 0020 0007',
            ],
            [
                'label' => '3DS チャレンジ要求されず認証失敗(VISA:U)',
                'number' => '4100 0000 0040 0003',
            ],
            [
                'label' => '3DS チャレンジ要求されず認証失敗(VISA:R)',
                'number' => '4100 0000 0050 0000',
            ],
            [
                'label' => '3DS 本人認証画面の代替としてチャレンジ要求(VISA:C-Y)',
                'number' => '4100 0000 0000 5000',
                'description' => '「決済に進む」を押下すると認証成功します。',
            ],
            [
                'label' => '3DS 本人認証画面の代替としてチャレンジ要求(VISA:C→N)',
                'number' => '4100 0000 0030 0005',
                'description' => '「決済に進む」を押下すると認証失敗します。',
            ],
            [
                'label' => '3DS 認証成功(MASTER:1)',
                'number' => '5100 0000 0000 0107',
            ],
            [
                'label' => '3DS 認証成功(MASTER:2)',
                'number' => '5100 0000 0000 0115',
            ],
            [
                'label' => '3DS チャレンジ要求されず認証失敗(MASTER:N)',
                'number' => '5100 0000 0020 0004',
            ],
            [
                'label' => '3DS チャレンジ要求されず認証失敗(MASTER:U)',
                'number' => '5100 0000 0040 0000',
            ],
            [
                'label' => '3DS チャレンジ要求されず認証失敗(MASTER:R)',
                'number' => '5100 0000 0050 0007',
            ],
            [
                'label' => '3DS 本人認証画面の代替としてチャレンジ要求(MASTER:C-Y)',
                'number' => '5100 0000 0000 5007',
            ],
            [
                'label' => '3DS 本人認証画面の代替としてチャレンジ要求(MASTER:C-N)',
                'number' => '5100 0000 0030 0002',
            ],
            [
                'label' => '3Dセキュア非対応カードによる認証試行 (VISA)',
                'number' => '4100 0000 0010 0009',
                'description' => 'テストカードの利用にあたっては「認証結果Aのテストカードの注意事項」を参照',
            ],
            [
                'label' => '3Dセキュア非対応カードによる認証試行 (MASTER)',
                'number' => '5100 0000 0010 0006',
                'description' => 'テストカードの利用にあたっては「認証結果Aのテストカードの注意事項」を参照',
            ],
        ],
    ],
];
