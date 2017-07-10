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
        // now checking provider_user_id from social_acounts table is email. If authentication attempts by auth framgia
        $id = $providerName == SocialAccount::FRAMGIA_PROVIDER ? $providerUser->getEmail() : $providerUser->getId();

        $account = $this->model->whereProvider($providerName)
            ->whereProviderUserId($id)
            ->first();

        return $account;
    }
}
