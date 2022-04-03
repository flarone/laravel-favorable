<?php

namespace Flarone\Favoriteable;

use Illuminate\Support\ServiceProvider;

class FavoriteableServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../migrations');
    }
    
    public function register()
    {
    }
}
