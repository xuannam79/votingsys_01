<?php

namespace App\Providers;

use Session;
use View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Session::put('locale', 'vi');

        $linkGoogleMapApi = 'https://maps.googleapis.com/maps/api/js?key='
                            . config("app.key_program.google_map")
                            . '&v=3.exp&libraries=places&language=';
        View::share('linkGoogleMapApi', $linkGoogleMapApi);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
