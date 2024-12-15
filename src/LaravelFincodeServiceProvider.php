<?php

namespace Fincode\Laravel;

use Fincode\Laravel\Console\Commands\PublishCommand;
use Fincode\Laravel\Models\FinCard;
use Fincode\Laravel\Models\FinCustomer;
use Fincode\Laravel\Models\FinPayment;
use Fincode\Laravel\Models\FinPaymentApplePay;
use Fincode\Laravel\Models\FinPaymentCard;
use Fincode\Laravel\Models\FinPaymentKonbini;
use Fincode\Laravel\Models\FinShop;
use Fincode\Laravel\Models\FinShopToken;
use Illuminate\Contracts\Foundation\CachesRoutes;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * @noinspection PhpUnused
 */
class LaravelFincodeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (! app()->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__.'/../config/fincode.php', 'fincode');
        }

        $this->registerCommands();
        $this->registerRoutes();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePublishes();

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        Relation::enforceMorphMap([
            'fin_card' => FInCard::class,
            'fin_customer' => FinCustomer::class,
            'fin_payment_apple_pay' => FinPaymentApplePay::class,
            'fin_payment_card' => FinPaymentCard::class,
            'fin_payment_konbini' => FinPaymentKonbini::class,
            'fin_payment' => FinPayment::class,
            'fin_shop_token' => FinShopToken::class,
            'fin_shop' => FinShop::class,
        ]);
    }

    /**
     * Register the console commands for the package.
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                PublishCommand::class,
            ]);
        }
    }

    protected function registerRoutes(): void
    {
        if ($this->app instanceof CachesRoutes && $this->app->routesAreCached()) {
            return;
        }

        Route::group([
            'domain' => config('fincode.webhook.domain'),
            'prefix' => config('fincode.webhook.path'),
            'middleware' => config('fincode.webhook.middleware'),
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });
    }

    /**
     * Configure publishing for the package.
     */
    protected function configurePublishes(): void
    {
        if (app()->runningInConsole()) {
            $this->publishesMigrations([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], ['fincode', 'fincode-migrations']);

            $this->publishes([
                __DIR__.'/../config/fincode.php' => config_path('fincode.php'),
            ], ['fincode', 'fincode-config']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function provides(): array
    {
        return [
            PublishCommand::class,
        ];
    }
}
