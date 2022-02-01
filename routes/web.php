<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/redirect', function (Request $request) {
    $request->session()->put('state', $state = Str::random(40));

    $query = http_build_query([
        'client_id' => config('auth.client_id'),
        'redirect_uri' => config('auth.redirect_uri'),
        'response_type' => 'code',
        'scope' => '',
        'state' => $state,
    ]);

    return redirect(config('auth.app_url').'oauth/authorize?'.$query);
});

Route::get('/callback', function (Request $request) {
    $state = $request->session()->pull('state');

    throw_unless(
        strlen($state) > 0 && $state === $request->state,
        InvalidArgumentException::class
    );

    $response = Http::asForm()->post(config('auth.app_url').'oauth/token', [
        'grant_type' => 'authorization_code',
        'client_id' => config('auth.client_id'),
        'client_secret' => config('auth.client_secret'),
        'redirect_uri' => config('auth.redirect_uri'),
        'code' => $request->code,
    ]);

    $request->session()->put($response->json());

    return redirect("/connect");
});

Route::get('/connect', function (Request $request) {
    $accessToken = $request->session()->get("access_token");

    $response = Http::withHeaders([
        'Accept' => 'application/json',
        'Authorization' => 'Bearer '.$accessToken,
    ])->get(config('auth.app_url').'api/user');
    
    return $response->json();
});