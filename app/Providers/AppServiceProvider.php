<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        View::composer('*', function ($view) {
            $view->with('currentLocale', app()->getLocale());
            $view->with('supportedLocales', [
                'en' => 'English',
                'nl' => 'Nederlands',
                'de' => 'Deutsch',
                'fr' => 'Fran&ccedil;ais',
                'es' => 'Espa&ntilde;ol',
            ]);
        });
    }
}
