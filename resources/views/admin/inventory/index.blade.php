@extends('layouts.admin')

@section('title', 'Inventario — Administración')
@section('header_title', 'Control de Inventario & Insumos')

@section('content')

@if(session('success'))
    <div style="background-color: #E8F5E9; color: #2E7D32; padding: 1rem; border-radius: 8px; font-weight: 600; margin-bottom: 1.5rem;">
        {{ session('success') }}
    </div>
@endif

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h3 style="color: #3E2723;">Stock Actual de Insumos (Materia Prima en MySQL)</h3>
    <div style="display: flex; gap: 1rem;">
        <button onclick="document.getElementById('adjustModal').style.display='flex'" style="background: #8D6E63; color: white; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 600; cursor: pointer;">
            🔄 Movimiento / Ajuste
        </button>
        <button onclick="document.getElementById('newIngModal').style.display='flex'" style="background: #3E2723; color: white; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 600; cursor: pointer;">
            + Nuevo Insumo
        </button>
    </div>
</div>

<div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; margin-bottom: 2rem;">
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="background: #F5F2EB; border-bottom: 2px solid #D7CCC8;">
                <th style="padding: 12px;">ID</th>
                <th style="padding: 12px;">Insumo / Materia Prima</th>
                <th style="padding: 12px;">Unidad</th>
                <th style="padding: 12px;">Costo Unitario</th>
                <th style="padding: 12px;">Stock Actual MySQL</th>
                <th style="padding: 12px;">Stock Mínimo</th>
                <th style="padding: 12px;">Alerta</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ingredients as $ing)
                @php
                    $qty = $ing->inventory?->current_quantity ?? 0;
                    $isLow = $qty <= $ing->min_stock;
                @endphp
                <tr style="border-bottom: 1px solid #EFEBE9;">
                    <td style="padding: 12px; font-weight: 700; font-family: monospace;">#{{ $ing->id }}</td>
                    <td style="padding: 12px; font-weight: 700; color: #3E2723;">{{ $ing->name }}</td>
                    <td style="padding: 12px;">{{ $ing->unit }}</td>
                    <td style="padding: 12px;">${{ number_format($ing->unit_cost, 4) }}</td>
                    <td style="padding: 12px; font-weight: 700; font-size: 1.05rem;">
                        {{ number_format($qty, 3) }} {{ $ing->unit }}
                    </td>
                    <td style="padding: 12px; color: #666;">{{ number_format($ing->min_stock, 3) }}</td>
                    <td style="padding: 12px;">
                        @if($isLow)
                            <span style="background: #FFEBEE; color: #C62828; padding: 4px 10px; border-radius: 12px; font-size: 0.85rem; font-weight: 700;">
                                ⚠️ Stock Bajo
                            </span>
                        @else
                            <span style="background: #E8F5E9; color: #2E7D32; padding: 4px 10px; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">
                                OK
                            </span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Auditoría de Movimientos Recientes -->
<div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
    <h4 style="color: #3E2723; margin-bottom: 1rem;">📜 Bitácora de Movimientos de Inventario</h4>

    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
        <thead>
            <tr style="background: #F5F2EB; border-bottom: 2px solid #D7CCC8;">
                <th style="padding: 8px;">Fecha</th>
                <th style="padding: 8px;">Insumo</th>
                <th style="padding: 8px;">Tipo</th>
                <th style="padding: 8px;">Cantidad</th>
                <th style="padding: 8px;">Notas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentMovements as $mov)
                <tr style="border-bottom: 1px solid #EFEBE9;">
                    <td style="padding: 8px;">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                    <td style="padding: 8px; font-weight: 600;">{{ $mov->ingredient?->name }}</td>
                    <td style="padding: 8px;">
                        <span style="font-weight: 700; color: {{ in_array($mov->type, ['entrada']) ? '#2E7D32' : '#C62828' }};">
                            {{ strtoupper($mov->type) }}
                        </span>
                    </td>
                    <td style="padding: 8px; font-weight: 700;">{{ number_format($mov->quantity, 3) }}</td>
                    <td style="padding: 8px; color: #666;">{{ $mov->notes ?: 'Sin notas' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="padding: 10px; text-align: center; color: #888;">No hay movimientos registrados.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal Nuevo Insumo -->
<div id="newIngModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 200;">
    <div style="background: white; width: 90%; max-width: 480px; padding: 2rem; border-radius: 12px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="color: #3E2723;">Crear Insumo / Materia Prima</h3>
            <button onclick="document.getElementById('newIngModal').style.display='none'" style="background:none; border:none; font-size: 1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form action="{{ route('admin.inventory.storeIngredient') }}" method="POST">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px;">Nombre del Insumo *</label>
                <input type="text" name="name" required placeholder="Ej. Leche entera, Café en grano..." style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 4px;">Unidad *</label>
                    <select name="unit" required style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
                        <option value="Kg">Kilogramo (Kg)</option>
                        <option value="L">Litro (L)</option>
                        <option value="Pieza">Pieza</option>
                        <option value="g">Gramos (g)</option>
                        <option value="ml">Mililitros (ml)</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 4px;">Costo Unitario ($) *</label>
                    <input type="number" step="0.0001" name="unit_cost" required placeholder="180.00" style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 4px;">Stock Inicial *</label>
                    <input type="number" step="0.001" name="initial_stock" value="10" required style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 4px;">Stock Mínimo *</label>
                    <input type="number" step="0.001" name="min_stock" value="2" required style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
                </div>
            </div>

            <button type="submit" style="width: 100%; background: #3E2723; color: white; border: none; padding: 12px; border-radius: 6px; font-weight: 700; cursor: pointer;">
                Guardar Insumo
            </button>
        </form>
    </div>
</div>

<!-- Modal Registrar Ajuste de Inventario -->
<div id="adjustModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 200;">
    <div style="background: white; width: 90%; max-width: 480px; padding: 2rem; border-radius: 12px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="color: #3E2723;">Ajuste / Movimiento de Inventario</h3>
            <button onclick="document.getElementById('adjustModal').style.display='none'" style="background:none; border:none; font-size: 1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form action="{{ route('admin.inventory.adjust') }}" method="POST">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px;">Insumo *</label>
                <select name="ingredient_id" required style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
                    @foreach($ingredients as $ing)
                        <option value="{{ $ing->id }}">{{ $ing->name }} (Stock: {{ number_format($ing->inventory?->current_quantity ?? 0, 3) }} {{ $ing->unit }})</option>
                    @endforeach
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 4px;">Tipo de Movimiento *</label>
                    <select name="type" required style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
                        <option value="entrada">Entrada (+)</option>
                        <option value="salida">Salida (-)</option>
                        <option value="merma">Merma / Pérdida (-)</option>
                        <option value="ajuste">Ajuste Directo (=)</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 4px;">Cantidad *</label>
                    <input type="number" step="0.001" name="quantity" required placeholder="0.5" style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px;">Notas o Justificación</label>
                <input type="text" name="notes" placeholder="Ej. Reabastecimiento de insumos, caducidad..." style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
            </div>

            <button type="submit" style="width: 100%; background: #8D6E63; color: white; border: none; padding: 12px; border-radius: 6px; font-weight: 700; cursor: pointer;">
                Registrar Movimiento en MySQL
            </button>
        </form>
    </div>
</div>
@endsection
