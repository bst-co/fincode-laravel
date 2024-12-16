<?php

namespace Fincode\Laravel\Services\Webhook;

use Fincode\Laravel\Exceptions\FincodeWebhookTargetException;
use Fincode\Laravel\Models\FinCard;
use Illuminate\Database\Eloquent\Model;

class CardWebhook extends WebhookServiceInstance
{
    /**
     * {@inheritDoc}
     */
    public function handle(): Model
    {
        $values = [
            ...$this->payload->only([
                ...(new FinCard)->getFillable(),
            ]),
            'id' => $this->payload->get('card_id'),
        ];

        if ($this->payload->has('card_no_display')) {
            $values['card_no'] = $this->payload->get('card_no_display');
        }

        if ($this->payload->has('expire_display')) {
            $values['expire'] = $this->payload->get('expire_display');
        }

        if (FinCard::whereId($values['id'])->exists()) {
            return $this->binding->card($values);
        }

        throw new FincodeWebhookTargetException("Card#{$values['id']} not found.");
    }
}
