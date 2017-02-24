<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ApiRepositoryServiceProvider extends ServiceProvider
{

    protected static $repositories = [
        'user' => [
            \App\RepositoriesApi\Contracts\UserRepositoryInterface::class,
            \App\RepositoriesApi\UserRepositoryEloquent::class,
        ],

        'poll' => [
            \App\RepositoriesApi\Contracts\PollRepositoryInterface::class,
            \App\RepositoriesApi\PollRepositoryEloquent::class,
        ],

    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        foreach (static::$repositories as $repository) {
            $this->app->singleton(
                $repository[0],
                $repository[1]
            );
        }
    }
}
