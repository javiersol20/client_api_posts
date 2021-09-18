<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Http;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function resolveAuthorization()
    {
        if(auth()->user()->accessToken->expires_at <= now())
        {


            $response = Http::withHeaders([
                'Accept' => 'application/json',

            ])->post(config('services.javiersolis.url_api') .'/oauth/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => auth()->user()->accessToken->refresh_token,
                'client_id' => config('services.javiersolis.client_id'),
                'client_secret' => config('services.javiersolis.client_secret'),

            ]);

            $access_token = $response->json();

            //addSecond agrega los segundos a la hora actual
            auth()->user()->accessToken->update([
                'access_token' => $access_token['access_token'],
                'refresh_token' => $access_token['refresh_token'],
                'expires_at' => now()->addSecond($access_token['expires_in'])
            ]);
        }
    }
}
