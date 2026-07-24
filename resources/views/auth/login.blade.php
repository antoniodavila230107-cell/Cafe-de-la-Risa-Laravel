@extends('layouts.store')

@section('title', 'Iniciar Sesión — Café de la Risa')

@section('content')
<div style="max-width: 440px; margin: 3rem auto; background: white; padding: 2.5rem; border-radius: 16px; box-shadow: 0 10px 25px rgba(62,39,35,0.12);">
    <div style="text-align: center; margin-bottom: 2rem;">
        <img src="{{ asset('images/cafe-risa-mascota.png') }}" alt="Mascota Café de la Risa" style="height: 70px; margin-bottom: 12px;">
        <h2 style="color: #3E2723; font-size: 1.6rem; font-weight: 700;">Acceso Administrativo</h2>
        <p style="color: #6D4C41; font-size: 0.9rem; margin-top: 4px;">Ingresa tus credenciales para administrar o atender recepción</p>
    </div>

    @if($errors->any())
        <div style="background-color: #FFEBEE; color: #C62828; padding: 0.8rem; border-radius: 8px; font-size: 0.9rem; margin-bottom: 1.5rem;">
            {{ $errors->first() }}
        </div>
    @endif

    @if(session('info'))
        <div style="background-color: #E3F2FD; color: #1565C0; padding: 0.8rem; border-radius: 8px; font-size: 0.9rem; margin-bottom: 1.5rem;">
            {{ session('info') }}
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf
        <div style="margin-bottom: 1.2rem;">
            <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.9rem; color: #3E2723;">Correo Electrónico</label>
            <input type="email" name="email" value="{{ old('email') }}" required style="width: 100%; padding: 0.75rem; border: 1px solid #D7CCC8; border-radius: 8px; font-size: 1rem; outline: none;">
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.9rem; color: #3E2723;">Contraseña</label>
            <input type="password" name="password" required style="width: 100%; padding: 0.75rem; border: 1px solid #D7CCC8; border-radius: 8px; font-size: 1rem; outline: none;">
        </div>

        <button type="submit" style="width: 100%; background: linear-gradient(135deg, #3E2723, #5D4037); color: white; border: none; padding: 0.85rem; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: opacity 0.2s;">
            Iniciar Sesión
        </button>
    </form>

    <div style="margin-top: 2rem; background: #FFF8E1; padding: 1rem; border-radius: 8px; border: 1px solid #FFE082; font-size: 0.85rem; color: #4E342E;">
        <strong style="display: block; margin-bottom: 6px; color: #3E2723;">🔑 Credenciales Demostración:</strong>
        <p>• <strong>Admin:</strong> admin@cafedelarisa.com / <code>password</code></p>
        <p style="margin-top: 4px;">• <strong>Recepción:</strong> recepcion@cafedelarisa.com / <code>password</code></p>
    </div>
</div>
@endsection
