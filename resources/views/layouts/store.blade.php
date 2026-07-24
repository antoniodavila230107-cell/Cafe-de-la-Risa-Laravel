<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Café de la Risa — Menú en Línea')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-coffee: #3E2723;
            --secondary-caramel: #8D6E63;
            --accent-gold: #D7CCC8;
            --bg-cream: #FFF8E1;
            --card-bg: #FFFFFF;
            --text-dark: #2C1810;
            --text-muted: #6D4C41;
            --green-success: #2E7D32;
            --shadow-sm: 0 2px 8px rgba(62, 39, 35, 0.08);
            --shadow-md: 0 4px 16px rgba(62, 39, 35, 0.12);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg-cream);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header {
            background-color: var(--primary-coffee);
            color: #FFFFFF;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-md);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #FFFFFF;
        }

        .header-brand img {
            height: 48px;
            width: auto;
            border-radius: 8px;
        }

        .header-brand h1 {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .header-nav {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .nav-link {
            color: var(--accent-gold);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-link:hover {
            color: #FFFFFF;
        }

        .btn-cart {
            background: linear-gradient(135deg, #8D6E63, #5D4037);
            color: #FFFFFF;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 24px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .btn-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(0,0,0,0.3);
        }

        .cart-badge {
            background-color: #FF7043;
            color: white;
            font-size: 0.8rem;
            padding: 2px 8px;
            border-radius: 12px;
        }

        .main-container {
            flex: 1;
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }

        .footer {
            background-color: var(--primary-coffee);
            color: var(--accent-gold);
            text-align: center;
            padding: 1.5rem;
            font-size: 0.9rem;
            margin-top: auto;
        }

        @media (max-width: 768px) {
            .header {
                padding: 0.8rem 1rem;
            }
            .header-brand h1 {
                font-size: 1.2rem;
            }
            .main-container {
                padding: 1rem;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <header class="header">
        <a href="{{ route('store.comprar') }}" class="header-brand">
            <img src="{{ asset('images/cafe-risa-logo-principal.png') }}" alt="Café de la Risa Logo">
            <div>
                <h1>Café de la Risa</h1>
                <small style="color: var(--accent-gold); font-size: 0.75rem;">Sabor que hace sonreír</small>
            </div>
        </a>

        <nav class="header-nav">
            <a href="{{ route('store.comprar') }}" class="nav-link">Menú</a>

            <button class="btn-cart" id="openCartBtn">
                🛒 Carrito <span class="cart-badge" id="cartCount">0</span>
            </button>
        </nav>
    </header>

    <main class="main-container">
        @yield('content')
    </main>

    <footer class="footer">
        <p>&copy; {{ date('Y') }} Café de la Risa — Todos los derechos reservados.</p>
    </footer>

    @yield('scripts')
</body>
</html>
