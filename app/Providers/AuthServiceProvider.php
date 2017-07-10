<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\SocialAccount;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('administer', function ($user, $participatedPoll) {
            return $user->id == $participatedPoll->user_id;
        });

        Gate::define('ownerPoll', function ($user, $poll) {
            return $user->id == $poll->user_id;
        });

        Gate::define('framgia-provider', function ($user) {
            return $user->socialAccounts()->whereProvider(SocialAccount::FRAMGIA_PROVIDER)->exists();
        });

        Passport::routes();
    }
}
