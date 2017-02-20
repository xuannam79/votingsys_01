<?php

namespace App\RepositoriesApi\Contracts;

use Laravel\Socialite\Contracts\Provider;

interface SocialAccountRepositoryInterface
{
    public function createOrGetUser(Provider $provider, $data);
}
