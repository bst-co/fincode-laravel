<?php

namespace Fincode\Laravel\Http\Request;

use Fincode\Laravel\Exceptions\FincodeRequestException;
use Fincode\Laravel\Http\FincodeRequestToken;

abstract class FincodeAbstract
{
    /**
     * Fincodeとの通信に使用するテナントトークン情報
     */
    protected readonly FincodeRequestToken $token;

    /**
     * @param  FincodeRequestToken|null  $token  Fincodeとの通信に使用するテナントトークン情報、空の場合はデフォルト値が適用される
     *
     * @throws FincodeRequestException
     */
    public function __construct(
        ?FincodeRequestToken $token = null
    ) {
        $this->token = $token ?? FincodeRequestToken::make();
    }
}
