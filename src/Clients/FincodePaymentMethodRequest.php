<?php

namespace Fincode\Laravel\Clients;

use ErrorException;
use Fincode\OpenAPI\Model\CustomerPaymentMethodCreatingRequest;
use Fincode\OpenAPI\Model\CustomerPaymentMethodCreatingRequestCard;
use Fincode\OpenAPI\Model\CustomerPaymentMethodCreatingRequestDirectdebit;
use Fincode\OpenAPI\Model\CustomerPaymentMethodCreatingResponse;
use Fincode\OpenAPI\Model\PaymentMethodPayType;
use Fincode\OpenAPI\Model\PropertiesDefaultFlag;

/**
 * Fincode 決済手段APIリクエスト
 *
 * @see https://docs.fincode.jp/api#tag/%E6%B1%BA%E6%B8%88%E6%89%8B%E6%AE%B5
 */
class FincodePaymentMethodRequest extends FincodeCustomerAbstract
{
    /**
     * カードの決済手段を登録する
     *
     * @param  PaymentMethodPayType  $pay_type  決済種別
     * @param  bool  $default  デフォルトフラグ
     * @param  array  $attributes  その他の属性値
     *
     * @throws ErrorException
     */
    public function create(
        PaymentMethodPayType $pay_type,
        bool $default,
        array $attributes,
        CustomerPaymentMethodCreatingRequestCard|CustomerPaymentMethodCreatingRequestDirectdebit|array $method,
    ): CustomerPaymentMethodCreatingResponse {
        $body = (new CustomerPaymentMethodCreatingRequest($this->binding->castArray([
            ...$attributes,
            'pay_type' => $pay_type,
            'default_flag' => $default ? PropertiesDefaultFlag::_1 : PropertiesDefaultFlag::_0,
            'client_field_1' => $this->token->client_field_1,
            'client_field_3' => $this->token->client_field_3,
        ])));

        $method = $this->binding->castArray($method);

        if ($pay_type === PaymentMethodPayType::DIRECTDEBIT) {
            throw new ErrorException("{$pay_type->value} is not supported.");
            //            $body->setDirectdebit(new CustomerPaymentMethodCreatingRequestDirectdebit($method));
        } elseif ($pay_type === PaymentMethodPayType::CARD) {
            $body->setCard(new CustomerPaymentMethodCreatingRequestCard($method));
        } else {
            throw new ErrorException("{$pay_type->value} is not supported.");
        }

        return $this->dispatch(
            CustomerPaymentMethodCreatingResponse::class,
            fn () => $this->token->default()->createCustomerPaymentMethod($this->customer->id, $this->token->private_tenant_id, $body),
        );
    }
}
