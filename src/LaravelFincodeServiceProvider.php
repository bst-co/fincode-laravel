<?php

namespace Fincode\Laravel;

use Illuminate\Support\ServiceProvider;
use Fincode\Laravel\Console\Commands\PublishCommand;

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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePublishes();

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
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
