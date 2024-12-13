<?php

namespace Fincode\Laravel\Http\Request;

use Fincode\Laravel\Eloquent\FinModelBinding;
use Fincode\Laravel\Exceptions\FincodeRequestException;
use Fincode\Laravel\Http\FincodeRequestToken;

abstract class FincodeAbstract
{
    /**
     * Fincodeとの通信に使用するテナントトークン情報
     */
    protected readonly FincodeRequestToken $token;

    protected readonly FinModelBinding $binding;

    /**
     * @param  FincodeRequestToken|null  $token  Fincodeとの通信に使用するテナントトークン情報、空の場合はデフォルト値が適用される
     *
     * @throws FincodeRequestException
     */
    public function __construct(
        ?FincodeRequestToken $token = null
    ) {
        $this->binding = new FinModelBinding;
        $this->token = $token ?? FincodeRequestToken::make();
    }
}
