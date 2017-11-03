<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Socialite;
use App\Services\SocialAccountService;
use FAuth;
use App\Models\SocialAccount;
use Session;

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
    public function handleProviderCallback(Request $request, SocialAccountService $service, $provider)
    {
        $driver = $provider === SocialAccount::FRAMGIA_DRIVER ? FAuth::driver($provider) : Socialite::driver($provider);

        try {
            $user = $service->createOrGetUser($driver);

            if ($user) {
                auth()->login($user);
                
                if (Session::has('tokenSettingRequireAuthWsm')) {
                    $token = Session::get('tokenSettingRequireAuthWsm');

                    Session::put('tokenSettingRequireAuthWsm', '');

                    return redirect()->action('LinkController@show', ['token' => $token]);
                }

                return redirect()->to(url('/'));
            }
        } catch (\Exception $ex) {
            return redirect()->to(url('/login'));
        }
    }
}
