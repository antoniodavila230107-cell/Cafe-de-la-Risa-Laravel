<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role?->name === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            if ($user->role?->name === 'reception') {
                return redirect()->route('reception.index');
            }
            return redirect()->route('store.comprar');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->role?->name === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            }

            if ($user->role?->name === 'reception') {
                return redirect()->intended(route('reception.index'));
            }

            return redirect()->intended(route('store.comprar'));
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no son válidas.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('store.comprar')->with('info', 'Sesión cerrada correctamente.');
    }
}
