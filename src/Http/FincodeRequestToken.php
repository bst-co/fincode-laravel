<?php

declare(strict_types=1);

namespace Fincode\Laravel\Http;

use Fincode\Laravel\Events\FincodeRequestTokenEvent;
use Fincode\Laravel\Exceptions\FincodeRequestException;
use Fincode\Laravel\Models\FinShop;
use Fincode\Laravel\Models\FinShopToken;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use OpenAPI\Fincode\Api\DefaultApi;
use OpenAPI\Fincode\Configuration;
use OpenAPI\Fincode\HeaderSelector;

/**
 * @template TToken of (string|FinShop|FinShopToken|null)
 */
readonly class FincodeRequestToken
{
    /**
     * 本番環境利用フラグ
     */
    public bool $live;

    /**
     * ショップID
     */
    public string $shop_id;

    /**
     * 3Dセキュア時に表示される店舗名
     */
    public ?string $tenant_name;

    /**
     * サーバーAPIで使用するAPIキー
     */
    public string $secret_key;

    /**
     * クライアントAPIで使用するAPIキー
     */
    public string $public_key;

    /**
     * 決済情報に適用する加盟店自由項目 (client_field_1に適用される)
     * 加盟店が自由に設定できる項目として使用する
     */
    public ?string $client_field_1;

    /**
     * 決済情報に適用する加盟店自由紅毛 (client_field_3に適用される)
     * 決済経路を示すためにシステム利用する
     */
    public ?string $client_field_3;

    /**
     * 顧客情報を共有しないプラットフォームのメインショップである場合、ショップIDを参照できます
     */
    public ?string $private_shop_id;

    /**
     * @param  FinShopToken  $token  接続先情報パック
     */
    protected function __construct(
        protected FinShopToken $token,
        ?bool $live = null,
        ?string $source = null,
        protected bool $debug = false,
    ) {
        $this->live = $live ?? $this->token->live ?? config('fincode.options.live') ?? false;

        $this->shop_id = $this->token->shop_id;
        $this->tenant_name = $this->token->tenant_name;
        $this->public_key = $this->token->public_key;
        $this->secret_key = $this->token->secret_key;

        $this->client_field_1 = $this->token->client_field;
        $this->client_field_3 = $this->token->exists ? $this->token->getTable().':'.$this->token->id : $source;

        $this->private_shop_id = $this->token->shop?->is_private_shop ? $this->token->shop_id : null;
    }

    /**
     * @param  FinShopToken|string|FinShop|null  $token  接続に使用するトークン情報
     *                                                   <ul>
     *                                                   <li>FinShopToken: オブジェクトの値に基づき、接続情報を構築する</li>
     *                                                   <li>FinShop: オブジェクトに紐づく FinShopToken を取得して接続情報を構築する</li>
     *                                                   <li>string: "shop_id" として FinShopToken、またはコンフィグ設定から 'fincode.platforms.***' を取得する</li>
     *                                                   <li>null: システムのデフォルト設定から接続情報を取得する</li>
     *                                                   </ul>
     * @param  ?bool  $live  Fincodeの接続先を強制的に設定できます。
     *                       <ul>
     *                       <li>true = 本番環境に接続</li>
     *                       <li>false = テスト環境に接続</li>
     *                       <li>null = 各種設定、またはシステム初期値を使用します</li>
     *                       </ul>
     *
     * @throws FincodeRequestException
     */
    public static function make(FinShopToken|string|FinShop|null $token = null, ?bool $live = null): static
    {
        $token = static::token($token) ?? $token ?? config('fincode.default');

        $live = $live ?: config('fincode.options.live');

        if ($token instanceof FinShop) {
            $token = $token->token();
        }

        if ($token instanceof FinShopToken) {
            return new static($token, $live);
        }

        if (is_string($token) && $token !== '') {
            if ($value = FinShopToken::whereShopId($token)->first()) {
                return new static($value, $live);
            }

            $config = "fincode.platforms.$token";

            if (($value = config($config))) {
                if (is_array($value) && isset($value['shop_id'], $value['public_key'], $value['secret_key'])) {
                    return new static((new FinShopToken)->forceFill([
                        'id' => $value['shop_id'],
                        'public_key' => $value['public_key'],
                        'secret_key' => $value['secret_key'],
                        'tenant_name' => $value['tenant_name'] ?? null,
                        'client_field' => $value['client_field'] ?? null,
                    ]), $value['live'] ?? $live, 'config:'.$config);
                }

                throw new FincodeRequestException("Config '$config' is missing required parameters.", 2);
            }

            throw new FincodeRequestException("Config '$config' is not found.", 1);
        }

        throw new FincodeRequestException('Invalid token type.', 0);
    }

    /**
     * イベント発火による、トークンの更新を試みる
     */
    private static function token(FinShopToken|string|FinShop|null $token): FinShopToken|string|FinShop|null
    {
        [$response] = event(new FincodeRequestTokenEvent($token));

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
        ?ClientInterface $client = null,
        ?Configuration $config = null,
        ?HeaderSelector $selector = null,
    ): DefaultApi {
        return new DefaultApi($client, $config ?? $this->config(), $selector);
    }
}
