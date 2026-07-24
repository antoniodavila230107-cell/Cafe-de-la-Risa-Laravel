@extends('layouts.admin')

@section('title', 'Caja & Turnos — Administración')
@section('header_title', 'Control de Caja y Apertura/Cierre de Turnos')

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

<div style="margin-bottom: 2rem;">
    @if($activeShift)
        <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 6px solid #2E7D32;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                <div>
                    <span style="background: #E8F5E9; color: #2E7D32; padding: 4px 12px; border-radius: 16px; font-weight: 700; font-size: 0.85rem;">TURNO DE CAJA ABIERTO</span>
                    <h3 style="color: #3E2723; margin-top: 8px;">Cajero: {{ $activeShift->user?->name }}</h3>
                    <p style="color: #666; font-size: 0.9rem;">Abierto el: {{ $activeShift->opened_at->format('d/m/Y H:i') }}</p>
                </div>

                <button onclick="document.getElementById('closeShiftModal').style.display='flex'" style="background: #C62828; color: white; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 700; cursor: pointer;">
                    🔒 Cerrar Turno de Caja
                </button>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; background: #F5F2EB; padding: 1.2rem; border-radius: 8px; margin-bottom: 1.5rem;">
                <div>
                    <span style="font-size: 0.8rem; color: #6D4C41; font-weight: 700;">FONDO INICIAL</span>
                    <h4 style="font-size: 1.3rem; color: #3E2723;">${{ number_format($activeShift->opening_amount, 2) }}</h4>
                </div>
                <div>
                    <span style="font-size: 0.8rem; color: #6D4C41; font-weight: 700;">MOVIMIENTOS REGISTRADOS</span>
                    <h4 style="font-size: 1.3rem; color: #3E2723;">{{ $activeShift->movements->count() }}</h4>
                </div>
            </div>

            <button onclick="document.getElementById('movementModal').style.display='flex'" style="background: #3E2723; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer;">
                + Registrar Entradas / Salidas de Caja
            </button>
        </div>
    @else
        <div style="background: white; padding: 2rem; border-radius: 12px; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-left: 6px solid #C62828;">
            <span style="font-size: 2.5rem;">🔒</span>
            <h3 style="color: #3E2723; margin: 8px 0;">No Hay Turno de Caja Abierto</h3>
            <p style="color: #666; margin-bottom: 1.5rem;">Abre un nuevo turno indicando el monto inicial de caja.</p>
            <button onclick="document.getElementById('openShiftModal').style.display='flex'" style="background: #2E7D32; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; font-size: 1rem; cursor: pointer;">
                🔓 Abrir Nuevo Turno de Caja
            </button>
        </div>
    @endif
</div>

<!-- Historial de Turnos Anteriores -->
<div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
    <h4 style="color: #3E2723; margin-bottom: 1rem;">📜 Historial de Turnos de Caja Cerrados en MySQL</h4>

    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
        <thead>
            <tr style="background: #F5F2EB; border-bottom: 2px solid #D7CCC8;">
                <th style="padding: 10px;">Apertura</th>
                <th style="padding: 10px;">Cierre</th>
                <th style="padding: 10px;">Monto Inicial</th>
                <th style="padding: 10px;">Monto Final Arqueo</th>
                <th style="padding: 10px;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pastShifts as $s)
                <tr style="border-bottom: 1px solid #EFEBE9;">
                    <td style="padding: 10px;">{{ $s->opened_at->format('d/m/Y H:i') }}</td>
                    <td style="padding: 10px;">{{ $s->closed_at?->format('d/m/Y H:i') ?? 'N/A' }}</td>
                    <td style="padding: 10px;">${{ number_format($s->opening_amount, 2) }}</td>
                    <td style="padding: 10px; font-weight: 700;">${{ number_format($s->closing_amount ?? 0, 2) }}</td>
                    <td style="padding: 10px;"><span style="background: #F5F5F5; color: #616161; padding: 2px 8px; border-radius: 10px; font-size: 0.8rem;">Cerrado</span></td>
                </tr>
            @empty
                <tr><td colspan="5" style="padding: 12px; text-align: center; color: #888;">No hay turnos pasados registrados.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal Abrir Turno -->
<div id="openShiftModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 200;">
    <div style="background: white; width: 90%; max-width: 400px; padding: 2rem; border-radius: 12px;">
        <h3 style="color: #3E2723; margin-bottom: 1rem;">Abrir Turno de Caja</h3>
        <form action="{{ route('admin.cash.openShift') }}" method="POST">
            @csrf
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px;">Monto Inicial ($) *</label>
                <input type="number" step="0.5" name="opening_amount" value="500.00" required style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
            </div>
            <button type="submit" style="width: 100%; background: #2E7D32; color: white; border: none; padding: 12px; border-radius: 6px; font-weight: 700; cursor: pointer;">
                Confirmar Apertura
            </button>
        </form>
    </div>
</div>

<!-- Modal Cerrar Turno -->
@if($activeShift)
<div id="closeShiftModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 200;">
    <div style="background: white; width: 90%; max-width: 400px; padding: 2rem; border-radius: 12px;">
        <h3 style="color: #3E2723; margin-bottom: 1rem;">Cerrar Turno de Caja</h3>
        <form action="{{ route('admin.cash.closeShift', $activeShift->id) }}" method="POST">
            @csrf
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px;">Monto Final en Efectivo ($) *</label>
                <input type="number" step="0.5" name="closing_amount" required placeholder="Arqueo físico de caja" style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
            </div>
            <button type="submit" style="width: 100%; background: #C62828; color: white; border: none; padding: 12px; border-radius: 6px; font-weight: 700; cursor: pointer;">
                Confirmar Cierre de Caja
            </button>
        </form>
    </div>
</div>

<!-- Modal Movimiento de Caja -->
<div id="movementModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 200;">
    <div style="background: white; width: 90%; max-width: 420px; padding: 2rem; border-radius: 12px;">
        <h3 style="color: #3E2723; margin-bottom: 1rem;">Movimiento de Caja</h3>
        <form action="{{ route('admin.cash.movement') }}" method="POST">
            @csrf
            <input type="hidden" name="cash_shift_id" value="{{ $activeShift->id }}">
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px;">Tipo *</label>
                <select name="type" required style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
                    <option value="entrada">Entrada (+)</option>
                    <option value="salida">Salida / Retiro (-)</option>
                </select>
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px;">Monto ($) *</label>
                <input type="number" step="0.5" name="amount" required style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px;">Descripción / Concepto *</label>
                <input type="text" name="description" required placeholder="Ej. Cambio para caja, pago menor..." style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
            </div>
            <button type="submit" style="width: 100%; background: #3E2723; color: white; border: none; padding: 12px; border-radius: 6px; font-weight: 700; cursor: pointer;">
                Guardar Movimiento
            </button>
        </form>
    </div>
</div>
@endif

@endsection
