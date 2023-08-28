<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class Redirect extends Controller
{
    public function welcome(){
        return view('index');
    }

    public function login(){
        return view('login');
    }

    public function register(){
        return view('register');
    }
    public function intro(){
        return view('pages.intro.intro');
    }

    public function forget_password(){
        return view('pages.forms.forget_password');
    }

    public function reset_password(){
        return view('pages.forms.reset_password');
    }

    public function errors(){
        return view('pages.errors.confirmed');
    }

    public function send_email(){
        return view('pages.emails.verfiy');
    }
}


