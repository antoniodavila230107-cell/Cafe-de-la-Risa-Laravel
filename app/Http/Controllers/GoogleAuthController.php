<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        if (config('services.google.client_id')) {
            return Socialite::driver('google')->redirect();
        }

        // Demo / Fallback Google login simulation if Google Cloud credentials are not configured yet
        $customerRole = Role::firstOrCreate(['name' => 'customer'], ['display_name' => 'Cliente']);

        $demoUser = User::firstOrCreate(
            ['email' => 'cliente_google@gmail.com'],
            [
                'role_id' => $customerRole->id,
                'name' => 'Cliente Google (Demo)',
                'password' => bcrypt(Str::random(16)),
            ]
        );

        Auth::login($demoUser);
        session(['google_customer_name' => $demoUser->name, 'google_customer_email' => $demoUser->email]);

        return redirect()->route('store.comprar')->with('success', '¡Bienvenido! Sesión iniciada con tu cuenta de Google.');
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $customerRole = Role::firstOrCreate(['name' => 'customer'], ['display_name' => 'Cliente']);

            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'role_id' => $customerRole->id,
                    'name' => $googleUser->getName(),
                    'password' => bcrypt(Str::random(16)),
                ]
            );

            Auth::login($user);
            session(['google_customer_name' => $user->name, 'google_customer_email' => $user->email]);

            return redirect()->route('store.comprar')->with('success', "¡Hola {$user->name}! Has iniciado sesión con Google.");
        } catch (\Exception $e) {
            return redirect()->route('store.comprar')->with('error', 'Ocurrió un error al autenticar con Google.');
        }
    }
}
