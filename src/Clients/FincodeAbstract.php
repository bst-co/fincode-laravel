<?php

namespace Fincode\Laravel\Clients;

use Closure;
use Fincode\Laravel\Eloquent\FinModelBinding;
use Fincode\Laravel\Exceptions\FincodeApiException;
use Fincode\Laravel\Exceptions\FincodeRequestException;
use Fincode\Laravel\Http\FincodeRequestToken;
use GuzzleHttp\Exception\GuzzleException;
use OpenAPI\Fincode\ApiException;
use OpenAPI\Fincode\Model\ModelInterface;

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

    /**
     * APIのリクエストラッパ
     *
     * @template TInterface of ModelInterface
     *
     * @param  class-string<TInterface>|class-string<TInterface>[]  $interface  返却が期待されるオブジェクト名、またはそのリスト
     * @param  Closure  $closure  APIリクエストを実行するクロージャ関数
     * @return TInterface
     *
     * @throws FincodeApiException APIリクエストに失敗すると発生する例外
     *
     * @noinspection PhpRedundantCatchClauseInspection
     */
    protected function dispatch(array|string $interface, Closure $closure)
    {
        $interface = is_array($interface) ? $interface : [$interface];

        try {
            $response = $closure();
        } catch (GuzzleException|ApiException $e) {
            throw new FincodeApiException($e);
        }

        if (in_array($response::class, $interface)) {
            return $response;
        }

        throw new FincodeApiException($response);
    }
}
