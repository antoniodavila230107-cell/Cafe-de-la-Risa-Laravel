@extends('layouts.admin')

@section('title', 'Reportes de Ingresos — Administración')
@section('header_title', 'Reporte de Ingresos & Utilidad Demostrativa')

@section('content')

<!-- Filtros de Período -->
<div style="background: white; padding: 1rem 1.5rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h4 style="color: #3E2723;">Filtrar Período de Reporte:</h4>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.reports.index', ['period' => 'today']) }}" style="padding: 6px 14px; border-radius: 20px; text-decoration: none; font-weight: 600; font-size: 0.9rem; {{ $period === 'today' ? 'background:#3E2723; color:white;' : 'background:#F5F2EB; color:#3E2723;' }}">Hoy</a>
        <a href="{{ route('admin.reports.index', ['period' => '7days']) }}" style="padding: 6px 14px; border-radius: 20px; text-decoration: none; font-weight: 600; font-size: 0.9rem; {{ $period === '7days' ? 'background:#3E2723; color:white;' : 'background:#F5F2EB; color:#3E2723;' }}">Últimos 7 días</a>
        <a href="{{ route('admin.reports.index', ['period' => '15days']) }}" style="padding: 6px 14px; border-radius: 20px; text-decoration: none; font-weight: 600; font-size: 0.9rem; {{ $period === '15days' ? 'background:#3E2723; color:white;' : 'background:#F5F2EB; color:#3E2723;' }}">Últimos 15 días</a>
        <a href="{{ route('admin.reports.index', ['period' => 'month']) }}" style="padding: 6px 14px; border-radius: 20px; text-decoration: none; font-weight: 600; font-size: 0.9rem; {{ $period === 'month' ? 'background:#3E2723; color:white;' : 'background:#F5F2EB; color:#3E2723;' }}">Este Mes</a>
    </div>
</div>

<!-- Tarjetas Métricas -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); border-left: 4px solid #2E7D32;">
        <span style="font-size: 0.8rem; color: #6D4C41; text-transform: uppercase; font-weight: 700;">Ventas Brutas</span>
        <h2 style="font-size: 1.8rem; color: #2E7D32; font-weight: 700; margin-top: 6px;">${{ number_format($totalSales, 2) }}</h2>
        <small style="color: #888;">{{ $ordersCount }} ventas pagadas</small>
    </div>

    <div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); border-left: 4px solid #C62828;">
        <span style="font-size: 0.8rem; color: #6D4C41; text-transform: uppercase; font-weight: 700;">Gastos Registrados</span>
        <h2 style="font-size: 1.8rem; color: #C62828; font-weight: 700; margin-top: 6px;">${{ number_format($totalExpenses, 2) }}</h2>
        <small style="color: #888;">Egresos de operación</small>
    </div>

    <div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); border-left: 4px solid #3E2723;">
        <span style="font-size: 0.8rem; color: #6D4C41; text-transform: uppercase; font-weight: 700;">Utilidad Estimada</span>
        <h2 style="font-size: 1.8rem; color: #3E2723; font-weight: 700; margin-top: 6px;">${{ number_format($netIncome, 2) }}</h2>
        <small style="color: #888;">Ventas menos gastos</small>
    </div>

    <div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); border-left: 4px solid #8D6E63;">
        <span style="font-size: 0.8rem; color: #6D4C41; text-transform: uppercase; font-weight: 700;">Ticket Promedio</span>
        <h2 style="font-size: 1.8rem; color: #8D6E63; font-weight: 700; margin-top: 6px;">${{ number_format($averageTicket, 2) }}</h2>
        <small style="color: #888;">Por pedido en línea</small>
    </div>
</div>

<!-- Tabla Detallada de Ventas -->
<div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
    <h4 style="color: #3E2723; margin-bottom: 1rem;">Detalle de Pedidos en el Período</h4>

    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
        <thead>
            <tr style="background: #F5F2EB; border-bottom: 2px solid #D7CCC8;">
                <th style="padding: 10px;">Folio</th>
                <th style="padding: 10px;">Fecha</th>
                <th style="padding: 10px;">Cliente</th>
                <th style="padding: 10px;">Método Pago</th>
                <th style="padding: 10px;">Subtotal</th>
                <th style="padding: 10px;">Descuento</th>
                <th style="padding: 10px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $ord)
                <tr style="border-bottom: 1px solid #EFEBE9;">
                    <td style="padding: 10px; font-weight: 700; font-family: monospace;">{{ $ord->folio }}</td>
                    <td style="padding: 10px;">{{ $ord->created_at->format('d/m/Y H:i') }}</td>
                    <td style="padding: 10px;">{{ $ord->customer_name }}</td>
                    <td style="padding: 10px;">{{ strtoupper($ord->payment_method) }}</td>
                    <td style="padding: 10px;">${{ number_format($ord->subtotal, 2) }}</td>
                    <td style="padding: 10px; color: #C62828;">-${{ number_format($ord->discount, 2) }}</td>
                    <td style="padding: 10px; font-weight: 700; color: #2E7D32;">${{ number_format($ord->total, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" style="padding: 15px; text-align: center; color: #888;">No hay ventas registradas en este período.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
