<?php

use App\Http\Controllers\SSO\SSOController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes(['register' => false, 'reset' => false]);

Route::get("/sso/redirect", [SSOController::class, 'redirect'])->name("sso.redirect");
Route::get("/callback", [SSOController::class, 'callback'])->name("sso.callback");
Route::get("/sso/connect", [SSOController::class, 'connect'])->name("sso.connect");


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/', function () {
    return redirect('/home');
});

// Route::get('/login', function () {
//     return redirect('/sso/redirect');
// });