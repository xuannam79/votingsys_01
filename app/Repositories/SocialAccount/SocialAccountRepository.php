<?php
namespace App\Repositories\SocialAccount;

use Auth;
use App\Models\SocialAccount;
use App\Repositories\BaseRepository;

class SocialAccountRepository extends BaseRepository
{
    public function __construct(SocialAccount $socialAccount)
    {
        $this->model = $socialAccount;
    }

    public function getAccount($providerName, $providerUser)
    {
        $account = $this->model->whereProvider($providerName)
            ->whereProviderUserId($providerUser->getId())
            ->first();

        return $account;
    }
}
