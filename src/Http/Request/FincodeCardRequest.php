<?php

namespace Fincode\Laravel\Http\Request;

use Fincode\Laravel\Exceptions\FincodeApiException;
use Fincode\Laravel\Exceptions\FincodeRequestException;
use Fincode\Laravel\Exceptions\FincodeUnknownResponseException;
use Fincode\Laravel\Models\FinCard;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Builder;
use OpenAPI\Fincode\ApiException;
use OpenAPI\Fincode\Model\CustomerCardCreatingRequest;
use OpenAPI\Fincode\Model\CustomerCardCreatingResponse;
use OpenAPI\Fincode\Model\CustomerCardDeletingResponse;
use OpenAPI\Fincode\Model\CustomerCardRetrievingResponse;
use OpenAPI\Fincode\Model\CustomerCardUpdatingRequest;
use OpenAPI\Fincode\Model\CustomerCardUpdatingResponse;
use OpenAPI\Fincode\Model\DefaultFlag;

class FincodeCardRequest extends FincodeCustomerAbstract
{
    /**
     * カード情報を作成する
     *
     * @param  string  $token  FincodeJSで取得したカード利用トークン
     * @param  bool  $default  デフォルトフラグ
     *
     * @throws FincodeRequestException|FincodeUnknownResponseException
     */
    public function create(string $token, bool $default = true, bool $save = true): FinCard
    {
        $body = (new CustomerCardCreatingRequest)
            ->setToken($token)
            ->setDefaultFlag($default ? DefaultFlag::_1 : DefaultFlag::_0);

        try {
            $response = $this->token->default()
                ->createCustomerCard($this->customer->id, $this->token->private_shop_id, $body);
        } catch (GuzzleException|ApiException $e) {
            throw new FincodeRequestException($e);
        }

        if ($response instanceof CustomerCardCreatingResponse) {
            $result = (new FinCard)
                ->forceFill([
                    'id' => $response->getId(),
                    'customer_id' => $response->getCustomerId(),
                    'default_flag' => $response->getDefaultFlag() === DefaultFlag::_1,
                    'card_no' => $response->getCardNo(),
                    'expire' => $response->getExpire(),
                    'holder_name' => $response->getHolderName(),
                    'type' => $response->getType(),
                    'brand' => $response->getBrand(),
                    'card_no_hash' => $response->getCardNoHash(),
                    'created' => $response->getCreated(),
                    'updated' => $response->getUpdated(),
                ]);

            if ($save) {
                $result->save();
            }

            return $result;
        }

        throw new FincodeUnknownResponseException;
    }

    /**
     * カード情報の最新情報を取得する
     *
     * @param  FinCard|string  $card  取得対象のFinCardオブジェクトまたは、カードID
     * @param  bool  $save  データ取得成功時の保存フラグ、デフォルトでは保存しません
     *
     * @throws FincodeRequestException|FincodeUnknownResponseException
     */
    public function get(FinCard|string $card, bool $save = false): FinCard
    {
        $card_id = $card instanceof FinCard ? $card->id : $card;

        try {
            $response = $this->token->default()
                ->retrieveCustomerCard($this->customer->id, $card_id, $this->token->private_shop_id);
        } catch (GuzzleException|ApiException $e) {
            throw new FincodeRequestException($e);
        }

        if ($response instanceof CustomerCardRetrievingResponse) {
            $result = (new FinCard)
                ->forceFill([
                    'id' => $response->getId(),
                    'customer_id' => $response->getCustomerId(),
                    'card_no' => $response->getCardNo(),
                    'default_flag' => $response->getDefaultFlag() === DefaultFlag::_1,
                    'expire' => $response->getExpire(),
                    'holder_name' => $response->getHolderName(),
                    'type' => $response->getType(),
                    'brand' => $response->getBrand(),
                    'card_no_hash' => $response->getCardNoHash(),
                    'created' => $response->getCreated(),
                    'updated' => $response->getUpdated(),
                ]);

            if ($save) {
                $result->save();
            }

            return $result;
        }

        throw new FincodeUnknownResponseException;
    }

    /**
     * カード情報の更新
     *
     * @param  FinCard|string  $card  更新対象となるFinCardオブジェクト、またはカードID。カード名義人名や有効期限を変更する場合は FinCardオブジェクトの各値を変更してください。
     * @param  string  $token  FincodeJSで取得したカード利用トークン
     *
     * @throws FincodeRequestException|FincodeUnknownResponseException
     */
    public function update(FinCard|string $card, string $token, bool $save = true): FinCard
    {
        $card_id = $card instanceof FinCard ? $card->id : $card;

        $body = (new CustomerCardUpdatingRequest)
            ->setToken($token)
            ->setDefaultFlag($card->default_flag ? DefaultFlag::_1 : DefaultFlag::_0);

        if ($card instanceof FinCard) {
            $body
                ->setHolderName($card->holder_name)
                ->setExpire($card->expire->format('Ym'));
        }

        try {
            $response = $this->token->default()
                ->updateCustomerCard($this->customer->id, $card_id, $this->token->private_shop_id);
        } catch (GuzzleException|ApiException $e) {
            throw new FincodeRequestException($e);
        }

        if ($response instanceof CustomerCardUpdatingResponse) {
            $result = (new FinCard)
                ->forceFill([
                    'id' => $response->getId(),
                    'customer_id' => $response->getCustomerId(),
                    'default_flag' => $response->getDefaultFlag() === DefaultFlag::_1,
                    'card_no' => $response->getCardNo(),
                    'expire' => $response->getExpire(),
                    'holder_name' => $response->getHolderName(),
                    'type' => $response->getType(),
                    'brand' => $response->getBrand(),
                    'card_no_hash' => $response->getCardNoHash(),
                    'created' => $response->getCreated(),
                    'updated' => $response->getUpdated(),
                ]);

            if ($save) {
                $result->save();
            }

            return $result;
        }

        throw new FincodeUnknownResponseException;
    }

    /**
     * カード情報を削除する
     *
     * @throws FincodeUnknownResponseException
     */
    public function delete(FinCard|string $card, bool $save = true): Builder|FinCard
    {
        $card_id = $card instanceof FinCard ? $card->id : $card;

        try {
            $response = $this->token->default()
                ->deleteCustomerCard($this->customer->id, $card_id, $this->token->private_shop_id);
        } catch (GuzzleException|ApiException $e) {
            throw new FincodeApiException($e);
        }

        if ($response instanceof CustomerCardDeletingResponse) {
            $query = FinCard::whereCardId($response->getId())
                ->whereCustomerId($response->getCustomerId());

            if ($save) {
                $query->delete();
                $query->withTrashed();
            } else {
                $query->withoutTrashed();
            }

            return $query;
        }

        throw new FincodeUnknownResponseException;
    }
}
