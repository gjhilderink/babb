<?php

return [
    'name'     => env('APP_NAME', 'BABB Portaal'),
    'env'      => env('APP_ENV', 'production'),
    'debug'    => (bool) env('APP_DEBUG', false),
    'url'      => env('APP_URL', 'http://localhost'),
    'timezone' => env('APP_TIMEZONE', 'Europe/Amsterdam'),
    'locale'   => env('APP_LOCALE', 'nl'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'nl'),
    'faker_locale'    => env('APP_FAKER_LOCALE', 'nl_NL'),
    'key'    => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
    'providers' => Illuminate\Support\ServiceProvider::defaultProviders()->merge([])->toArray(),
];
