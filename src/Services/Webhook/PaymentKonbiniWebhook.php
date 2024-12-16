<?php

namespace Fincode\Laravel\Services\Webhook;

use Fincode\Laravel\Exceptions\FincodeWebhookTargetException;
use Fincode\Laravel\Models\FinPayment;
use Fincode\Laravel\Models\FinPaymentKonbini;
use Illuminate\Database\Eloquent\Model;

class PaymentKonbiniWebhook extends WebhookServiceInstance
{
    /**
     * {@inheritDoc}
     */
    public function handle(): Model
    {
        $values = [
            ...$this->payload->only([
                ...(new FinPayment)->getFillable(),
                ...(new FinPaymentKonbini)->getFillable(),
            ]),
            'id' => $this->payload->get('order_id'),
            'total_amount' => $this->payload->get('amount') + $this->payload->get('tax'),
        ];

        if (FinPayment::whereId($values['id'])->exists()) {
            return $this->binding->payment($values);
        }

        throw new FincodeWebhookTargetException("Payment#{$values['id']} not found.");
    }
}
