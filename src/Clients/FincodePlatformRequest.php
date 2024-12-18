<?php

declare(strict_types=1);

namespace Fincode\Laravel\Clients;

use Fincode\Laravel\Eloquent\PlatformRateObject;
use Fincode\Laravel\Exceptions\FincodeUnknownResponseException;
use Fincode\Laravel\Models\FinShop;
use Fincode\OpenAPI\Model\PlatformRateConfig;
use Fincode\OpenAPI\Model\PlatformShopRetrievingResponse;
use Fincode\OpenAPI\Model\PlatformShopUpdatingResponse;

/**
 * FincodeプラットフォームショップAPI
 *
 * @see https://docs.fincode.jp/api#tag/%E3%83%97%E3%83%A9%E3%83%83%E3%83%88%E3%83%95%E3%82%A9%E3%83%BC%E3%83%A0%E3%82%B7%E3%83%A7%E3%83%83%E3%83%97
 */
class FincodePlatformRequest extends FincodeAbstract
{
    /**
     * プラットフォームショップ 一覧取得
     *
     * @throws FincodeUnknownResponseException
     */
    public function index()
    {
        throw new FincodeUnknownResponseException;
    }

    /**
     * プラットフォームショップ 取得
     */
    public function retrieve(FinShop|string $shop): FinShop
    {
        $shop_id = $shop instanceof FinShop ? $shop->id : $shop;

        $response = $this->dispatch(
            PlatformShopRetrievingResponse::class,
            fn () => $this->token->default()->retrievePlatformShop($shop_id)
        );

        return $this->binding->shop($response);
    }

    /**
     * プラットフォームショップ 更新
     */
    public function update(FinShop|string $shop, PlatformRateObject $rate): FinShop
    {
        $shop_id = $shop instanceof FinShop ? $shop->id : $shop;

        $body = new PlatformRateConfig($this->binding->castArray($rate));

        $response = $this->dispatch(
            PlatformShopUpdatingResponse::class,
            fn () => $this->token->default()->updatePlatformShop($shop_id, $body)
        );

        return $this->binding->shop($response);
    }
}
