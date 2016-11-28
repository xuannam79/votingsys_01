<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Request;
use Illuminate\Support\Facades\Validator;

class ValidationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extendImplicit('option', 'App\Services\PollValidator@option');
        Validator::extendImplicit('setting', 'App\Services\PollValidator@setting');
        Validator::extendImplicit('participant', 'App\Services\PollValidator@participant');
        Validator::extendImplicit('info', 'App\Services\PollValidator@information');
        Validator::extendImplicit('updateEmail', 'App\Services\PollValidator@updateEmail');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
