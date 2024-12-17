<?php

namespace Fincode\Laravel\Http\Request;

use Fincode\Laravel\Eloquent\PlatformRateObject;
use Fincode\Laravel\Exceptions\FincodeUnknownResponseException;
use Fincode\Laravel\Models\FinShop;
use OpenAPI\Fincode\Model\PlatformRateConfig;
use OpenAPI\Fincode\Model\TenantShopRetrievingResponse;
use OpenAPI\Fincode\Model\TenantShopUpdatingResponse;

/**
 * FincodeテナントショップAPIリクエスト
 *
 * @see https://docs.fincode.jp/api#tag/%E3%83%86%E3%83%8A%E3%83%B3%E3%83%88%E3%82%B7%E3%83%A7%E3%83%83%E3%83%97
 */
class FincodeTenantRequest extends FincodeAbstract
{
    /**
     * テナントショップ 一覧取得
     *
     * @throws FincodeUnknownResponseException
     */
    public function index()
    {
        throw new FincodeUnknownResponseException;
    }

    /**
     * テナントショップ 取得
     */
    public function retrieve(FinShop|string $tenant): FinShop
    {
        $tenant_id = $tenant instanceof FinShop ? $tenant->id : $tenant;

        $response = $this->dispatch(
            TenantShopRetrievingResponse::class,
            fn () => $this->token->default()->retrieveTenantShop($tenant_id)
        );

        return $this->binding->shop($response);
    }

    /**
     * テナントショップ 更新
     *
     * @throws FincodeUnknownResponseException
     */
    public function update(FinShop|string $tenant, PlatformRateObject $rate): FinShop
    {
        $tenant = FinShop::find($tenant);

        $body = new PlatformRateConfig($this->binding->castArray($rate));

        $response = $this->dispatch(
            TenantShopUpdatingResponse::class,
            fn () => $this->token->default()->updateTenantShop($tenant->id, $body)
        );

        return $this->binding->shop($response);
    }
}
