<?php

namespace Fincode\Laravel\Concerns;

use Fincode\Laravel\Clients\FincodeRequestToken;
use Fincode\Laravel\Exceptions\FincodeApiException;
use Fincode\Laravel\Exceptions\FincodeRequestException;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Console\Input\InputOption;
use Throwable;

abstract class FincodeApiCommand extends Command
{
    /**
     * @var \Illuminate\Support\Collection<Throwable>
     */
    protected \Illuminate\Support\Collection $exceptions;

    public function __construct()
    {
        $this->exceptions = collect();
        parent::__construct();
    }

    /**
     * @inhe
     */
    protected function configure(): void
    {
        $this->getDefinition()->addOptions([
            new InputOption('shop', null, InputOption::VALUE_OPTIONAL, 'Shop ID or Config name.', 'default'),
            new InputOption('no-save', 'N', InputOption::VALUE_NONE, 'Not autosaving model.'),
            new InputOption('pretty', null, InputOption::VALUE_NONE, 'Pretty print JSON'),
            new InputOption('escape', null, InputOption::VALUE_NONE, 'Escape Unicode JSON'),
        ]);
    }

    /**
     * コマンドを実行し、正常な応答がある場合はJSONデータを返却します
     */
    final public function handle(): void
    {
        $begin = now();

        $option = 0;

        if ($this->option('pretty')) {
            $option |= JSON_PRETTY_PRINT;
        }

        if (! $this->option('escape')) {
            $option |= JSON_UNESCAPED_UNICODE;
        }

        try {
            if ($model = $this->process()) {
                $data = [
                    'command' => $this->getName(),
                    'data' => $model->toArray(),
                    'datetime' => $begin->toIso8601String(),
                    'duration' => $begin->diffInSeconds(),
                ];

                if ($this->exceptions->isNotEmpty()) {
                    $data['errors'] = $this->exceptions->map(fn (Throwable $e) => $e->getMessage())->toArray();
                }

                $this->line(json_encode($data, $option));
            }
        } catch (FincodeApiException $e) {
            if ($this->output->isDebug()) {
                throw $e;
            }

            $this->error('['.$e->getStatusCode().'] '.$e->getMessage());
        }
    }

    /**
     * APIリクエストトークンを作成
     *
     * @throws FincodeRequestException
     */
    public function getToken(): FincodeRequestToken
    {
        $token = $this->option('shop');
        $token = FincodeRequestToken::make($token, null, $this->output->isDebug());

        $this->info("Using connection fincode to {$token->host()} by {$token->shop_id}", 'v');

        return $token;
    }

    public function addExceptions(Throwable $e): void
    {
        $this->exceptions->push($e);
    }

    public function isSave(): bool
    {
        return $this->option('no-save') === false;
    }

    /**
     * APIリクエストの処理部分を書く
     *
     * @throws FincodeApiException
     */
    abstract protected function process(): Model|Collection;
}
