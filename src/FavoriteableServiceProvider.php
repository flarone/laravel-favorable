<?php

namespace Flarone\Favoriteable;

use Illuminate\Support\ServiceProvider;

class FavoriteableServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerPublishables();

        $this->loadMigrationsFrom(__DIR__.'/../migrations');
    }
    
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/favorable.php', 'favorable');
    }

    protected function registerPublishables(): void {
        $this->publishes([
            __DIR__ . '/../config/favorable.php' => config_path('favorable.php')
        ], 'config');
    }
}
