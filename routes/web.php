<?php

use App\Http\Controllers\ForgetPasswordController;
use App\Http\Controllers\Redirect;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get("FTRU",[Redirect::class,"welcome"])->name("FTRU");
Route::get("FTRU/login",[Redirect::class,"login"])->name("login");
Route::get("FTRU/signup",[Redirect::class,"register"])->name("signup");

Route::post("FTRU/signup",[UserController::class,"handleRegister"])->name("new user");
Route::post("FTRU/login",[UserController::class,"handleLogin"])->name("access user");

Route::get('/auth/{provider}/redirect',[UserController::class,"redirect"]);
Route::get('/auth/{provider}/callback',[UserController::class, 'callback']);

Route::get("FTRU/home",[Redirect::class,"intro"])->name("home")->middleware('auth');

Route::post("FTRU/logout",[UserController::class,"logout"])->name("logout");

Route::get("FTRU/signin/forget",[ForgetPasswordController::class,"forget_password"])->name("forget_pass");
Route::post('FTRU/signin/forgetpassword',[ForgetPasswordController::class,'forgetPasswordHandle'])->name('forget_password_handle');

Route::get("FTRU/reset/{token}",[ForgetPasswordController::class,"reset_password"])->name("reset_pass");
Route::post("FTRU/reset",[ForgetPasswordController::class, "resetPasswordHandle"])->name("reset_password_handle");

Route::get("FTRU/error",[Redirect::class,"errors"])->name("error");
Route::get("FTRU/email",[Redirect::class,"send_email"])->name("email");

Route::post("FTRU/verfiy",[UserController::class,"handleOTP"])->name("verfiy");


// !try 

Route::get("error",[Redirect::class,"errors"])->name("error");
Route::get("hui",function(){
    return view('pages.emails.verfiy');
});
Route::get("errrrr",function(){
    return view('pages.errors.confirmed');
});

Route::get("user/{id}",[UserController::class,"try_exception"]);

Route::get("kop/{lang}",[UserController::class,"loca"])->name('lang');

Route::get("huop",function(){
    return view('pages.errors.lang_confirmed');
})->middleware("lang");
