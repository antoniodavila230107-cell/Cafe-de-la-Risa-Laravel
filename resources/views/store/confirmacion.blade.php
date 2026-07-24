@extends('layouts.store')

@section('title', 'Seguimiento de Pedido — Café de la Risa')

@section('styles')
<style>
    .confirm-card {
        max-width: 720px;
        margin: 2rem auto;
        background: white;
        padding: 2.5rem;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(62,39,35,0.1);
    }

    /* Stepper Tracker */
    .tracker-container {
        margin: 2rem 0;
        background: #FAF7F2;
        padding: 1.5rem;
        border-radius: 16px;
        border: 1px solid #EFEBE9;
    }
    .tracker-title {
        text-align: center;
        font-weight: 700;
        color: #3E2723;
        font-size: 1.1rem;
        margin-bottom: 1.5rem;
    }
    .tracker-steps {
        display: flex;
        justify-content: space-between;
        position: relative;
    }
    .tracker-steps::before {
        content: '';
        position: absolute;
        top: 24px;
        left: 40px;
        right: 40px;
        height: 4px;
        background: #E0D7D0;
        z-index: 1;
    }
    .tracker-progress-line {
        position: absolute;
        top: 24px;
        left: 40px;
        height: 4px;
        background: #2E7D32;
        z-index: 1;
        transition: width 0.4s ease;
    }
    .step-item {
        position: relative;
        z-index: 2;
        text-align: center;
        flex: 1;
    }
    .step-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: white;
        border: 3px solid #E0D7D0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        margin: 0 auto 8px auto;
        transition: all 0.3s;
    }
    .step-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: #8D6E63;
    }
    .step-item.active .step-icon {
        border-color: #2E7D32;
        background: #E8F5E9;
        box-shadow: 0 0 0 4px rgba(46, 125, 50, 0.15);
    }
    .step-item.active .step-label {
        color: #2E7D32;
        font-weight: 700;
    }

    /* OXXO Voucher */
    .oxxo-voucher {
        background: #FFF3E0;
        border: 2px dashed #FFB74D;
        border-radius: 14px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        text-align: center;
    }
    .oxxo-code {
        font-family: monospace;
        font-size: 1.8rem;
        font-weight: 800;
        letter-spacing: 4px;
        color: #D84315;
        background: white;
        padding: 10px 20px;
        border-radius: 10px;
        display: inline-block;
        margin: 12px 0;
        border: 1px solid #FFE082;
    }

    /* Ticket Print View */
    @media print {
        header, footer, .btn-no-print, .tracker-container { display: none !important; }
        body { background: white !important; }
        .confirm-card {
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            max-width: 100% !important;
        }
        .print-ticket-header {
            display: block !important;
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 12px;
            margin-bottom: 12px;
        }
    }
</style>
@endsection

@section('content')

@php
    // Calcular progreso del tracker (3 estados)
    // Estado 1: Preparación (received, preparing)
    // Estado 2: En Camino / Listo (on_the_way, ready)
    // Estado 3: Entregado (delivered)
    $status = $order->order_status;
    $step = 1;
    $lineWidth = '0%';
    if (in_array($status, ['on_the_way', 'ready'])) {
        $step = 2;
        $lineWidth = '50%';
    } elseif ($status === 'delivered') {
        $step = 3;
        $lineWidth = '100%';
    }
@endphp

