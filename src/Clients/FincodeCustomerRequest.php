<?php

namespace Fincode\Laravel\Clients;

use Fincode\Laravel\Exceptions\FincodeApiException;
use Fincode\Laravel\Models\FinCustomer;
use Fincode\OpenAPI\Model\CustomerCreatingRequest;
use Fincode\OpenAPI\Model\CustomerCreatingResponse;
use Fincode\OpenAPI\Model\CustomerDeletingResponse;
use Fincode\OpenAPI\Model\CustomerRetrievingResponse;
use Fincode\OpenAPI\Model\CustomerUpdatingRequest;
use Fincode\OpenAPI\Model\CustomerUpdatingResponse;
use Fincode\OpenAPI\Model\DeleteFlag;

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
     * @throws FincodeApiException
     */
    public function retrieve(FinCustomer|string $customer): ?FinCustomer
    {
        $customer_id = $customer instanceof FinCustomer ? $customer->id : $customer;

        $response = $this->dispatch(
            CustomerRetrievingResponse::class,
            fn () => $this->token->default()->retrieveCustomer($customer_id, $this->token->private_tenant_id)
        );

        return $this->binding->customer($response);
    }

    /**
     * 顧客 登録
     *
     * @throws FincodeApiException
     */
    public function create(FinCustomer $customer): ?FinCustomer
    {
        $body = (new CustomerCreatingRequest($this->binding->castArray($customer)));

        $response = $this->dispatch(
            CustomerCreatingResponse::class,
            fn () => $this->token->default()->createCustomer($this->token->private_tenant_id, $body)
        );

        return $this->binding->customer($response);
    }

    /**
     * 顧客 更新
     *
     * @throws FincodeApiException
     */
    public function update(FinCustomer $customer): ?FinCustomer
    {
        $body = (new CustomerUpdatingRequest($this->binding->castArray($customer)));

        $response = $this->dispatch(
            CustomerUpdatingResponse::class,
            fn () => $this->token->default()->updateCustomer($customer->id, $this->token->private_tenant_id, $body)
        );

        return $this->binding->customer($response);
    }

    /**
     * 顧客 削除
     *
     * @return ?FinCustomer 削除した対象がモデルにある場合はモデルを、ない場合はnullを返却
     *
     * @throws FincodeApiException
     */
    public function delete(FinCustomer|string $customer): ?FinCustomer
    {
        $customer_id = $customer instanceof FinCustomer ? $customer->id : $customer;

        $response = $this->dispatch(
            CustomerDeletingResponse::class,
            fn () => $this->token->default()->deleteCustomer($customer_id, $this->token->private_tenant_id)
        );

        if ($response->getDeleteFlag() === DeleteFlag::_1) {
            return FInCustomer::find($response->getId());
        }

        return null;
    }
}
