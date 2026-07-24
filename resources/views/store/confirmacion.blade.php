@extends('layouts.store')

@section('title', 'Confirmación de Pedido — Café de la Risa')

@section('content')
<div style="max-width: 680px; margin: 2rem auto; background: white; padding: 2.5rem; border-radius: 16px; box-shadow: 0 10px 25px rgba(62,39,35,0.12); text-align: center;">
    <div style="margin-bottom: 1.5rem;">
        <span style="font-size: 3rem;">🎉</span>
        <h2 style="color: #3E2723; font-size: 1.8rem; font-weight: 700; margin-top: 8px;">¡Pedido Confirmado!</h2>
        <p style="color: #6D4C41; font-size: 1rem;">Presenta este código QR en la recepción al momento de recoger tu pedido.</p>
    </div>

    <!-- Contenedor del Código QR -->
    <div style="background: #FFF8E1; padding: 1.5rem; border-radius: 16px; display: inline-block; margin-bottom: 2rem; border: 2px dashed #8D6E63;">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode(route('reception.index', ['token' => $order->qr_token])) }}" alt="Código QR del Pedido" style="width: 180px; height: 180px;">
        <div style="margin-top: 10px; font-weight: 700; font-size: 1.1rem; color: #3E2723; font-family: monospace;">
            FOLIO: {{ $order->folio }}
        </div>
    </div>

    <!-- Detalles del Pedido -->
    <div style="text-align: left; background: #F5F2EB; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <span><strong>Cliente:</strong> {{ $order->customer_name }}</span>
            <span><strong>Fecha:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <span>
                <strong>Estado de Pago:</strong> 
                @if($order->payment_status === 'paid')
                    <span style="color: #2E7D32; font-weight: 700;">🟢 Pagado ({{ strtoupper($order->payment_method) }})</span>
                @else
                    <span style="color: #E65100; font-weight: 700;">🟠 Pendiente (Pago en Efectivo al Recoger)</span>
                @endif
            </span>
        </div>
        @if($order->table)
            <div style="margin-bottom: 8px; color: #3E2723;">
                <strong>Mesa / Zona Reservada:</strong> {{ $order->table->zone?->name }} — Mesa {{ $order->table->number }}
            </div>
        @endif

        <hr style="border: 0; border-top: 1px solid #D7CCC8; margin: 12px 0;">

        <h4 style="color: #3E2723; margin-bottom: 8px;">Resumen del Pedido:</h4>
        @foreach($order->items as $item)
            <div style="display: flex; justify-content: space-between; font-size: 0.95rem; margin-bottom: 4px;">
                <span>{{ $item->quantity }}x {{ $item->item_name }}</span>
                <span>${{ number_format($item->subtotal, 2) }}</span>
            </div>
        @endforeach

        <hr style="border: 0; border-top: 1px solid #D7CCC8; margin: 12px 0;">

        <div style="display: flex; justify-content: space-between; font-size: 1.1rem; font-weight: 700; color: #3E2723;">
            <span>Total:</span>
            <span>${{ number_format($order->total, 2) }}</span>
        </div>
    </div>

    <a href="{{ route('store.comprar') }}" style="display: inline-block; background: #3E2723; color: white; text-decoration: none; padding: 10px 24px; border-radius: 8px; font-weight: 600;">
        Volver al Menú
    </a>
</div>
@endsection
