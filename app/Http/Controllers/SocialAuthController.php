<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Socialite;
use App\Services\SocialAccountService;

class SocialAuthController extends Controller
{

    /**
      * @param $provider
      * @return mixed
    */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
      * @param SocialAccountService $service
      * @param $provider
     * @return mixed
    */
    public function handleProviderCallback(SocialAccountService $service, $provider)
    {
        try {
            $user = $service->createOrGetUser(Socialite::driver($provider));

            if ($user) {
              auth()->login($user);

              return redirect()->to(url('/'))->withMessage(trans('user.login_successfully'));
            }
        } catch (\Exception $ex) {
            return redirect()->to(url('/login'));
        }
    }
}
