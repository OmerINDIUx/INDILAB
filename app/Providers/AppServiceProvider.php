<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Stringable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! Stringable::hasMacro('doesntStartWith')) {
            Stringable::macro('doesntStartWith', function ($search) {
                return ! $this->startsWith($search);
            });
        }

        if (! Stringable::hasMacro('doesntEndWith')) {
            Stringable::macro('doesntEndWith', function ($search) {
                return ! $this->endsWith($search);
            });
        }
    }
}
