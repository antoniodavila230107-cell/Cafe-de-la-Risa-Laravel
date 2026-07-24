@extends('layouts.admin')

@section('title', 'Dashboard — Café de la Risa')
@section('header_title', 'Dashboard Principal POS & Control de Pedidos')

@section('content')

@if(session('success'))
<div style="background: #E8F5E9; color: #2E7D32; padding: 1rem; border-radius: 8px; font-weight: 600; margin-bottom: 1.5rem; border: 1px solid #A5D6A7;">
    ✅ {{ session('success') }}
</div>
@endif

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); border-left: 4px solid #3E2723;">
        <span style="font-size: 0.85rem; color: #6D4C41; text-transform: uppercase; font-weight: 700;">Ventas del Día</span>
        <h2 style="font-size: 1.8rem; color: #3E2723; font-weight: 700; margin-top: 6px;">${{ number_format($todaySalesSum, 2) }}</h2>
        <small style="color: #888;">{{ $todayOrdersCount }} pedidos hoy</small>
    </div>

    <div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); border-left: 4px solid #8D6E63;">
        <span style="font-size: 0.85rem; color: #6D4C41; text-transform: uppercase; font-weight: 700;">Pedidos Pendientes</span>
        <h2 style="font-size: 1.8rem; color: #8D6E63; font-weight: 700; margin-top: 6px;">{{ $pendingOrdersCount }}</h2>
        <small style="color: #888;">En preparación / delivery</small>
    </div>

    <div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); border-left: 4px solid #2E7D32;">
        <span style="font-size: 0.85rem; color: #6D4C41; text-transform: uppercase; font-weight: 700;">Mesas Libres</span>
        <h2 style="font-size: 1.8rem; color: #2E7D32; font-weight: 700; margin-top: 6px;">{{ $availableTablesCount }} / {{ $totalTablesCount }}</h2>
        <small style="color: #888;">Disponibles en mapa</small>
    </div>

    <div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); border-left: 4px solid #D7CCC8;">
        <span style="font-size: 0.85rem; color: #6D4C41; text-transform: uppercase; font-weight: 700;">Productos Activos</span>
        <h2 style="font-size: 1.8rem; color: #3E2723; font-weight: 700; margin-top: 6px;">{{ $productsCount }}</h2>
        <small style="color: #888;">En catálogo MySQL</small>
    </div>
</div>

