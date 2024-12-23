<?php

namespace Fincode\Laravel;

use Fincode\Laravel\Console\Commands;
use Fincode\Laravel\Events\FincodeWebhookEvent;
use Fincode\Laravel\Listeners\FincodeWebhookListener;
use Fincode\Laravel\Models\FinCard;
use Fincode\Laravel\Models\FinCustomer;
use Fincode\Laravel\Models\FinPayment;
use Fincode\Laravel\Models\FinPaymentApplePay;
use Fincode\Laravel\Models\FinPaymentCard;
use Fincode\Laravel\Models\FinPaymentKonbini;
use Fincode\Laravel\Models\FinShop;
use Fincode\Laravel\Models\FinShopToken;
use Fincode\Laravel\Models\FinWebhook;
use Illuminate\Contracts\Foundation\CachesRoutes;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * @noinspection PhpUnused
 */
class FincodeLaravelServiceProvider extends ServiceProvider
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

        Event::listen(
            FincodeWebhookEvent::class,
            FincodeWebhookListener::class,
        );

        Relation::morphMap([
            'fin_card' => FInCard::class,
            'fin_customer' => FinCustomer::class,
            'fin_payment_apple_pay' => FinPaymentApplePay::class,
            'fin_payment_card' => FinPaymentCard::class,
            'fin_payment_konbini' => FinPaymentKonbini::class,
            'fin_payment' => FinPayment::class,
            'fin_shop_token' => FinShopToken::class,
            'fin_shop' => FinShop::class,
            'fin_webhook' => FinWebhook::class,
        ]);
    }

    /**
     * Register the console commands for the package.
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\PublishCommand::class,
                Commands\CustomerRetrieveCommand::class,
                Commands\CustomerDeleteCommand::class,
                Commands\CardCommand::class,
                Commands\CardDeleteCommand::class,
                Commands\CardRetrieveCommand::class,
                Commands\PlatformRetrieveCommand::class,
                Commands\TenantRetrieveCommand::class,
                Commands\WebhookCommand::class,
                Commands\WebhookCreateCommand::class,
                Commands\WebhookDeleteCommand::class,
            ]);
        }
    }

    /**
     * Appending Fincode Route
     */
    protected function registerRoutes(): void
    {
        if ($this->app instanceof CachesRoutes && $this->app->routesAreCached()) {
            return;
        }

        Route::group([
            'domain' => config('fincode.route.domain'),
            'prefix' => config('fincode.route.path'),
            'middleware' => config('fincode.route.middleware'),
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
            $this->publishes([
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
