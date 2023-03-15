<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;


class ProviderController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }
    public function callback($provider)
    {
        try {
            $social_user = Socialite::driver($provider)->user();
            $user = User::updateOrCreate([
                'provider_id' => $social_user->id,
                'provider' => $provider,
            ], [
                'name' => $social_user->name,
                'email' => $social_user->email,
                'provider_token' => $social_user->token,
            ]);

            Auth::login($user);

            return redirect('/dashboard');
        }catch(\Exception $e){
            return redirect('/login');
        }
    }
}
