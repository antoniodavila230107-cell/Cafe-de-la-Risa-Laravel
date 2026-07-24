<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Recepción & QR — Café de la Risa')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .reception-header {
            background-color: var(--primary-coffee);
            color: #FFFFFF;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .reception-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #FFFFFF;
        }

        .reception-brand img {
            height: 40px;
        }

        .reception-container {
            max-width: 1100px;
            width: 100%;
            margin: 2rem auto;
            padding: 0 1.5rem;
            flex: 1;
        }
    </style>
    @yield('styles')
</head>
<body>

    <header class="reception-header">
        <a href="{{ route('reception.index') }}" class="reception-brand">
            <img src="{{ asset('images/cafe-risa-logo-principal.png') }}" alt="Logo">
            <h2>Café de la Risa — Recepción & QR</h2>
        </a>

        <div style="display: flex; align-items: center; gap: 15px;">
            <a href="{{ route('store.comprar') }}" style="color: var(--accent-gold); text-decoration: none;">Ver Tienda</a>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" style="color: var(--accent-gold); text-decoration: none;">Panel Admin</a>
            @endif
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" style="background: none; border: 1px solid var(--accent-gold); color: white; padding: 4px 10px; border-radius: 4px; cursor: pointer;">Salir</button>
            </form>
        </div>
    </header>

    <main class="reception-container">
        @yield('content')
    </main>

</body>
</html>
