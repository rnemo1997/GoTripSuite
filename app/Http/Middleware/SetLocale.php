<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    protected array $supported = ['en', 'nl', 'de', 'fr', 'es'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');

        if ($locale && in_array($locale, $this->supported)) {
            App::setLocale($locale);
        } else {
            $locale = 'en';
            App::setLocale('en');
        }

        // Set URL defaults so route() generates correct locale prefix
        URL::defaults(['locale' => $locale === 'en' ? null : $locale]);

        // Remove locale from route parameters so controllers don't receive it
        $request->route()->forgetParameter('locale');

        return $next($request);
    }
}
