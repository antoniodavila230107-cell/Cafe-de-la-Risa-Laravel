@extends('layouts.store')

@section('title', 'Acerca del Proyecto — Café de la Risa')

@section('content')
<div style="max-width: 800px; margin: 2rem auto; background: white; padding: 2.5rem; border-radius: 16px; box-shadow: 0 10px 25px rgba(62,39,35,0.12);">
    <div style="text-align: center; margin-bottom: 2rem;">
        <img src="{{ asset('images/cafe-risa-logo-principal.png') }}" alt="Logo Café de la Risa" style="width: 120px; height: auto; margin-bottom: 1rem;">
        <h2 style="color: #3E2723; font-size: 2rem; font-weight: 700;">Café de la Risa — Aplicación Web Profesional</h2>
        <p style="color: #8D6E63; font-size: 1.1rem; font-weight: 600;">Sistema de Gestión de Cafetería y POS Monolítico (Laravel + MySQL)</p>
    </div>

    <div style="background: #F5F2EB; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border-left: 4px solid #3E2723;">
        <h3 style="color: #3E2723; margin-bottom: 8px;">📌 Resumen de la Arquitectura</h3>
        <p style="color: #4E342E; font-size: 0.95rem; line-height: 1.6;">
            Esta aplicación web demostrativa fue completamente migrada desde un prototipo original en C# ASP.NET Core a un sistema web moderno en <strong>Laravel + MySQL</strong>.
            Toda la persistencia operativa en tiempo de ejecución depende al 100% del motor de base de datos relacional MySQL, eliminando por completo cualquier archivo JSON de datos.
        </p>
    </div>

    <h3 style="color: #3E2723; margin-bottom: 1rem;">🛠️ Tecnologías y Características Principales</h3>
    <ul style="list-style: none; margin-bottom: 2rem;">
        <li style="padding: 10px 0; border-bottom: 1px solid #EFEBE9;">
            <strong>Framework Web:</strong> Laravel 13 (Arquitectura MVC monolítica responsiva).
        </li>
        <li style="padding: 10px 0; border-bottom: 1px solid #EFEBE9;">
            <strong>Base de Datos Relacional:</strong> MySQL Server 8.4 (`cafeteria_profesional`) con integridad referencial y transacciones ACID.
        </li>
        <li style="padding: 10px 0; border-bottom: 1px solid #EFEBE9;">
            <strong>Descuento Automatizado por Receta:</strong> Cada venta procesada en la tienda descuenta automáticamente la materia prima correspondiente del inventario de insumos.
        </li>
        <li style="padding: 10px 0; border-bottom: 1px solid #EFEBE9;">
            <strong>Códigos QR de Uso Único:</strong> Cada pedido genera un token QR seguro de uso único para su validación en recepción.
        </li>
        <li style="padding: 10px 0; border-bottom: 1px solid #EFEBE9;">
            <strong>Scheduler de Mesas:</strong> Automatización que libera mesas reservadas cuya vigencia de 15 minutos ha expirado.
        </li>
    </ul>

    <div style="text-align: center;">
        <a href="{{ route('store.comprar') }}" style="display: inline-block; background: #3E2723; color: white; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: 700;">
            Ir al Menú & Realizar Pedido
        </a>
    </div>
</div>
@endsection
