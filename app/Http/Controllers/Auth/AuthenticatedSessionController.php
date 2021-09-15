<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Traits\token;

class AuthenticatedSessionController extends Controller
{
    use token;
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {


        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $response = Http::withHeaders([
            'Accept' => 'application/json'
        ])->post(config('services.javiersolis.url_api') .'/v1/login', [
            'email' => $request->email,
            'password' => $request->password
        ]);

        if($response->status() == 404)
        {
            return back()->withErrors('These credentials do not match our records.');
        }
        $service = $response->json();

        /**
         *  el metodo firstOrCreate lo que hace es
         * que busca primero el campo y si ya existe
         * lo almacena en la variable, pero si no existe
         * lo crea, ahora el metodo updateOrCreate lo que hace
         * es lo mismo al firstOrCreate con la diferencia
         * que hace la peticion y comprueba si existe y si
         * si existe en email ya no lo registra pero lo actualiza
         * y si no existe lo cre
         */

        $user = User::updateOrCreate([
            'email' => $request->email,
        ], $service['data']);

        if(!$user->accessToken)
        {

            $this->getAccessToken($user, $service);

        }

        Auth::login($user, $request->remember);
        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