<div class="confirm-card">
    <div class="btn-no-print" style="text-align: right; margin-bottom: 1rem;">
        <button onclick="window.print()" style="background: #3E2723; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
            🖨️ Imprimir Ticket / Descargar PDF
        </button>
    </div>

    <div style="text-align: center; margin-bottom: 1.5rem;">
        <span style="font-size: 3rem;">🎉</span>
        <h2 style="color: #3E2723; font-size: 1.8rem; font-weight: 700; margin-top: 8px;">
            @if($order->service_type === 'delivery')
                ¡Pedido a Domicilio Registrado!
            @else
                ¡Pedido Confirmado para Recoger!
            @endif
        </h2>
        <p style="color: #6D4C41; font-size: 1rem;">Folio de Pedido: <strong>{{ $order->folio }}</strong></p>
    </div>

    {{-- Tracker de 3 Estados --}}
    <div class="tracker-container btn-no-print">
        <div class="tracker-title">📍 Estado de tu Pedido en Tiempo Real</div>
        <div class="tracker-steps">
            <div class="tracker-progress-line" style="width: {{ $lineWidth }};"></div>

            <div class="step-item {{ $step >= 1 ? 'active' : '' }}">
                <div class="step-icon">👨‍🍳</div>
                <div class="step-label">1. En Preparación</div>
            </div>

            <div class="step-item {{ $step >= 2 ? 'active' : '' }}">
                <div class="step-icon">
                    @if($order->service_type === 'delivery') 🛵 @else 🛍️ @endif
                </div>
                <div class="step-label">
                    @if($order->service_type === 'delivery') 2. En Camino @else 2. Listo p/ Recoger @endif
                </div>
            </div>

            <div class="step-item {{ $step >= 3 ? 'active' : '' }}">
                <div class="step-icon">✅</div>
                <div class="step-label">3. Entregado</div>
            </div>
        </div>
    </div>

    {{-- Ficha OXXO si aplicó ese pago --}}
    @if($order->payment_method === 'oxxo' && $order->oxxo_reference)
    <div class="oxxo-voucher">
        <h4 style="color: #E65100; font-weight: 800; font-size: 1.2rem; margin-bottom: 4px;">🏪 Ficha de Pago OXXO Pay</h4>
        <p style="font-size: 0.88rem; color: #5D4037;">Muestra esta referencia en cualquier tienda OXXO para realizar tu pago en efectivo.</p>
        
        <div class="oxxo-code">{{ chunk_split($order->oxxo_reference, 4, ' ') }}</div>

        <div style="font-size: 0.9rem; font-weight: 700; color: #3E2723; margin-bottom: 6px;">
            Monto a Pagar: ${{ number_format($order->total, 2) }} MXN
        </div>
        <div style="font-size: 0.8rem; color: #C62828;">
            Vence el: {{ $order->oxxo_expires_at?->format('d/m/Y H:i') ?? 'En 48 hrs' }}
        </div>
    </div>
    @endif

    {{-- Código QR --}}
    <div style="text-align: center; background: #FFF8E1; padding: 1.5rem; border-radius: 16px; margin-bottom: 2rem; border: 2px dashed #8D6E63;">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode(route('reception.index', ['token' => $order->qr_token])) }}" alt="Código QR del Pedido" style="width: 180px; height: 180px;">
        <div style="margin-top: 10px; font-weight: 700; font-size: 1.1rem; color: #3E2723; font-family: monospace;">
            FOLIO: {{ $order->folio }}
        </div>
    </div>

    {{-- Resumen del Ticket --}}
    <div style="background: #F5F2EB; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <span><strong>Cliente:</strong> {{ $order->customer_name }}</span>
            <span><strong>Fecha:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div style="margin-bottom: 8px;">
            <strong>Modalidad:</strong> 
            @if($order->service_type === 'delivery')
                🛵 Entrega a Domicilio (Delivery)
            @else
                🥡 Recoger en Sucursal
            @endif
        </div>
        
        @if($order->service_type === 'delivery')
            <div style="background: white; padding: 10px 14px; border-radius: 8px; margin: 10px 0; border: 1px solid #E0D7D0;">
                <strong style="color: #3E2723; display: block; margin-bottom: 2px;">📍 Dirección de Entrega:</strong>
                <p style="font-size: 0.9rem; color: #5D4037; margin: 0;">{{ $order->full_address }}</p>
                @if($order->delivery_references)
                    <small style="color: #8D6E63;">Ref: {{ $order->delivery_references }}</small>
                @endif
                <div style="font-size: 0.85rem; color: #3E2723; margin-top: 4px;"><strong>Teléfono:</strong> {{ $order->customer_phone }}</div>
            </div>
        @elseif($order->table)
            <div style="margin-bottom: 8px; color: #3E2723;">
                <strong>Mesa / Zona Reservada:</strong> {{ $order->table->zone?->name }} — Mesa {{ $order->table->number }}
            </div>
        @endif

        <div style="margin-bottom: 8px;">
            <strong>Método de Pago:</strong> 
            @if($order->payment_method === 'online')
                <span style="color: #2E7D32; font-weight: 700;">💳 Tarjeta Simulada (Pagado)</span>
            @elseif($order->payment_method === 'oxxo')
                <span style="color: #E65100; font-weight: 700;">🏪 OXXO Pay (Pendiente)</span>
            @else
                <span style="color: #5D4037; font-weight: 700;">💵 Efectivo (Al Entregar / Recoger)</span>
            @endif
        </div>

        <hr style="border: 0; border-top: 1px solid #D7CCC8; margin: 12px 0;">

        <h4 style="color: #3E2723; margin-bottom: 8px;">Artículos:</h4>
        @foreach($order->items as $item)
            <div style="display: flex; justify-content: space-between; font-size: 0.95rem; margin-bottom: 4px;">
                <span>{{ $item->quantity }}x {{ $item->item_name }}</span>
                <span>${{ number_format($item->subtotal, 2) }}</span>
            </div>
        @endforeach

        @if($order->discount > 0)
            <div style="display: flex; justify-content: space-between; font-size: 0.95rem; color: #2E7D32; margin-top: 4px;">
                <span>Descuento Cupón:</span>
                <span>-${{ number_format($order->discount, 2) }}</span>
            </div>
        @endif

        <hr style="border: 0; border-top: 1px solid #D7CCC8; margin: 12px 0;">

        <div style="display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: 800; color: #3E2723;">
            <span>Total:</span>
            <span>${{ number_format($order->total, 2) }} MXN</span>
        </div>
    </div>

    <div class="btn-no-print" style="display: flex; justify-content: space-between; gap: 1rem;">
        <a href="{{ route('profile.index') }}" style="background: #8D6E63; color: white; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: 600;">
            📋 Ver mis Pedidos
        </a>
        <a href="{{ route('store.comprar') }}" style="background: #3E2723; color: white; text-decoration: none; padding: 10px 24px; border-radius: 8px; font-weight: 600;">
            ☕ Volver al Menú
        </a>
    </div>
</div>
@endsection
