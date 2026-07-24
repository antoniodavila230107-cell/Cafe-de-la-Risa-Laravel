@extends('layouts.admin')

@section('title', 'Dashboard — Café de la Risa')
@section('header_title', 'Dashboard Principal POS')

@section('content')
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); border-left: 4px solid #3E2723;">
        <span style="font-size: 0.85rem; color: #6D4C41; text-transform: uppercase; font-weight: 700;">Ventas del Día</span>
        <h2 style="font-size: 1.8rem; color: #3E2723; font-weight: 700; margin-top: 6px;">${{ number_format($todaySalesSum, 2) }}</h2>
        <small style="color: #888;">{{ $todayOrdersCount }} pedidos hoy</small>
    </div>

    <div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); border-left: 4px solid #8D6E63;">
        <span style="font-size: 0.85rem; color: #6D4C41; text-transform: uppercase; font-weight: 700;">Pedidos Pendientes</span>
        <h2 style="font-size: 1.8rem; color: #8D6E63; font-weight: 700; margin-top: 6px;">{{ $pendingOrdersCount }}</h2>
        <small style="color: #888;">En preparación / entrega</small>
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
    <h3 style="color: #3E2723; margin-bottom: 1rem; font-size: 1.2rem;">Últimos Pedidos Registrados</h3>

    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.95rem;">
        <thead>
            <tr style="background: #F5F2EB; border-bottom: 2px solid #D7CCC8;">
                <th style="padding: 10px;">Folio</th>
                <th style="padding: 10px;">Cliente</th>
                <th style="padding: 10px;">Pago</th>
                <th style="padding: 10px;">Estado</th>
                <th style="padding: 10px;">Total</th>
                <th style="padding: 10px;">Acción</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentOrders as $order)
                <tr style="border-bottom: 1px solid #EFEBE9;">
                    <td style="padding: 10px; font-weight: 700; font-family: monospace;">{{ $order->folio }}</td>
                    <td style="padding: 10px;">{{ $order->customer_name }}</td>
                    <td style="padding: 10px;">
                        @if($order->payment_status === 'paid')
                            <span style="color: #2E7D32; font-weight: 600;">Pagado ({{ strtoupper($order->payment_method) }})</span>
                        @else
                            <span style="color: #E65100; font-weight: 600;">Pendiente</span>
                        @endif
                    </td>
                    <td style="padding: 10px;">
                        @if($order->order_status === 'delivered')
                            <span style="background: #E8F5E9; color: #2E7D32; padding: 2px 8px; border-radius: 10px; font-size: 0.85rem;">Entregado</span>
                        @else
                            <span style="background: #FFF3E0; color: #E65100; padding: 2px 8px; border-radius: 10px; font-size: 0.85rem;">{{ ucfirst($order->order_status) }}</span>
                        @endif
                    </td>
                    <td style="padding: 10px; font-weight: 700;">${{ number_format($order->total, 2) }}</td>
                    <td style="padding: 10px;">
                        <a href="{{ route('reception.index', ['token' => $order->qr_token]) }}" style="color: #8D6E63; text-decoration: none; font-weight: 600;">Ver QR &rarr;</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="padding: 15px; text-align: center; color: #888;">No hay pedidos registrados en MySQL aún.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
