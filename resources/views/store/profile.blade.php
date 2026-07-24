@extends('layouts.store')

@section('title', 'Mi Perfil — Café de la Risa')

@section('styles')
<style>
    .profile-page {
        max-width: 900px;
        margin: 0 auto;
    }

    /* Hero Card */
    .profile-hero {
        background: linear-gradient(135deg, #3E2723 0%, #6D4C41 100%);
        border-radius: 20px;
        padding: 2.5rem;
        display: flex;
        align-items: center;
        gap: 2rem;
        margin-bottom: 2rem;
        color: white;
        position: relative;
        overflow: hidden;
    }
    .profile-hero::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 160px; height: 160px;
        background: rgba(255,255,255,0.06);
        border-radius: 50%;
    }
    .profile-hero::after {
        content: '';
        position: absolute;
        bottom: -60px; left: 40%;
        width: 220px; height: 220px;
        background: rgba(255,255,255,0.04);
        border-radius: 50%;
    }

    .profile-avatar-lg {
        width: 88px; height: 88px;
        border-radius: 50%;
        background: linear-gradient(135deg, #D7CCC8, #BCAAA4);
        display: flex; align-items: center; justify-content: center;
        font-size: 2.2rem; font-weight: 800; color: #3E2723;
        border: 4px solid rgba(255,255,255,0.3);
        flex-shrink: 0;
        z-index: 1;
    }
    .profile-hero-info { z-index: 1; flex: 1; }
    .profile-hero-info h2 { font-size: 1.7rem; font-weight: 800; margin-bottom: 4px; }
    .profile-hero-info p { opacity: 0.8; font-size: 0.95rem; margin-bottom: 10px; }
    .google-badge {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.25);
        padding: 4px 12px; border-radius: 20px;
        font-size: 0.8rem; font-weight: 600;
    }
    .edit-profile-btn {
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.3);
        color: white; padding: 8px 18px;
        border-radius: 8px; font-weight: 600;
        cursor: pointer; font-size: 0.9rem;
        transition: background 0.2s; text-decoration: none;
        display: inline-flex; align-items: center; gap: 6px;
    }
    .edit-profile-btn:hover { background: rgba(255,255,255,0.25); color: white; }

    /* Stats */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .stat-card {
        background: white;
        border-radius: 14px;
        padding: 1.4rem 1.2rem;
        text-align: center;
        box-shadow: 0 2px 10px rgba(62,39,35,0.07);
        border: 1px solid #F0EBE8;
        transition: transform 0.2s;
    }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-icon { font-size: 1.8rem; margin-bottom: 8px; }
    .stat-value { font-size: 1.7rem; font-weight: 800; color: #3E2723; }
    .stat-label { font-size: 0.8rem; color: #8D6E63; font-weight: 600; margin-top: 2px; }

    /* Section cards */
    .section-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(62,39,35,0.07);
        border: 1px solid #F0EBE8;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    .section-header {
        padding: 1.2rem 1.5rem;
        border-bottom: 1px solid #F5F0EE;
        display: flex; justify-content: space-between; align-items: center;
    }
    .section-header h3 { font-size: 1.05rem; font-weight: 700; color: #3E2723; }
    .section-body { padding: 1.5rem; }

    /* Order history */
    .order-row {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 0.75rem;
        border: 1px solid #F5F0EE;
        transition: background 0.15s;
    }
    .order-row:hover { background: #FFFBF7; }
    .order-row:last-child { margin-bottom: 0; }

    .order-icon {
        width: 44px; height: 44px;
        border-radius: 10px;
        background: #FFF8E1;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem; flex-shrink: 0;
    }
    .order-info { flex: 1; }
    .order-folio { font-weight: 700; color: #3E2723; font-size: 0.95rem; }
    .order-meta { font-size: 0.8rem; color: #8D6E63; margin-top: 2px; }
    .order-amount { font-weight: 800; color: #3E2723; font-size: 1.05rem; }

    .badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
    }
    .badge-paid { background: #E8F5E9; color: #2E7D32; }
    .badge-pending { background: #FFF3E0; color: #E65100; }
    .badge-delivered { background: #E3F2FD; color: #1565C0; }
    .badge-preparing { background: #F3E5F5; color: #6A1B9A; }
    .badge-cancelled { background: #FFEBEE; color: #C62828; }

    .btn-qr {
        background: #3E2723;
        color: white;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex; align-items: center; gap: 4px;
        transition: background 0.2s;
    }
    .btn-qr:hover { background: #6D4C41; color: white; }

    /* Edit form */
    .form-group { margin-bottom: 1rem; }
    .form-group label { display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.9rem; color: #3E2723; }
    .form-input {
        width: 100%; padding: 10px 14px;
        border: 1px solid #D7CCC8; border-radius: 8px;
        font-size: 0.95rem; outline: none;
        transition: border-color 0.2s;
    }
    .form-input:focus { border-color: #8D6E63; }
    .form-input:disabled { background: #F5F0EE; color: #9E9E9E; }
    .btn-save {
        background: linear-gradient(135deg, #3E2723, #6D4C41);
        color: white; border: none;
        padding: 10px 24px; border-radius: 8px;
        font-weight: 700; cursor: pointer;
        font-size: 0.95rem; transition: opacity 0.2s;
    }
    .btn-save:hover { opacity: 0.9; }

    .empty-orders {
        text-align: center; padding: 3rem 1rem; color: #8D6E63;
    }
    .empty-orders .empty-icon { font-size: 3rem; margin-bottom: 1rem; }
    .empty-orders p { font-size: 1rem; margin-bottom: 1rem; }

    @media (max-width: 700px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .profile-hero { flex-direction: column; text-align: center; gap: 1rem; }
    }
</style>
@endsection

@section('content')
<div class="profile-page">

    @if(session('success'))
    <div style="background: #E8F5E9; color: #2E7D32; border: 1px solid #A5D6A7; border-radius: 8px; padding: 12px 16px; margin-bottom: 1.5rem; font-weight: 600;">
        ✅ {{ session('success') }}
    </div>
    @endif

    {{-- Hero --}}
    <div class="profile-hero">
        <div class="profile-avatar-lg">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div class="profile-hero-info">
            <h2>{{ $user->name }}</h2>
            <p>{{ $user->email }}</p>
            <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                <span class="google-badge">
                    <svg width="14" height="14" viewBox="0 0 18 18"><path fill="#4285F4" d="M17.64 9.2c0-.74-.06-1.28-.19-1.84H9v3.34h4.96c-.1.83-.64 2.08-1.84 2.92l2.84 2.2c1.7-1.57 2.68-3.88 2.68-6.62z"/><path fill="#34A853" d="M9 18c2.43 0 4.47-.8 5.96-2.18l-2.84-2.2c-.76.53-1.78.9-3.12.9-2.38 0-4.41-1.57-5.13-3.72L.97 13.06C2.45 16 5.48 18 9 18z"/><path fill="#FBBC05" d="M3.87 10.8c-.18-.53-.28-1.1-.28-1.8s.1-1.27.28-1.8L.97 4.94C.35 6.17 0 7.55 0 9s.35 2.83.97 4.06l2.9-2.26z"/><path fill="#EA4335" d="M9 3.58c1.32 0 2.5.45 3.44 1.35l2.58-2.58C13.46.89 11.43 0 9 0 5.48 0 2.45 2 1.07 4.94l2.9 2.26C4.6 5.15 6.62 3.58 9 3.58z"/></svg>
                    Cuenta Google
                </span>
                <span style="font-size:0.8rem; opacity:0.7;">Cliente desde {{ $user->created_at->format('M Y') }}</span>
            </div>
        </div>
        <a href="#edit-section" class="edit-profile-btn">✏️ Editar perfil</a>
    </div>

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">🛍️</div>
            <div class="stat-value">{{ $stats['total_orders'] }}</div>
            <div class="stat-label">Pedidos Totales</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-value">${{ number_format($stats['total_spent'], 0) }}</div>
            <div class="stat-label">Total Gastado</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-value">{{ $stats['orders_done'] }}</div>
            <div class="stat-label">Entregados</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⏳</div>
            <div class="stat-value">{{ $stats['orders_pending'] }}</div>
            <div class="stat-label">En Proceso</div>
        </div>
    </div>

    {{-- Historial de Pedidos --}}
    <div class="section-card">
        <div class="section-header">
            <h3>📋 Mis Pedidos</h3>
            <a href="{{ route('store.comprar') }}" style="font-size:0.85rem; color:#8D6E63; font-weight:600; text-decoration:none;">+ Nuevo pedido</a>
        </div>
        <div class="section-body">
            @forelse($orders as $order)
            <div class="order-row">
                <div class="order-icon">
                    @if($order->service_type === 'para_llevar') 🥡
                    @elseif($order->service_type === 'en_mesa') 🪑
                    @else ☕ @endif
                </div>
                <div class="order-info">
                    <div class="order-folio">Pedido #{{ $order->folio }}</div>
                    <div class="order-meta">
                        {{ $order->created_at->format('d M Y, H:i') }} ·
                        {{ $order->items->count() }} artículo(s) ·
                        @if($order->order_status === 'delivered')
                            <span class="badge badge-delivered">Entregado</span>
                        @elseif($order->order_status === 'preparing')
                            <span class="badge badge-preparing">Preparando</span>
                        @elseif($order->order_status === 'confirmed')
                            <span class="badge badge-paid">Confirmado</span>
                        @elseif($order->order_status === 'cancelled')
                            <span class="badge badge-cancelled">Cancelado</span>
                        @else
                            <span class="badge badge-pending">Pendiente</span>
                        @endif
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:12px; flex-shrink:0;">
                    <div class="order-amount">${{ number_format($order->total, 2) }}</div>
                    <a href="{{ route('store.confirmacion', $order->folio) }}" class="btn-qr">🔍 Ver QR</a>
                </div>
            </div>
            @empty
            <div class="empty-orders">
                <div class="empty-icon">🛒</div>
                <p>Aún no has realizado ningún pedido.</p>
                <a href="{{ route('store.comprar') }}" style="background:#3E2723; color:white; padding:10px 24px; border-radius:8px; font-weight:700; text-decoration:none; font-size:0.95rem;">
                    Ver el Menú
                </a>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Editar Perfil --}}
    <div class="section-card" id="edit-section">
        <div class="section-header">
            <h3>✏️ Editar Perfil</h3>
        </div>
        <div class="section-body">
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Nombre Completo</label>
                    <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required>
                    @error('name')<span style="color:#c62828; font-size:0.85rem;">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Correo Electrónico</label>
                    <input type="email" class="form-input" value="{{ $user->email }}" disabled>
                    <small style="color:#8D6E63; font-size:0.8rem;">El correo se gestiona desde tu cuenta de Google.</small>
                </div>
                <button type="submit" class="btn-save">Guardar Cambios</button>
            </form>
        </div>
    </div>

    {{-- Zona de peligro --}}
    <div class="section-card">
        <div class="section-header">
            <h3 style="color:#c62828;">🚪 Sesión</h3>
        </div>
        <div class="section-body" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
            <div>
                <p style="font-weight:600; color:#3E2723; margin-bottom:4px;">Cerrar Sesión</p>
                <p style="font-size:0.85rem; color:#8D6E63;">Se cerrará tu sesión en este dispositivo.</p>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" style="background:#c62828; color:white; border:none; padding:10px 22px; border-radius:8px; font-weight:700; cursor:pointer;">
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
