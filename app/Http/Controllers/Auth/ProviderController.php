<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;


class ProviderController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }
    public function callback($provider)
    {
        try {
            $social_user = Socialite::driver($provider)->user();

            $data = User::where('email', $social_user->email)->first();
            if(is_null($data)){
                $users['provider_id'] = $social_user->id;
                $users['provider'] = $provider;
                $users['provider_token'] = $social_user->token;
                $users['name'] = $social_user->name;
                $users['email'] = $social_user->email;
                $users['email_verified_at'] = now();
                $data = User::create($users);
            }else{
                $users['provider_id'] = $social_user->id;
                $users['provider'] = $provider;
                $users['provider_token'] = $social_user->token;
                User::where('id', $data->id)->update($users);
                $data = User::where('id', $data->id)->first();
            }

            Auth::login($data);

            return redirect('/dashboard');
        }catch(\Exception $e){
            return redirect('/login');
        }
    }
}
