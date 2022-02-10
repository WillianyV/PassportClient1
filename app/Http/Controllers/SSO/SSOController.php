<?php

namespace App\Http\Controllers\SSO;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class SSOController extends Controller
{
    public function redirect(Request $request)
    {
        // $request->session()->put('state', $state = Str::random(40));

        /** TBM PD SER FEITA DESSA FORMA */
        $state = Str::random(40);
        session([
            'state' => $state
        ]);

        $query = http_build_query([
            'client_id' => config('auth.client_id'),
            'redirect_uri' => config('auth.redirect_uri'),
            'response_type' => 'code',
            'scope' => '',
            'state' => $state,
        ]);
    
        return redirect(config('auth.app_url').'oauth/authorize?'.$query);
    }

    public function callback(Request $request)
    {
        // $state = $request->session()->pull('state');

        // throw_unless(
        //     strlen($state) > 0 && $state === $request->state,
        //     InvalidArgumentException::class
        // );

        /** NA AULA DE Beer and Code, ELES NÃO VERIFICAM E PEGAM O STATE
         * POIS ELES FAZEM DA SEGUNDA FORMA NO REDIREC
         */
        // $state = $request->session()->pull('state');

        // throw_unless(
        //     strlen($state) > 0 && $state === $request->state,
        //     InvalidArgumentException::class
        // );
    
        $response = Http::asForm()->post(config('auth.app_url').'oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => config('auth.client_id'),
            'client_secret' => config('auth.client_secret'),
            'redirect_uri' => config('auth.redirect_uri'),
            'code' => $request->code,
        ]);
    
        $request->session()->put($response->json());
    
        return redirect(route("sso.connect"));
    }

    public function connect(Request $request)
    {
        $accessToken = $request->session()->get("access_token");

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$accessToken,
        ])->get(config('auth.app_url').'api/user');
        
        $user_array =  $response->json();
        try {
            $email = $user_array['email'];
        } catch (\Throwable $th) {
            return redirect("login")->withErrors("Falha ao pegar informações do Login! Tente Novamente.");
        }

        $user = User::where("email", $email)->first();

        if (!$user) {
            $user = User::create([
                'name'  => $user_array['name'],
                'email' => $user_array['email'],
            ]);
        }

        Auth::login($user);

        // return redirect(route("home"));
        return redirect(config('auth.app_url'));
    }
}
