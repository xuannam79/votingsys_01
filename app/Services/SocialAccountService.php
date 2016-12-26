<?php

namespace App\Services;

use App\Models\User;
use App\Models\SocialAccount;
use Laravel\Socialite\Contracts\Provider;
use App\Repositories\SocialAccount\SocialAccountRepository;
use App\Repositories\User\UserRepositoryInterface;

class SocialAccountService
{
    protected $socialAccountRepository;
    protected $userRepository;

    public function __construct(SocialAccountRepository $socialAccountRepository, UserRepositoryInterface $userRepository)
    {
        $this->socialAccountRepository = $socialAccountRepository;
        $this->userRepository = $userRepository;
    }

    public function createOrGetUser(Provider $provider)
    {
        $providerUser = $provider->user();

        $providerName = class_basename($provider);
        $account = $this->socialAccountRepository->getAccount($providerName, $providerUser);

        if ($account) {
            $user = $account->user;

            if (preg_match('#^(http)|(https).*$#', $user->avatar)
                && $user->avatar != $providerUser->avatar) {
                $user->avatar = $providerUser->avatar;
                $user->save();
            }

            return $user;
        }

        $account = new SocialAccount([
            'provider_user_id' => $providerUser->getId(),
            'provider' => $providerName,
        ]);

        $user = $this->userRepository->getUserWithEmail($providerUser);

        if (!$user || $user && $user->email == null) {
            $datas = [
                'email' => $providerUser->getEmail(),
                'name' => $providerUser->getName(),
                'avatar' => $providerUser->getAvatar(),
            ];

            $user = $this->userRepository->createUserSocial($datas);
        }

        $account->user()->associate($user);
        $account->save();

        return $user;
    }
}
