<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use Laravel\Socialite\Facades\Socialite;

class UserController extends Controller
{
    // handle register from the form
    public function handleRegister(RegisterRequest $request){
        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password"=>Hash::make($request->password)
        ]);
        Auth::login($user);

        return view('pages.intro.intro');
    }

    public function redirect($provider){
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider){
        $socialUser = Socialite::driver($provider)->user();
        // ! why?
        $user = User::where('email',$socialUser->getEmail())->exists();
        if($user){
            return redirect(route("signup"))->withErrors(['email'=>'This email uses different method to login ']);
        }
        else{
            $user=User::create([
            'name' => $socialUser->getName(),
            'email' => $socialUser->getEmail(),
            'provider_id' => $socialUser->getId(),
            'provider_name' => $provider,
            'provider_token' => $socialUser->token,
            'password' => Hash::make($socialUser->password),
            ]);
        }
        Auth::login($user);
        return view('pages.intro.intro');
    }

    public function handleLogin(LoginRequest $request){
        $is_login= Auth::attempt(["email"=>$request->email,"password"=>$request->password],$request->filled('remember'));
        if($is_login){
            return redirect()->route('home');
        }
    }

    public function logout(){
        $user = Auth::user();

        Auth::logout();

        if ($user) {
            $user->setRememberToken(null);
            $user->save();
        }

        return redirect()->route('access user');
    }


}
