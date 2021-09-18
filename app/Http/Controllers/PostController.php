<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PostController extends Controller
{

    public function store()
    {

        /**
         * El token solo tiene una vida, una vez recargado lo que hara es expirar
         */

        $this->resolveAuthorization();
        /*-----------------------------------------------------------------------*/
       $reponse =  Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '. auth()->user()->accessToken->access_token
        ])
        ->post(config('services.javiersolis.url_api').'/v1/posts', [
                'name' => 'ESTE ES UN NOMBRE DEL CLIENTE',
                'slug' => 'esto-es-slug-pruebaaaaa',
                'extract' => 'esto-es-slug-prueba',
                'body' => 'esto-es-slug-prueba',
                'category_id' => 1
        ]);

       return $reponse->json();
    }

}
