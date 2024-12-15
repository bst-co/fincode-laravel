<?php

namespace Fincode\Laravel\Http\Request;

use Fincode\Laravel\Exceptions\FincodeRequestException;
use Fincode\Laravel\Exceptions\FincodeUnknownResponseException;
use Fincode\Laravel\Models\FinCustomer;
use GuzzleHttp\Exception\GuzzleException;
use OpenAPI\Fincode\ApiException;
use OpenAPI\Fincode\Model\CustomerCreatingRequest;
use OpenAPI\Fincode\Model\CustomerDeletingResponse;
use OpenAPI\Fincode\Model\CustomerRetrievingResponse;
use OpenAPI\Fincode\Model\CustomerUpdatingRequest;
use OpenAPI\Fincode\Model\CustomerUpdatingResponse;
use OpenAPI\Fincode\Model\DeleteFlag;

/**
 * Fincode顧客APIリクエスト
 *
 * @see https://docs.fincode.jp/api#tag/%E9%A1%A7%E5%AE%A2
 */
class FincodeCustomerRequest extends FincodeAbstract
{
    /**
     * 顧客 取得
     *
     * @throws FincodeRequestException|FincodeUnknownResponseException
     */
    public function retrieve(FinCustomer|string $customer): ?FinCustomer
    {
        $customer_id = $customer instanceof FinCustomer ? $customer->id : $customer;

        try {
            $response = $this->token->default()
                ->retrieveCustomer($customer_id, $this->token->private_shop_id);
        } catch (GuzzleException|ApiException $e) {
            throw new FincodeRequestException($e);
        }

        if ($response instanceof CustomerRetrievingResponse) {
            return $this->binding->customer($response);
        }

        throw new FincodeUnknownResponseException;
    }

    /**
     * 顧客 登録
     *
     * @throws FincodeRequestException|FincodeUnknownResponseException
     */
    public function create(FinCustomer $customer): ?FinCustomer
    {
        $body = (new CustomerCreatingRequest($this->binding->castArray($customer)));

        try {
            $response = $this->token->default()
                ->createCustomer($this->token->private_shop_id, $body);
        } catch (GuzzleException|ApiException $e) {
            throw new FincodeRequestException($e);
        }

        if ($response instanceof CustomerCreatingRequest) {
            return $this->binding->customer($response);
        }

        throw new FincodeUnknownResponseException;
    }

    /**
     * 顧客 更新
     *
     * @throws FincodeRequestException|FincodeUnknownResponseException
     */
    public function update(FinCustomer $customer): ?FinCustomer
    {
        $body = (new CustomerUpdatingRequest($this->binding->castArray($customer)));

        try {
            $response = $this->token->default()
                ->updateCustomer($customer->id, $this->token->private_shop_id, $body);
        } catch (GuzzleException|ApiException $e) {
            throw new FincodeRequestException($e);
        }

        if ($response instanceof CustomerUpdatingResponse) {
            return $this->binding->customer($response);
        }

        throw new FincodeUnknownResponseException;
    }

    /**
     * 顧客 削除
     *
     * @throws FincodeRequestException|FincodeUnknownResponseException
     */
    public function delete(FinCustomer $customer): ?FinCustomer
    {
        try {
            $response = $this->token->default()
                ->deleteCustomer($customer->id, $this->token->private_shop_id);
        } catch (GuzzleException|ApiException $e) {
            throw new FincodeRequestException($e);
        }

        if ($response instanceof CustomerDeletingResponse) {
            if ($response->getDeleteFlag() === DeleteFlag::_1) {
                return FInCustomer::find($response->getId());
            }

            return null;
        }

        throw new FincodeUnknownResponseException;
    }
}
