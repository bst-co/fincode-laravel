<?php

declare(strict_types=1);

namespace Fincode\Laravel\Clients;

use Fincode\Laravel\Events\FincodeRequestTokenEvent;
use Fincode\Laravel\Exceptions\FincodeRequestException;
use Fincode\Laravel\Models\FinShop;
use Fincode\Laravel\Models\FinShopToken;
use Fincode\OpenAPI\Api\DefaultApi;
use Fincode\OpenAPI\Api\WebhookApi;
use Fincode\OpenAPI\Configuration;
use Fincode\OpenAPI\HeaderSelector;
use Fincode\OpenAPI\Model\Shop;
use Fincode\OpenAPI\Model\ShopType;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

/**
 * @template TToken of (string|FinShop|FinShopToken|null)
 */
class FincodeRequestToken
{
    /**
     * テナントのメインショップである場合に、ショップIDを参照できます
     */
    public readonly ?string $main_tenant_id;

    /**
     * 顧客情報を共有しないプラットフォームのメインショップである場合、ショップIDを参照できます
     */
    public readonly ?string $private_tenant_id;

    /**
     * @param  ShopType|null  $mode  ショップモード
     * @param  string  $shop_id  スタンダード/プラットフォーム(メイン/サブ) のショップID
     * @param  string|null  $tenant_id  テナントID
     * @param  string|null  $tenant_name  Dセキュア時に表示される店舗名 (半角英数/半角スペース/半角記号のみ, 13文字以内)
     * @param  string  $secret_key  サーバーAPIで使用するAPIキー
     * @param  string  $public_key  クライアントAPIで使用するAPIキー
     * @param  bool  $shared_customer  プラットフォームが顧客を共有するタイプかどうか
     * @param  bool|null  $live  本番環境利用フラグ
     * @param  string|null  $client_field_1  決済情報に適用する加盟店自由項目 (client_field_1に適用される) 加盟店が自由に設定できる項目として使用
     * @param  string|null  $client_field_3  決済情報に適用する加盟店自由項目 (client_field_3に適用される) 決済経路を示すためにシステム利用
     * @param  bool|null  $debug  デバッグモードの利用有無
     */
    protected function __construct(
        public readonly ?ShopType $mode,
        public readonly string $shop_id,
        public readonly ?string $tenant_id,
        public readonly ?string $tenant_name,
        public readonly string $secret_key,
        public readonly string $public_key,
        public readonly bool $shared_customer,
        public readonly ?string $client_field_1 = null,
        public readonly ?string $client_field_3 = null,
        public readonly bool $live = false,
        public readonly bool $debug = false,
    ) {
        $this->main_tenant_id = null;

        $this->private_tenant_id = $this->shared_customer ? null : $this->tenant_id;
    }

