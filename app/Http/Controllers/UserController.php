<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Exceptions\UserExcists;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\RegisterRequest;
use App\Models\Opt;
use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
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
        $this->sendOTP($user);
        return view('pages.forms.otp',["user_id"=>$user->id]);
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
        //!handle else
        else{
            return redirect()->route('error')->withErrors("wrong credentials invalid username or password");
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

    public function sendOTP($user){
        $otp = rand(10000000,99999999);
        $time = time();
        $user_otp=Otp::updateOrCreate(
            ["email"=>$user->email]
            ,[
                "email"=>$user->email,
                "otp"=>$otp,
                "created_at"=>$time,
            ]);

        $data["email"]=$user_otp->email;
        $data["otp"]=$user_otp->otp;
        Mail::send('pages.emails.your_otp', ['data'=>$data], function ($message)use($data) {
            $message->to($data["email"]);
            $message->subject('Verify Your Email');
        });
    }

    public function handleOTP(Request $request){
        $validatedData = $request->validate([
            'otp' => 'required|numeric',
        ]);
        $user_data = User::where("id",decrypt($request->user_id))->first();
        $otp_data = Otp::where("email",$user_data->email)->first();
        if($otp_data->otp == $request->otp){
            if($user_data->email == $otp_data->email){
                $user_data->update([
                    "email_verified_at"=>time(),
                ]);
                return view("pages.intro.intro");
            }else{
                // ! handle errors
                return view('pages.errors.confirmed');
            }
        }else{
            return view('pages.errors.confirmed');
        }
    }


    // // ? try the exception

    // public function try_exception($id){
    //     $user = User::find($id);
    //     if(!$user){
    //         throw new UserExcists("you not in our community yet !!");
    //     }
    //     return view('pages.intro.intro');

    // }

    // //? try localization

    // public function loca($lang){
    //     App::setLocale($lang);
    //     Session::put("lang",$lang);
    //     return redirect()->back();
    // }

    

}
