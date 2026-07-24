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

        /* Profile dropdown */
        .profile-btn {
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.25);
            color: white;
            padding: 7px 14px;
            border-radius: 24px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            transition: background 0.2s;
        }
        .profile-btn:hover { background: rgba(255,255,255,0.22); }
        .profile-avatar {
            width: 28px; height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, #D7CCC8, #8D6E63);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.85rem; color: white;
            flex-shrink: 0;
        }
        .profile-dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 28px rgba(0,0,0,0.18);
            min-width: 220px;
            z-index: 500;
            overflow: hidden;
        }
        .profile-dropdown.open { display: block; }
        .profile-dropdown-header {
            background: #F5F2EB;
            padding: 14px 16px;
            border-bottom: 1px solid #E8E0D8;
        }
        .profile-dropdown-header p { color: #3E2723; font-weight: 700; font-size: 0.95rem; margin-bottom: 2px; }
        .profile-dropdown-header small { color: #8D6E63; font-size: 0.8rem; }
        .profile-dropdown a, .profile-dropdown button {
            display: flex; align-items: center; gap: 10px;
            width: 100%; padding: 12px 16px;
            color: #3E2723; font-size: 0.9rem; font-weight: 500;
            text-decoration: none; background: none; border: none;
            cursor: pointer; transition: background 0.15s; text-align: left;
        }
        .profile-dropdown a:hover, .profile-dropdown button:hover { background: #FFF8E1; }
        .profile-dropdown .danger { color: #c62828; }
        .profile-dropdown .danger:hover { background: #FFEBEE; }
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

            {{-- Botón de Perfil --}}
            <div style="position:relative;">
                <button class="profile-btn" id="profileBtn" onclick="toggleProfileMenu()">
                    <div class="profile-avatar">
                        @auth
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        @else
                            👤
                        @endauth
                    </div>
                    @auth
                        <span style="max-width:100px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                            {{ explode(' ', auth()->user()->name)[0] }}
                        </span>
                    @else
                        <span>Ingresar</span>
                    @endauth
                    <span style="font-size:0.7rem; opacity:0.7;">▼</span>
                </button>

                <div class="profile-dropdown" id="profileDropdown">
                    @auth
                        <div class="profile-dropdown-header">
                            <p>{{ auth()->user()->name }}</p>
                            <small>{{ auth()->user()->email }}</small>
                        </div>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" style="color: #3E2723; font-weight: 700; background: #FFF8E1;">
                                📊 Panel de Administración
                            </a>
                        @elseif(auth()->user()->isReception())
                            <a href="{{ route('reception.index') }}" style="color: #3E2723; font-weight: 700; background: #FFF8E1;">
                                📋 Panel de Recepción
                            </a>
                        @endif
                        <a href="{{ route('profile.index') }}">
                            👤 Ver mi Perfil
                        </a>
                        <a href="{{ route('store.comprar') }}">
                            🛒 Hacer un Pedido
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="danger">
                                🚪 Cerrar Sesión
                            </button>
                        </form>
                    @else
                        <div class="profile-dropdown-header">
                            <p>No has iniciado sesión</p>
                            <small>Inicia sesión para pedir más rápido</small>
                        </div>
                        <a href="{{ route('auth.google') }}">
                            <svg width="16" height="16" viewBox="0 0 18 18"><path fill="#4285F4" d="M17.64 9.2c0-.74-.06-1.28-.19-1.84H9v3.34h4.96c-.1.83-.64 2.08-1.84 2.92l2.84 2.2c1.7-1.57 2.68-3.88 2.68-6.62z"/><path fill="#34A853" d="M9 18c2.43 0 4.47-.8 5.96-2.18l-2.84-2.2c-.76.53-1.78.9-3.12.9-2.38 0-4.41-1.57-5.13-3.72L.97 13.06C2.45 16 5.48 18 9 18z"/><path fill="#FBBC05" d="M3.87 10.8c-.18-.53-.28-1.1-.28-1.8s.1-1.27.28-1.8L.97 4.94C.35 6.17 0 7.55 0 9s.35 2.83.97 4.06l2.9-2.26z"/><path fill="#EA4335" d="M9 3.58c1.32 0 2.5.45 3.44 1.35l2.58-2.58C13.46.89 11.43 0 9 0 5.48 0 2.45 2 1.07 4.94l2.9 2.26C4.6 5.15 6.62 3.58 9 3.58z"/></svg>
                            Continuar con Google
                        </a>
                        <a href="{{ route('login') }}" style="border-top: 1px solid #EFEBE9; color: #8D6E63; font-size: 0.85rem;">
                            🔐 Acceso Personal / Admin
                        </a>
                    @endauth
                </div>
            </div>
        </nav>
    </header>

    <main class="main-container">
        @yield('content')
    </main>

    <footer class="footer">
        <p>&copy; {{ date('Y') }} Café de la Risa — Todos los derechos reservados.</p>
        <p style="margin-top: 6px;">
            <a href="{{ route('login') }}" style="color: var(--accent-gold); text-decoration: none; opacity: 0.75; font-size: 0.8rem;">
                🔐 Acceso al Sistema Administrativo (Personal)
            </a>
        </p>
    </footer>

    @yield('scripts')
    <script>
        function toggleProfileMenu() {
            document.getElementById('profileDropdown').classList.toggle('open');
        }
        document.addEventListener('click', function(e) {
            const btn = document.getElementById('profileBtn');
            const menu = document.getElementById('profileDropdown');
            if (btn && !btn.contains(e.target) && menu && !menu.contains(e.target)) {
                menu.classList.remove('open');
            }
        });
    </script>
</body>
</html>