    /**
     * @param  string|null  $token  接続に使用するトークン情報
     *                              <ul>
     *                              <li>FinShopToken: オブジェクトの値に基づき、接続情報を構築する</li>
     *                              <li>FinShop: オブジェクトに紐づく FinShopToken を取得して接続情報を構築する</li>
     *                              <li>string: "shop_id" として FinShopToken、またはコンフィグ設定から 'fincode.platforms.***' を取得する</li>
     *                              <li>null: システムのデフォルト設定から接続情報を取得する</li>
     *                              </ul>
     * @param  string|FinShop|null  $tenant  テナントIDをオーバーライドする場合に指定してください (platformモード時のみ有効)
     * @param  ?bool  $live  Fincodeの接続先を強制的に設定できます。
     *                       <ul>
     *                       <li>true = 本番環境に接続</li>
     *                       <li>false = テスト環境に接続</li>
     *                       <li>null = 各種設定、またはシステム初期値を使用します</li>
     *                       </ul>
     * @return FincodeRequestToken
     *
     * @throws FincodeRequestException
     */
    public static function make(
        ?string $token = null,
        string|FinShop|null $tenant = null,
        ?bool $live = null,
        ?bool $debug = null
    ): static {
        $token = $token ?? config('fincode.default');

        if ($tenant instanceof FinShop) {
            if (empty($tenant->id)) {
                throw new FincodeRequestException('Tenant is not ID', 4);
            }

            if ($tenant->shop_type !== ShopType::TENANT) {
                throw new FincodeRequestException("Tenant '{$tenant->id}' is not tenant.", 3);
            }
        }

        $debug = (bool) ($debug ?? config('fincode.options.debug'));

        $platform = data_get(config('fincode.platforms'), $token);

        if (empty($platform) || ! is_array($platform)) {
            throw new FincodeRequestException("Config 'fincode.platforms.$token' is not found.", 1);
        }

        $live = (bool) ($live ?? data_get($platform, 'live') ?? config('fincode.options.live') ?? false);

        $mode = match ($platform['mode'] ?? 'standard') {
            'tenant' => ShopType::TENANT,
            'platform' => ShopType::PLATFORM,
            'standard' => null,
            default => throw new FincodeRequestException("Config 'fincode.platforms.$token.mode' = '{$platform['mode']}' is unknown.", 2),
        };

        if ($mode === ShopType::PLATFORM) {
            if ($tenant instanceof FinShop) {
                $tenant_id = $tenant->id;
            } elseif ($tenant !== null) {
                $tenant_id = $tenant;
            } else {
                $tenant_id = data_get($platform, 'tenant_id');
            }
        } elseif ($mode === ShopType::TENANT) {
            $tenant_id = data_get($platform, 'tenant_id');
        } else {
            $tenant_id = null;
        }

        if ($mode === ShopType::TENANT && empty($tenant_id)) {
            throw new FincodeRequestException("Config 'fincode.platforms.$token' is Tenant mode, but there is not tenant_id", 4);
        }

        $shared_customer = match ($mode) {
            ShopType::PLATFORM,
            ShopType::TENANT => (bool) data_get($platform, 'shared_customer', false),
            default => false,
        };

        return new static(
            mode: $mode,
            shop_id: data_get($platform, 'shop_id'),
            tenant_id: $tenant_id,
            tenant_name: data_get($platform, 'tenant_name'),
            secret_key: data_get($platform, 'secret_key'),
            public_key: data_get($platform, 'public_key'),
            shared_customer: $shared_customer,
            client_field_1: data_get($platform, 'client_field'),
            client_field_3: "config:fincode.platforms.{$token}",
            live: $live,
            debug: $debug,
        );
    }

    /**
     * イベント発火による、トークンの更新を試みる
     *
     * @deprecated
     */
    private static function token(FinShopToken|string|FinShop|null $token): FinShopToken|string|FinShop|null
    {
        [$response] = array_pad(event(new FincodeRequestTokenEvent($token)), 1, null);

        if (is_bool($response)) {
            return $response ? $token : null;
        }

        if (is_string($response) || is_null($response) || $response instanceof FinShopToken || $response instanceof FinShop) {
            return $response;
        }

        return $token;
    }

    /**
     * APIが使用する接続先ホストを取得
     */
    public function host(): string
    {
        return $this->config()->getHost();
    }

    /**
     * APIの通信設定を作成
     */
    public function config(): Configuration
    {
        $config = new Configuration;

        $config->setHost($config->getHostFromSettings($this->live ? 0 : 1))
            ->setDebug($this->debug)
            ->setUserAgent(config('fincode.options.user_agent', $config->getUserAgent()))
            ->setAccessToken($this->secret_key);

        return $config;
    }

    /**
     * Fincode デフォルトAPIオブジェクトを返却
     */
    public function default(
        ?ClientInterface $client = new Client,
        ?Configuration $config = null,
        ?HeaderSelector $selector = new HeaderSelector,
    ): DefaultApi {
        return new DefaultApi($client, $config ?? $this->config(), $selector);
    }

    /**
     * Fincode WebhookAPIオブジェクトを返却
     */
    public function webhook(
        ?ClientInterface $client = new Client,
        ?Configuration $config = null,
        ?HeaderSelector $selector = new HeaderSelector,
    ): WebhookApi {
        return new WebhookApi($client, $config ?? $this->config(), $selector);
    }
}
