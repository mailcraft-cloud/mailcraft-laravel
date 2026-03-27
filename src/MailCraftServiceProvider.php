<?php

namespace MailCraft;

use Illuminate\Support\ServiceProvider;

class MailCraftServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/mailcraft.php', 'mailcraft');

        $this->app->singleton(MailCraftClient::class, function ($app) {
            return new MailCraftClient(
                config('mailcraft.api_key', ''),
                config('mailcraft.base_url', 'https://api.mailcraft.cloud'),
            );
        });

        $this->app->alias(MailCraftClient::class, 'mailcraft');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/mailcraft.php' => config_path('mailcraft.php'),
        ], 'mailcraft-config');
    }
}