<div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.2rem;">
        <h3 style="color: #3E2723; font-size: 1.2rem; font-weight: 700;">📋 Control & Estado de Pedidos (Delivery & Sucursal)</h3>
        <a href="{{ route('reception.index') }}" style="background: #3E2723; color: white; padding: 8px 16px; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 0.85rem;">
            📱 Ver Escáner QR Recepción
        </a>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
            <thead>
                <tr style="background: #F5F2EB; border-bottom: 2px solid #D7CCC8;">
                    <th style="padding: 10px;">Folio</th>
                    <th style="padding: 10px;">Cliente</th>
                    <th style="padding: 10px;">Modalidad / Dirección</th>
                    <th style="padding: 10px;">Pago</th>
                    <th style="padding: 10px;">Estado Actual</th>
                    <th style="padding: 10px;">Cambiar Estado (Rastreador)</th>
                    <th style="padding: 10px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                    <tr style="border-bottom: 1px solid #EFEBE9;">
                        <td style="padding: 10px; font-weight: 700; font-family: monospace;">{{ $order->folio }}</td>
                        <td style="padding: 10px;">
                            <strong>{{ $order->customer_name }}</strong><br>
                            <small style="color: #666;">{{ $order->customer_phone ?: 'Sin teléfono' }}</small>
                        </td>
                        <td style="padding: 10px;">
                            @if($order->service_type === 'delivery')
                                <span style="color: #1565C0; font-weight: 700; display: block;">🛵 Delivery</span>
                                <small style="color: #5D4037;">{{ $order->full_address }}</small>
                            @elseif($order->table)
                                <span style="color: #3E2723; font-weight: 600;">🪑 Mesa {{ $order->table->number }}</span>
                            @else
                                <span style="color: #3E2723;">🥡 Recoger en Sucursal</span>
                            @endif
                        </td>
                        <td style="padding: 10px;">
                            @if($order->payment_status === 'paid')
                                <span style="color: #2E7D32; font-weight: 600;">🟢 Pagado ({{ strtoupper($order->payment_method) }})</span>
                            @elseif($order->payment_method === 'oxxo')
                                <span style="color: #E65100; font-weight: 600;">🏪 OXXO (Pendiente)</span>
                            @else
                                <span style="color: #5D4037; font-weight: 600;">💵 Efectivo (Al entregar)</span>
                            @endif
                        </td>
                        <td style="padding: 10px;">
                            @if($order->order_status === 'preparing' || $order->order_status === 'received')
                                <span style="background: #FFF3E0; color: #E65100; padding: 4px 10px; border-radius: 12px; font-weight: 700; font-size: 0.78rem;">👨‍🍳 En Preparación</span>
                            @elseif($order->order_status === 'on_the_way' || $order->order_status === 'ready')
                                <span style="background: #E3F2FD; color: #1565C0; padding: 4px 10px; border-radius: 12px; font-weight: 700; font-size: 0.78rem;">🛵 En Camino</span>
                            @elseif($order->order_status === 'delivered')
                                <span style="background: #E8F5E9; color: #2E7D32; padding: 4px 10px; border-radius: 12px; font-weight: 700; font-size: 0.78rem;">✅ Entregado</span>
                            @else
                                <span style="background: #FFEBEE; color: #C62828; padding: 4px 10px; border-radius: 12px; font-weight: 700; font-size: 0.78rem;">❌ {{ ucfirst($order->order_status) }}</span>
                            @endif
                        </td>
                        <td style="padding: 10px;">
                            <div style="display: flex; gap: 4px;">
                                <form action="{{ route('reception.updateStatus', $order->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="order_status" value="preparing">
                                    <button type="submit" title="Marcar 1. En Preparación" style="background: {{ in_array($order->order_status, ['received','preparing']) ? '#E65100' : '#FFF3E0' }}; color: {{ in_array($order->order_status, ['received','preparing']) ? 'white' : '#E65100' }}; border: 1px solid #FFE082; border-radius: 6px; padding: 4px 8px; font-size: 0.8rem; font-weight: 700; cursor: pointer;">👨‍🍳 1</button>
                                </form>
                                <form action="{{ route('reception.updateStatus', $order->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="order_status" value="on_the_way">
                                    <button type="submit" title="Marcar 2. En Camino / Listo" style="background: {{ in_array($order->order_status, ['on_the_way','ready']) ? '#1565C0' : '#E3F2FD' }}; color: {{ in_array($order->order_status, ['on_the_way','ready']) ? 'white' : '#1565C0' }}; border: 1px solid #90CAF9; border-radius: 6px; padding: 4px 8px; font-size: 0.8rem; font-weight: 700; cursor: pointer;">🛵 2</button>
                                </form>
                                <form action="{{ route('reception.updateStatus', $order->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="order_status" value="delivered">
                                    <button type="submit" title="Marcar 3. Entregado" style="background: {{ $order->order_status === 'delivered' ? '#2E7D32' : '#E8F5E9' }}; color: {{ $order->order_status === 'delivered' ? 'white' : '#2E7D32' }}; border: 1px solid #A5D6A7; border-radius: 6px; padding: 4px 8px; font-size: 0.8rem; font-weight: 700; cursor: pointer;">✅ 3</button>
                                </form>
                            </div>
                        </td>
                        <td style="padding: 10px; font-weight: 700; color: #3E2723;">${{ number_format($order->total, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding: 15px; text-align: center; color: #888;">No hay pedidos registrados en MySQL aún.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
