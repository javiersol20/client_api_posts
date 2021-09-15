<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait token{
    public function getAccessToken($user, $service)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',

        ])->post(config('services.javiersolis.url_api') .'/oauth/token', [
            'grant_type' => 'password',
            'client_id' => config('services.javiersolis.client_id'),
            'client_secret' => config('services.javiersolis.client_secret'),
            'username' => request('email'),
            'password' => request('password'),
        ]);

        $access_token = $response->json();

        //addSecond agrega los segundos a la hora actual
        $user->accessToken()->create([
            'service_id' => $service['data']['id'],
            'access_token' => $access_token['access_token'],
            'refresh_token' => $access_token['refresh_token'],
            'expires_at' => now()->addSecond($access_token['expires_in'])
        ]);

    }
}
