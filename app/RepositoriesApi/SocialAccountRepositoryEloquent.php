<?php

namespace App\RepositoriesApi;

use App\Models\SocialAccount;
use App\Models\User;
use GuzzleHttp\Exception\ClientException;
use Laravel\Socialite\Contracts\Provider;
use App\RepositoriesApi\Contracts\SocialAccountRepositoryInterface;

class SocialAccountRepositoryEloquent extends AbstractRepositoryEloquent implements SocialAccountRepositoryInterface
{
    public function __construct(SocialAccount $model)
    {
        parent::__construct($model);
    }

    public function createOrGetUser(Provider $provider, $data)
    {
        try {
            $providerUser = $provider->userFromToken($data['token']);
        } catch (ClientException $e) {
            return false;
        }
        $account = $this->model->whereProvider($data['provider'])
            ->whereProviderUserId($providerUser->getId())
            ->first();

        if ($account) {
            return $account->user;
        }

        $account = new SocialAccount([
            'provider_user_id' => $providerUser->getId(),
            'provider' => $data['provider']
        ]);
        $user = User::whereEmail($providerUser->getEmail())->first();

        if (!$user || $user && $user->email == null) {
            $user = User::create([
                'email' => $providerUser->getEmail(),
                'name' => $providerUser->getName(),
                'avatar' => $providerUser->getAvatar(),
            ]);
        }

        $account->user()->associate($user);
        $account->save();

        return User::find($user->id);
    }
}
