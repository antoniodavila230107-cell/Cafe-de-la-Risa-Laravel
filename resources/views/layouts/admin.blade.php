<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Administración — Café de la Risa')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #2C1810;
            --sidebar-active: #4E342E;
            --primary-coffee: #3E2723;
            --accent-gold: #D7CCC8;
            --bg-light: #F5F2EB;
            --card-bg: #FFFFFF;
            --text-dark: #1E100A;
            --green-success: #2E7D32;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            color: #FFFFFF;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.2);
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 3px;
        }
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.4);
        }

        .sidebar-brand {
            padding: 1.5rem 1rem;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            position: sticky;
            top: 0;
            background: var(--sidebar-bg);
            z-index: 10;
        }

        .sidebar-brand img {
            width: 42px;
            height: auto;
        }

        .sidebar-brand h2 {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0.5rem 0 2rem 0;
            flex: 1;
        }

        .sidebar-item a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.8rem 1.5rem;
            color: var(--accent-gold);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }

        .sidebar-item a:hover,
        .sidebar-item.active a {
            background-color: var(--sidebar-active);
            color: #FFFFFF;
            border-left: 4px solid var(--accent-gold);
        }

        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            font-size: 0.85rem;
            color: var(--accent-gold);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .content-wrapper {
            margin-left: 260px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .admin-header {
            background-color: #FFFFFF;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .admin-main {
            padding: 2rem;
            flex: 1;
        }

        .btn-logout {
            background: none;
            border: 1px solid var(--sidebar-active);
            color: var(--accent-gold);
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
    @yield('styles')
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('images/cafe-risa-mascota.png') }}" alt="Mascota Café de la Risa">
            <div>
                <h2>Café de la Risa</h2>
                <small style="color: var(--accent-gold); font-size: 0.7rem;">Panel Pos / Admin</small>
            </div>
        </div>

        <ul class="sidebar-menu">
            <li class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}">📊 Dashboard</a>
            </li>
            <li class="sidebar-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <a href="{{ route('admin.orders.index') }}">📋 Pedidos & Delivery</a>
            </li>
            <li class="sidebar-item {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                <a href="{{ route('admin.employees.index') }}">👥 Empleados & Sueldos</a>
            </li>
            <li class="sidebar-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <a href="{{ route('admin.products.index') }}">☕ Productos</a>
            </li>
            <li class="sidebar-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <a href="{{ route('admin.categories.index') }}">📂 Categorías</a>
            </li>
            <li class="sidebar-item {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
                <a href="{{ route('admin.inventory.index') }}">📦 Inventario</a>
            </li>
            <li class="sidebar-item {{ request()->routeIs('admin.recipes.*') ? 'active' : '' }}">
                <a href="{{ route('admin.recipes.index') }}">📜 Recetas</a>
            </li>
            <li class="sidebar-item {{ request()->routeIs('admin.tables.*') ? 'active' : '' }}">
                <a href="{{ route('admin.tables.index') }}">🪑 Mesas & Zonas</a>
            </li>
            <li class="sidebar-item {{ request()->routeIs('admin.cash.*') ? 'active' : '' }}">
                <a href="{{ route('admin.cash.index') }}">💵 Caja & Turnos</a>
            </li>
            <li class="sidebar-item {{ request()->routeIs('admin.expenses.*') ? 'active' : '' }}">
                <a href="{{ route('admin.expenses.index') }}">💸 Gastos Operativos</a>
            </li>
            <li class="sidebar-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <a href="{{ route('admin.reports.index') }}">📈 Reportes & Ganancias</a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('kitchen.index') }}" target="_blank">👨‍🍳 Monitor de Cocina</a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('store.comprar') }}" target="_blank">🛒 Terminal Cliente</a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('reception.index') }}">📱 Recepción / QR</a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <span>{{ auth()->user()->name ?? 'Admin' }}</span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">Salir</button>
            </form>
        </div>
    </aside>

    <div class="content-wrapper">
        <header class="admin-header">
            <h3>@yield('header_title', 'Dashboard Operativo')</h3>
            <span style="background: #E8F5E9; color: #2E7D32; padding: 4px 12px; border-radius: 16px; font-weight: 600; font-size: 0.85rem;">
                MySQL Conectado
            </span>
        </header>

        <main class="admin-main">
            @yield('content')
        </main>
    </div>

    @yield('scripts')
</body>
</html>
