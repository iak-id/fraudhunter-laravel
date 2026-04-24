<?php

namespace FraudHunter\Laravel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class FraudHunterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish configuration
        $this->publishes([
            __DIR__.'/../config/fraudhunter.php' => config_path('fraudhunter.php'),
        ], 'config');

        // Register event listeners from config
        $this->registerEventListeners();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/../config/fraudhunter.php', 'fraudhunter'
        );

        // Register main client as a singleton
        $this->app->singleton(FraudHunterClient::class, function ($app) {
            return new FraudHunterClient(config('fraudhunter'), $app['log']);
        });

        // Alias for easy injection
        $this->app->alias(FraudHunterClient::class, 'fraudhunter');
    }

    /**
     * Bind listeners to events specified in config.
     *
     * @return void
     */
    protected function registerEventListeners()
    {
        $eventMap = config('fraudhunter.event_map', []);

        foreach (array_keys($eventMap) as $event) {
            Event::listen($event, \FraudHunter\Laravel\Listeners\FraudHunterListener::class);
        }
    }
}
