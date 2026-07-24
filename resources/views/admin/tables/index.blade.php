@extends('layouts.admin')

@section('title', 'Mapa de Mesas — Administración')
@section('header_title', 'Mapa Visual de Mesas y Zonas Físicas')

@section('content')

@if(session('success'))
    <div style="background-color: #E8F5E9; color: #2E7D32; padding: 1rem; border-radius: 8px; font-weight: 600; margin-bottom: 1.5rem;">
        {{ session('success') }}
    </div>
@endif

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h3 style="color: #3E2723;">Distribución del Local en MySQL</h3>
    <div style="display: flex; gap: 1rem;">
        <button onclick="document.getElementById('newZoneModal').style.display='flex'" style="background: #8D6E63; color: white; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 600; cursor: pointer;">
            + Nueva Zona
        </button>
        <button onclick="document.getElementById('newTableModal').style.display='flex'" style="background: #3E2723; color: white; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 600; cursor: pointer;">
            + Nueva Mesa
        </button>
    </div>
</div>

<div style="display: flex; flex-direction: column; gap: 2rem;">
    @foreach($zones as $zone)
        <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
            <h4 style="color: #3E2723; font-size: 1.3rem; margin-bottom: 4px; border-bottom: 2px solid #F5F2EB; padding-bottom: 8px;">
                📍 {{ $zone->name }}
            </h4>
            <p style="color: #666; font-size: 0.85rem; margin-bottom: 1.2rem;">{{ $zone->description ?: 'Zona física del restaurante' }}</p>

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem;">
                @forelse($zone->tables as $table)
                    @php
                        $badgeColor = match($table->status) {
                            'available' => '#2E7D32',
                            'reserved' => '#E65100',
                            'occupied' => '#C62828',
                            'maintenance' => '#757575',
                        };
                        $bgColor = match($table->status) {
                            'available' => '#E8F5E9',
                            'reserved' => '#FFF3E0',
                            'occupied' => '#FFEBEE',
                            'maintenance' => '#F5F5F5',
                        };
                        $statusLabel = match($table->status) {
                            'available' => 'Libre',
                            'reserved' => 'Reservada',
                            'occupied' => 'Ocupada',
                            'maintenance' => 'Mantenimiento',
                        };
                    @endphp
                    <div style="background: {{ $bgColor }}; border: 2px solid {{ $badgeColor }}; border-radius: 10px; padding: 1rem; text-align: center;">
                        <span style="font-size: 1.4rem; font-weight: 800; color: {{ $badgeColor }}; font-family: monospace;">
                            Mesa {{ $table->number }}
                        </span>
                        <div style="font-size: 0.8rem; color: #555; margin: 4px 0 8px;">Capacidad: {{ $table->capacity }} personas</div>

                        <span style="background: {{ $badgeColor }}; color: white; padding: 2px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700; display: inline-block; margin-bottom: 8px;">
                            {{ $statusLabel }}
                        </span>

                        @if($table->current_order_folio)
                            <div style="font-size: 0.75rem; font-family: monospace; font-weight: 700; color: #3E2723; margin-bottom: 8px;">
                                Folio: {{ $table->current_order_folio }}
                            </div>
                        @endif

                        <form action="{{ route('admin.tables.updateStatus', $table->id) }}" method="POST">
                            @csrf
                            <select name="status" onchange="this.form.submit()" style="width: 100%; padding: 4px; font-size: 0.8rem; border-radius: 4px; border: 1px solid #CCC;">
                                <option value="available" {{ $table->status === 'available' ? 'selected' : '' }}>Marcar Libre</option>
                                <option value="reserved" {{ $table->status === 'reserved' ? 'selected' : '' }}>Marcar Reservada</option>
                                <option value="occupied" {{ $table->status === 'occupied' ? 'selected' : '' }}>Marcar Ocupada</option>
                                <option value="maintenance" {{ $table->status === 'maintenance' ? 'selected' : '' }}>Mantenimiento</option>
                            </select>
                        </form>
                    </div>
                @empty
                    <p style="color: #888; font-style: italic; font-size: 0.9rem;">Sin mesas en esta zona.</p>
                @endforelse
            </div>
        </div>
    @endforeach
</div>

<!-- Modal Nueva Zona -->
<div id="newZoneModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 200;">
    <div style="background: white; width: 90%; max-width: 420px; padding: 2rem; border-radius: 12px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="color: #3E2723;">Crear Nueva Zona Física</h3>
            <button onclick="document.getElementById('newZoneModal').style.display='none'" style="background:none; border:none; font-size: 1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form action="{{ route('admin.tables.storeZone') }}" method="POST">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px;">Nombre de la Zona *</label>
                <input type="text" name="name" required placeholder="Ej. Jardín, Mezzanine..." style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px;">Descripción</label>
                <input type="text" name="description" placeholder="Descripción opcional" style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
            </div>

            <button type="submit" style="width: 100%; background: #8D6E63; color: white; border: none; padding: 12px; border-radius: 6px; font-weight: 700; cursor: pointer;">
                Guardar Zona
            </button>
        </form>
    </div>
</div>

<!-- Modal Nueva Mesa -->
<div id="newTableModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 200;">
    <div style="background: white; width: 90%; max-width: 420px; padding: 2rem; border-radius: 12px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="color: #3E2723;">Crear Nueva Mesa</h3>
            <button onclick="document.getElementById('newTableModal').style.display='none'" style="background:none; border:none; font-size: 1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form action="{{ route('admin.tables.storeTable') }}" method="POST">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px;">Zona *</label>
                <select name="zone_id" required style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
                    @foreach($zones as $z)
                        <option value="{{ $z->id }}">{{ $z->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 4px;">Número / Código *</label>
                    <input type="text" name="number" required placeholder="M4, T3..." style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 4px;">Capacidad *</label>
                    <input type="number" name="capacity" value="4" required style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
                </div>
            </div>

            <button type="submit" style="width: 100%; background: #3E2723; color: white; border: none; padding: 12px; border-radius: 6px; font-weight: 700; cursor: pointer;">
                Guardar Mesa en MySQL
            </button>
        </form>
    </div>
</div>
@endsection
