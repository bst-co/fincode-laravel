<?php

namespace Fincode\Laravel\Http\Request;

use Fincode\Laravel\Eloquent\PlatformRateObject;
use Fincode\Laravel\Exceptions\FincodeApiException;
use Fincode\Laravel\Exceptions\FincodeUnknownResponseException;
use Fincode\Laravel\Models\FinShop;
use GuzzleHttp\Exception\GuzzleException;
use OpenAPI\Fincode\ApiException;
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
     *
     * @throws FincodeUnknownResponseException
     */
    public function get(FinShop|string $tenant, bool $save = true): FinShop
    {
        $tenant_id = $tenant instanceof FinShop ? $tenant->id : $tenant;

        try {
            $response = $this->token->default()
                ->retrieveTenantShop($tenant_id);
        } catch (GuzzleException|ApiException $e) {
            throw new FincodeApiException($e);
        }

        if ($response instanceof TenantShopRetrievingResponse) {
            $model = $this->binding->shop($response);

            if ($save) {
                $model->save();
            }

            return $model;
        }

        throw new FincodeUnknownResponseException;
    }

    /**
     * テナントショップ 更新
     *
     * @throws FincodeUnknownResponseException
     */
    public function update(FinShop|string $tenant, PlatformRateObject $rate, bool $save = true): FinShop
    {
        $tenant = FinShop::find($tenant);

        $body = new PlatformRateConfig($this->binding->castArray($rate));

        try {
            $response = $this->token->default()
                ->updateTenantShop($tenant->id, $body);
        } catch (GuzzleException|ApiException $e) {
            throw new FincodeApiException($e);
        }

        if ($response instanceof TenantShopUpdatingResponse) {
            $model = $this->binding->shop($response);

            if ($save) {
                $model->save();
            }

            return $model;
        }

        throw new FincodeUnknownResponseException;
    }
}
