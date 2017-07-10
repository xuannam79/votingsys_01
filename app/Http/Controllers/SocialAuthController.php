<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Socialite;
use App\Services\SocialAccountService;
use FAuth;
use App\Models\SocialAccount;

class SocialAuthController extends Controller
{

    /**
      * @param $provider
      * @return mixed
    */
    public function redirectToProvider($provider)
    {
        return $provider === SocialAccount::FRAMGIA_DRIVER ? FAuth::redirect() : Socialite::driver($provider)->redirect();
    }

    /**
      * @param SocialAccountService $service
      * @param $provider
     * @return mixed
    */
    public function handleProviderCallback(SocialAccountService $service, $provider)
    {
        $driver = $provider === SocialAccount::FRAMGIA_DRIVER ? FAuth::driver($provider) : Socialite::driver($provider);

        try {
            $user = $service->createOrGetUser($driver);

            if ($user) {
              auth()->login($user);

              return redirect()->to(url('/'));
            }
        } catch (\Exception $ex) {
            return redirect()->to(url('/login'));
        }
    }
}
