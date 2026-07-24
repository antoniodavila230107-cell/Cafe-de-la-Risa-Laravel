@extends('layouts.admin')

@section('title', 'Gestión de Pedidos — Administración')
@section('header_title', 'Gestión & Rastreo de Pedidos (Delivery y Sucursal)')

@section('content')

@if(session('success'))
<div style="background: #E8F5E9; color: #2E7D32; padding: 1rem; border-radius: 8px; font-weight: 600; margin-bottom: 1.5rem; border: 1px solid #A5D6A7;">
    ✅ {{ session('success') }}
</div>
@endif

<!-- Barra de Filtros -->
<div style="background: white; padding: 1.2rem; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); margin-bottom: 1.5rem;">
    <form action="{{ route('admin.orders.index') }}" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Buscar por Folio, Cliente o Teléfono..." style="flex: 1; min-width: 240px; padding: 9px 14px; border: 1px solid #D7CCC8; border-radius: 8px; font-size: 0.95rem; outline: none;">
        
        <select name="type" style="padding: 9px 14px; border: 1px solid #D7CCC8; border-radius: 8px; font-size: 0.95rem; outline: none;">
            <option value="">-- Todas las Modalidades --</option>
            <option value="delivery" {{ request('type') === 'delivery' ? 'selected' : '' }}>🛵 Delivery</option>
            <option value="para_llevar" {{ request('type') === 'para_llevar' ? 'selected' : '' }}>🥡 Recoger en Sucursal</option>
        </select>

        <select name="status" style="padding: 9px 14px; border: 1px solid #D7CCC8; border-radius: 8px; font-size: 0.95rem; outline: none;">
            <option value="">-- Todos los Estados --</option>
            <option value="preparing" {{ request('status') === 'preparing' ? 'selected' : '' }}>👨‍🍳 En Preparación</option>
            <option value="on_the_way" {{ request('status') === 'on_the_way' ? 'selected' : '' }}>🛵 En Camino / Listo</option>
            <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>✅ Entregado</option>
        </select>

        <button type="submit" style="background: #3E2723; color: white; border: none; padding: 9px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">
            Filtrar
        </button>
        
        @if(request()->hasAny(['search', 'type', 'status']))
            <a href="{{ route('admin.orders.index') }}" style="color: #8D6E63; text-decoration: none; font-size: 0.9rem; font-weight: 600;">Limpiar Filtros</a>
        @endif
    </form>
</div>

<!-- Tabla de Pedidos -->
<div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.05);">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
            <thead>
                <tr style="background: #F5F2EB; border-bottom: 2px solid #D7CCC8;">
                    <th style="padding: 10px;">Folio & Fecha</th>
                    <th style="padding: 10px;">Cliente & Contacto</th>
                    <th style="padding: 10px;">Modalidad & Dirección</th>
                    <th style="padding: 10px;">Método de Pago</th>
                    <th style="padding: 10px;">Estado Actual</th>
                    <th style="padding: 10px; width: 180px;">Cambiar Estado (Rastreador)</th>
                    <th style="padding: 10px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr style="border-bottom: 1px solid #EFEBE9;">
                        <td style="padding: 10px;">
                            <strong style="font-family: monospace; font-size: 1rem; color: #3E2723;">{{ $order->folio }}</strong><br>
                            <small style="color: #888;">{{ $order->created_at->format('d/m/Y H:i') }}</small>
                        </td>
                        <td style="padding: 10px;">
                            <strong>{{ $order->customer_name }}</strong><br>
                            <small style="color: #666;">Tel: {{ $order->customer_phone ?: 'N/A' }}</small>
                        </td>
                        <td style="padding: 10px;">
                            @if($order->service_type === 'delivery')
                                <span style="color: #1565C0; font-weight: 700; display: block;">🛵 Entrega a Domicilio</span>
                                <small style="color: #5D4037;">{{ $order->full_address }}</small>
                                @if($order->delivery_references)
                                    <br><small style="color: #8D6E63;">Ref: {{ $order->delivery_references }}</small>
                                @endif
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
                                <span style="color: #E65100; font-weight: 600;">🏪 OXXO Pay</span><br>
                                <small style="color: #8D6E63;">Ref: {{ $order->oxxo_reference }}</small>
                            @else
                                <span style="color: #5D4037; font-weight: 600;">💵 Efectivo (Al entregar)</span>
                            @endif
                        </td>
                        <td style="padding: 10px;">
                            @if($order->order_status === 'preparing' || $order->order_status === 'received')
                                <span style="background: #FFF3E0; color: #E65100; padding: 4px 10px; border-radius: 12px; font-weight: 700; font-size: 0.78rem;">👨‍🍳 En Preparación</span>
                            @elseif($order->order_status === 'on_the_way' || $order->order_status === 'ready')
                                <span style="background: #E3F2FD; color: #1565C0; padding: 4px 10px; border-radius: 12px; font-weight: 700; font-size: 0.78rem;">🛵 En Camino / Listo</span>
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
                                    <button type="submit" title="1. En Preparación" style="background: {{ in_array($order->order_status, ['received','preparing']) ? '#E65100' : '#FFF3E0' }}; color: {{ in_array($order->order_status, ['received','preparing']) ? 'white' : '#E65100' }}; border: 1px solid #FFE082; border-radius: 6px; padding: 5px 9px; font-size: 0.82rem; font-weight: 700; cursor: pointer;">👨‍🍳 1</button>
                                </form>
                                <form action="{{ route('reception.updateStatus', $order->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="order_status" value="on_the_way">
                                    <button type="submit" title="2. En Camino / Listo" style="background: {{ in_array($order->order_status, ['on_the_way','ready']) ? '#1565C0' : '#E3F2FD' }}; color: {{ in_array($order->order_status, ['on_the_way','ready']) ? 'white' : '#1565C0' }}; border: 1px solid #90CAF9; border-radius: 6px; padding: 5px 9px; font-size: 0.82rem; font-weight: 700; cursor: pointer;">🛵 2</button>
                                </form>
                                <form action="{{ route('reception.updateStatus', $order->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="order_status" value="delivered">
                                    <button type="submit" title="3. Entregado" style="background: {{ $order->order_status === 'delivered' ? '#2E7D32' : '#E8F5E9' }}; color: {{ $order->order_status === 'delivered' ? 'white' : '#2E7D32' }}; border: 1px solid #A5D6A7; border-radius: 6px; padding: 5px 9px; font-size: 0.82rem; font-weight: 700; cursor: pointer;">✅ 3</button>
                                </form>
                            </div>
                        </td>
                        <td style="padding: 10px; font-weight: 700; color: #3E2723;">${{ number_format($order->total, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding: 20px; text-align: center; color: #888;">No se encontraron pedidos con los criterios seleccionados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $orders->links() }}
    </div>
</div>
@endsection
