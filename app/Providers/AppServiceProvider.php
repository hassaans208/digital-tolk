<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Translation;
use App\Observers\TranslationObserver;

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
        Translation::observe(TranslationObserver::class);
    }
}
