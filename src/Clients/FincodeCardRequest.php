<?php

namespace Fincode\Laravel\Clients;

use Fincode\Laravel\Models\FinCard;
use Fincode\OpenAPI\Model\CustomerCardCreatingRequest;
use Fincode\OpenAPI\Model\CustomerCardCreatingResponse;
use Fincode\OpenAPI\Model\CustomerCardDeletingResponse;
use Fincode\OpenAPI\Model\CustomerCardListRetrievingResponse;
use Fincode\OpenAPI\Model\CustomerCardRetrievingResponse;
use Fincode\OpenAPI\Model\CustomerCardUpdatingRequest;
use Fincode\OpenAPI\Model\CustomerCardUpdatingResponse;
use Fincode\OpenAPI\Model\DefaultFlag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * FincodeカードAPIリクエスト
 *
 * @see https://docs.fincode.jp/api#tag/%E3%82%AB%E3%83%BC%E3%83%89
 */
class FincodeCardRequest extends FincodeCustomerAbstract
{
    /**
     * カード情報を作成する
     *
     * @param  string  $token  FincodeJSで取得したカード利用トークン
     * @param  bool  $default  デフォルトフラグ
     */
    public function create(string $token, bool $default = true): FinCard
    {
        $body = (new CustomerCardCreatingRequest)
            ->setToken($token)
            ->setDefaultFlag($default ? DefaultFlag::_1 : DefaultFlag::_0);

        $response = $this->dispatch(
            CustomerCardCreatingResponse::class,
            fn () => $this->token->default()->createCustomerCard($this->customer->id, $this->token->private_tenant_id, $body),
        );

        return $this->binding->card($response);
    }

    /**
     * カード 一覧取得
     *
     * @return Collection<FinCard>
     */
    public function list(): Collection
    {
        $response = $this->dispatch(
            CustomerCardListRetrievingResponse::class,
            fn () => $this->token->default()->retrieveCustomerCardList($this->customer->id, $this->token->private_tenant_id),
        );

        $models = Collection::make();

        foreach ($response->getList() as $item) {
            $models->push($this->binding->card($item));
        }

        return $models;
    }

    /**
     * カード情報の最新情報を取得する
     *
     * @param  FinCard|string  $card  取得対象のFinCardオブジェクトまたは、カードID
     */
    public function retrieve(FinCard|string $card): FinCard
    {
        $card_id = $card instanceof FinCard ? $card->id : $card;

        $response = $this->dispatch(
            CustomerCardRetrievingResponse::class,
            fn () => $this->token->default()->retrieveCustomerCard($this->customer->id, $card_id, $this->token->private_tenant_id),
        );

        return $this->binding->card($response);
    }

    /**
     * カード情報の更新
     *
     * @param  FinCard|string  $card  更新対象となるFinCardオブジェクト、またはカードID。カード名義人名や有効期限を変更する場合は FinCardオブジェクトの各値を変更してください。
     * @param  string|null  $token  FincodeJSで取得したカード利用トークン
     */
    public function update(FinCard|string $card, ?string $token = null): FinCard
    {
        $card_id = $card instanceof FinCard ? $card->id : $card;

        $body = (new CustomerCardUpdatingRequest);

        if ($token !== null) {
            $body->setToken($token);
        }

        if ($card instanceof FinCard) {
            $body
                ->setDefaultFlag($card->default_flag ? DefaultFlag::_1 : DefaultFlag::_0)
                ->setHolderName($card->holder_name)
                ->setExpire($card->expire->format('Ym'));
        }

        $response = $this->dispatch(
            CustomerCardUpdatingResponse::class,
            fn () => $this->token->default()->updateCustomerCard($this->customer->id, $card_id, $this->token->private_tenant_id),
        );

        return $this->binding->card($response);
    }

    /**
     * カード情報を削除する
     */
    public function delete(FinCard|string $card): Builder|FinCard
    {
        $card_id = $card instanceof FinCard ? $card->id : $card;

        $response = $this->dispatch(
            CustomerCardDeletingResponse::class,
            fn () => $this->token->default()->deleteCustomerCard($this->customer->id, $card_id, $this->token->private_tenant_id),
        );

        return FinCard::find($response->getId());
    }
}
