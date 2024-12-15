<?php

declare(strict_types=1);

namespace Fincode\Laravel\Http\Request;

use Fincode\Laravel\Eloquent\PlatformRateObject;
use Fincode\Laravel\Exceptions\FincodeApiException;
use Fincode\Laravel\Exceptions\FincodeUnknownResponseException;
use Fincode\Laravel\Models\FinShop;
use GuzzleHttp\Exception\GuzzleException;
use OpenAPI\Fincode\ApiException;
use OpenAPI\Fincode\Model\PlatformAccountRetrievingResponse;
use OpenAPI\Fincode\Model\PlatformRateConfig;
use OpenAPI\Fincode\Model\PlatformShopUpdatingResponse;

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
     *
     * @throws FincodeUnknownResponseException
     */
    public function get(FinShop|string $shop): FinShop
    {
        $shop_id = $shop instanceof FinShop ? $shop->id : $shop;

        try {
            $response = $this->token->default()
                ->retrievePlatformAccount($shop_id);
        } catch (GuzzleException|ApiException $e) {
            throw new FincodeApiException($e);
        }

        if ($response instanceof PlatformAccountRetrievingResponse) {
            return $this->binding->shop($response);
        }

        throw new FincodeUnknownResponseException;
    }

    /**
     * プラットフォームショップ 更新
     *
     * @throws FincodeUnknownResponseException
     */
    public function update(FinShop|string $shop, PlatformRateObject $rate): FinShop
    {
        $shop_id = $shop instanceof FinShop ? $shop->id : $shop;

        $body = new PlatformRateConfig($this->binding->castArray($rate));

        try {
            $response = $this->token->default()
                ->updatePlatformShop($shop_id, $body);
        } catch (GuzzleException|ApiException $e) {
            throw new FincodeApiException($e);
        }

        if ($response instanceof PlatformShopUpdatingResponse) {
            return $this->binding->shop($response);
        }

        throw new FincodeUnknownResponseException;
    }
}
