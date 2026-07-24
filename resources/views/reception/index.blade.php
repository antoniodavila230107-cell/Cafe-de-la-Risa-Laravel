@extends('layouts.reception')

@section('title', 'Recepción & QR — Café de la Risa')

@section('content')

@if(session('success'))
    <div style="background-color: #E8F5E9; color: #2E7D32; padding: 1rem; border-radius: 8px; font-weight: 600; margin-bottom: 1.5rem;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background-color: #FFEBEE; color: #C62828; padding: 1rem; border-radius: 8px; font-weight: 600; margin-bottom: 1.5rem;">
        {{ session('error') }}
    </div>
@endif

@if(session('info'))
    <div style="background-color: #E3F2FD; color: #1565C0; padding: 1rem; border-radius: 8px; font-weight: 600; margin-bottom: 1.5rem;">
        {{ session('info') }}
    </div>
@endif

<!-- Buscador / Escáner QR -->
<div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 2rem;">
    <h3 style="color: #3E2723; margin-bottom: 1rem;">📱 Escanear Código QR o Buscar Folio</h3>

    <form action="{{ route('reception.validateQr') }}" method="POST" style="display: flex; gap: 1rem; max-width: 600px;">
        @csrf
        <input type="text" name="qr_token" required placeholder="Ingresa token QR o escanea aquí..." autofocus style="flex: 1; padding: 10px 14px; border: 1px solid #D7CCC8; border-radius: 8px; font-size: 1rem; outline: none;">
        <button type="submit" style="background: #3E2723; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">
            Validar Pedido
        </button>
    </form>
</div>

<!-- Pedido Seleccionado -->
@if($selectedOrder)
    <div style="background: #FFF8E1; padding: 1.8rem; border-radius: 12px; border: 2px solid #8D6E63; margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
            <div>
                <span style="font-family: monospace; font-weight: 700; font-size: 1.3rem; color: #3E2723;">FOLIO: {{ $selectedOrder->folio }}</span>
                <h3 style="color: #3E2723; margin-top: 4px;">Cliente: {{ $selectedOrder->customer_name }}</h3>
                @if($selectedOrder->customer_phone)<p style="color: #666; font-size: 0.9rem;">Tel: {{ $selectedOrder->customer_phone }}</p>@endif
            </div>

            <div style="text-align: right;">
                @if($selectedOrder->qr_used)
                    <span style="background: #FFEBEE; color: #C62828; padding: 6px 14px; border-radius: 20px; font-weight: 700; font-size: 0.9rem;">
                        ❌ QR YA UTILIZADO
                    </span>
                @else
                    <span style="background: #E8F5E9; color: #2E7D32; padding: 6px 14px; border-radius: 20px; font-weight: 700; font-size: 0.9rem;">
                        ✅ QR VÁLIDO (De Un Solo Uso)
                    </span>
                @endif
            </div>
        </div>

        <hr style="border: 0; border-top: 1px solid #D7CCC8; margin: 1rem 0;">

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
            <div>
                <h4 style="color: #3E2723; margin-bottom: 8px;">Estado del Pago:</h4>
                @if($selectedOrder->payment_status === 'paid')
                    <p style="color: #2E7D32; font-weight: 700; font-size: 1.1rem;">🟢 PAGADO (${{ number_format($selectedOrder->total, 2) }})</p>
                    <small style="color: #666;">Método: {{ strtoupper($selectedOrder->payment_method) }}</small>
                @else
                    <p style="color: #E65100; font-weight: 700; font-size: 1.1rem;">🟠 PAGO PENDIENTE DE COBRO (${{ number_format($selectedOrder->total, 2) }})</p>
                    <small style="color: #666;">Registrar cobro simulado en efectivo al entregar</small>
                @endif
            </div>

            <div>
                <h4 style="color: #3E2723; margin-bottom: 8px;">Mesa de Referencia:</h4>
                @if($selectedOrder->table)
                    <p style="color: #3E2723; font-weight: 600;">{{ $selectedOrder->table->zone?->name }} — Mesa {{ $selectedOrder->table->number }}</p>
                @else
                    <p style="color: #888;">Sin mesa asignada</p>
                @endif
            </div>
        </div>

        <h4 style="color: #3E2723; margin-bottom: 8px;">Ítems del Pedido:</h4>
        <ul style="list-style: none; margin-bottom: 1.5rem; background: white; padding: 1rem; border-radius: 8px;">
            @foreach($selectedOrder->items as $item)
                <li style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px dashed #EEE;">
                    <span>{{ $item->quantity }}x {{ $item->item_name }}</span>
                    <strong>${{ number_format($item->subtotal, 2) }}</strong>
                </li>
            @endforeach
        </ul>

        @if(!$selectedOrder->qr_used && $selectedOrder->order_status !== 'delivered')
            <form action="{{ route('reception.deliver', $selectedOrder->id) }}" method="POST">
                @csrf
                <button type="submit" style="width: 100%; background: #2E7D32; color: white; border: none; padding: 14px; border-radius: 8px; font-size: 1.1rem; font-weight: 700; cursor: pointer;">
                    @if($selectedOrder->payment_status === 'pending')
                        Confirmar Cobro en Efectivo (${{ number_format($selectedOrder->total, 2) }}) y Entregar Pedido
                    @else
                        Confirmar Entrega de Pedido (Inutilizar QR)
                    @endif
                </button>
            </form>
        @else
            <div style="text-align: center; color: #888; font-weight: 600;">Este pedido ya ha sido finalizado y entregado.</div>
        @endif
    </div>
@endif

<!-- Cola de Pedidos Pendientes -->
<div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
    <h3 style="color: #3E2723; margin-bottom: 1rem;">📋 Cola de Pedidos Pendientes por Entregar</h3>

    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.95rem;">
        <thead>
            <tr style="background: #F5F2EB; border-bottom: 2px solid #D7CCC8;">
                <th style="padding: 10px;">Folio</th>
                <th style="padding: 10px;">Cliente</th>
                <th style="padding: 10px;">Pago</th>
                <th style="padding: 10px;">Total</th>
                <th style="padding: 10px;">Acción</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pendingOrders as $ord)
                <tr style="border-bottom: 1px solid #EFEBE9;">
                    <td style="padding: 10px; font-weight: 700; font-family: monospace;">{{ $ord->folio }}</td>
                    <td style="padding: 10px;">{{ $ord->customer_name }}</td>
                    <td style="padding: 10px;">
                        @if($ord->payment_status === 'paid')
                            <span style="color: #2E7D32; font-weight: 600;">🟢 Pagado</span>
                        @else
                            <span style="color: #E65100; font-weight: 600;">🟠 Por Cobrar</span>
                        @endif
                    </td>
                    <td style="padding: 10px; font-weight: 700;">${{ number_format($ord->total, 2) }}</td>
                    <td style="padding: 10px;">
                        <a href="{{ route('reception.index', ['token' => $ord->qr_token]) }}" style="background: #8D6E63; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 600;">
                            Abrir QR
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="padding: 15px; text-align: center; color: #888;">No hay pedidos pendientes en la cola.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
