@extends('layouts.admin')

@section('title', 'Gestión de Empleados — Administración')
@section('header_title', 'Gestión de Empleados, Sueldos y Asignaciones')

@section('content')

@if(session('success'))
<div style="background: #E8F5E9; color: #2E7D32; padding: 1rem; border-radius: 8px; font-weight: 600; margin-bottom: 1.5rem; border: 1px solid #A5D6A7;">
    ✅ {{ session('success') }}
</div>
@endif

<!-- Tarjetas de Resumen KPI -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.2rem; margin-bottom: 2rem;">
    <div style="background: white; padding: 1.2rem; border-radius: 12px; border-left: 4px solid #3E2723; box-shadow: 0 2px 6px rgba(0,0,0,0.05);">
        <span style="font-size: 0.8rem; color: #8D6E63; text-transform: uppercase; font-weight: 700;">Empleados Activos</span>
        <h2 style="font-size: 1.6rem; color: #3E2723; font-weight: 800; margin-top: 4px;">{{ $stats['total_count'] }}</h2>
        <small style="color: #666;">Personal en turno</small>
    </div>

    <div style="background: white; padding: 1.2rem; border-radius: 12px; border-left: 4px solid #1565C0; box-shadow: 0 2px 6px rgba(0,0,0,0.05);">
        <span style="font-size: 0.8rem; color: #1565C0; text-transform: uppercase; font-weight: 700;">🍷 Meseros</span>
        <h2 style="font-size: 1.6rem; color: #1565C0; font-weight: 800; margin-top: 4px;">{{ $stats['waiters_count'] }}</h2>
        <small style="color: #666;">Con mesas asignadas</small>
    </div>

    <div style="background: white; padding: 1.2rem; border-radius: 12px; border-left: 4px solid #2E7D32; box-shadow: 0 2px 6px rgba(0,0,0,0.05);">
        <span style="font-size: 0.8rem; color: #2E7D32; text-transform: uppercase; font-weight: 700;">📋 Recepcionistas</span>
        <h2 style="font-size: 1.6rem; color: #2E7D32; font-weight: 800; margin-top: 4px;">{{ $stats['receptionists'] }}</h2>
        <small style="color: #666;">En cajas / estaciones</small>
    </div>

    <div style="background: white; padding: 1.2rem; border-radius: 12px; border-left: 4px solid #E65100; box-shadow: 0 2px 6px rgba(0,0,0,0.05);">
        <span style="font-size: 0.8rem; color: #E65100; text-transform: uppercase; font-weight: 700;">👨‍🍳 Cocineros</span>
        <h2 style="font-size: 1.6rem; color: #E65100; font-weight: 800; margin-top: 4px;">{{ $stats['chefs_count'] }}</h2>
        <small style="color: #666;">En monitor de cocina</small>
    </div>

    <div style="background: white; padding: 1.2rem; border-radius: 12px; border-left: 4px solid #6A1B9A; box-shadow: 0 2px 6px rgba(0,0,0,0.05);">
        <span style="font-size: 0.8rem; color: #6A1B9A; text-transform: uppercase; font-weight: 700;">💰 Nómina Mensual Total</span>
        <h2 style="font-size: 1.6rem; color: #6A1B9A; font-weight: 800; margin-top: 4px;">${{ number_format($stats['total_payroll'], 2) }}</h2>
        <small style="color: #666;">Reflejado en Gastos Operativos</small>
    </div>
</div>

{{-- Botón y Formulario de Nuevo Empleado --}}
<div style="background: white; padding: 1.5rem; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.2rem;">
        <h3 style="color: #3E2723; font-size: 1.2rem; font-weight: 700;">➕ Registrar Nuevo Empleado</h3>
    </div>

    <form action="{{ route('admin.employees.store') }}" method="POST">
        @csrf
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem; color: #3E2723;">Nombre Completo *</label>
                <input type="text" name="name" required placeholder="Ej. Carlos Mendoza" style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px; outline: none;">
            </div>

            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem; color: #3E2723;">Puesto / Rol *</label>
                <select name="job_role" id="jobRoleSelect" required onchange="toggleRoleAssignments()" style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px; outline: none;">
                    <option value="mesero">🍷 Mesero (Atención a Mesas)</option>
                    <option value="recepcionista">📋 Recepcionista (Caja & Máquina)</option>
                    <option value="cocinero">👨‍🍳 Cocinero (Cocina & Preparación)</option>
                </select>
            </div>

            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem; color: #3E2723;">Sueldo ($) *</label>
                <input type="number" step="0.01" name="salary" required placeholder="8000.00" style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px; outline: none;">
            </div>

            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem; color: #3E2723;">Periodo de Pago *</label>
                <select name="salary_period" required style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px; outline: none;">
                    <option value="mensual">Mensual</option>
                    <option value="quincenal">Quincenal</option>
                    <option value="diario">Diario</option>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem; color: #3E2723;">Correo (Opcional)</label>
                <input type="email" name="email" placeholder="carlos@cafedelarisa.com" style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px; outline: none;">
            </div>

            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.88rem; color: #3E2723;">Teléfono (Opcional)</label>
                <input type="text" name="phone" placeholder="55 9876 5432" style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px; outline: none;">
            </div>
        </div>

        {{-- Asignación para Meseros (Mesas) --}}
        <div id="waiterAssignmentField" style="background: #FAF7F2; padding: 1rem; border-radius: 8px; border: 1px dashed #D7CCC8; margin-bottom: 1.2rem;">
            <label style="display: block; font-weight: 700; color: #3E2723; margin-bottom: 8px; font-size: 0.9rem;">
                🍷 Asignar Mesas para Atender (Selecciona una o varias):
            </label>
            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                @foreach($allTables as $tbl)
                <label style="background: white; border: 1px solid #D7CCC8; padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                    <input type="checkbox" name="table_ids[]" value="{{ $tbl->id }}">
                    🪑 Mesa {{ $tbl->number }} ({{ $tbl->zone?->name }})
                </label>
                @endforeach
            </div>
        </div>

        {{-- Asignación para Recepcionistas (Máquina de Cobro) --}}
        <div id="receptionistAssignmentField" style="display: none; background: #E8F5E9; padding: 1rem; border-radius: 8px; border: 1px dashed #A5D6A7; margin-bottom: 1.2rem;">
            <label style="display: block; font-weight: 700; color: #2E7D32; margin-bottom: 6px; font-size: 0.9rem;">
                📋 Asignar Estación / Máquina de Cobro:
            </label>
            <input type="text" name="register_station" value="Caja Principal 01" placeholder="Ej. Caja 01 - Barra Principal" style="width: 100%; max-width: 400px; padding: 8px 12px; border: 1px solid #A5D6A7; border-radius: 6px; font-size: 0.9rem;">
        </div>

        <button type="submit" style="background: #3E2723; color: white; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 700; font-size: 0.95rem; cursor: pointer;">
            Guardar y Registrar Empleado
        </button>
    </form>
</div>

<!-- Tabla de Empleados Registrados -->
<div style="background: white; padding: 1.5rem; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
    <h3 style="color: #3E2723; font-size: 1.2rem; font-weight: 700; margin-bottom: 1rem;">📜 Lista de Empleados & Asignaciones</h3>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
            <thead>
                <tr style="background: #F5F2EB; border-bottom: 2px solid #D7CCC8;">
                    <th style="padding: 10px;">Nombre & Contacto</th>
                    <th style="padding: 10px;">Puesto / Rol</th>
                    <th style="padding: 10px;">Sueldo & Periodo</th>
                    <th style="padding: 10px;">Sueldo Mensual (Gasto)</th>
                    <th style="padding: 10px;">Asignación (Mesas / Caja)</th>
                    <th style="padding: 10px;">Estado</th>
                    <th style="padding: 10px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $emp)
                <tr style="border-bottom: 1px solid #EFEBE9;">
                    <td style="padding: 10px;">
                        <strong style="color: #3E2723; font-size: 0.95rem;">{{ $emp->name }}</strong><br>
                        <small style="color: #666;">{{ $emp->email ?: ($emp->phone ?: 'Sin datos de contacto') }}</small>
                    </td>
                    <td style="padding: 10px;">
                        <span style="font-weight: 700;">{{ $emp->formatted_role }}</span>
                    </td>
                    <td style="padding: 10px; font-weight: 600;">
                        ${{ number_format($emp->salary, 2) }} / <small style="color: #666;">{{ ucfirst($emp->salary_period) }}</small>
                    </td>
                    <td style="padding: 10px; font-weight: 800; color: #6A1B9A;">
                        ${{ number_format($emp->monthly_salary, 2) }}
                    </td>
                    <td style="padding: 10px;">
                        @if($emp->job_role === 'mesero')
                            @if($emp->tables->count() > 0)
                                <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                                    @foreach($emp->tables as $t)
                                        <span style="background: #FFF8E1; border: 1px solid #FFE082; color: #E65100; padding: 2px 6px; border-radius: 4px; font-weight: 700; font-size: 0.78rem;">
                                            🪑 Mesa {{ $t->number }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span style="color: #888; font-style: italic;">Sin mesas asignadas</span>
                            @endif
                        @elseif($emp->job_role === 'recepcionista')
                            <span style="background: #E8F5E9; border: 1px solid #A5D6A7; color: #2E7D32; padding: 3px 8px; border-radius: 6px; font-weight: 700; font-size: 0.8rem;">
                                💻 {{ $emp->register_station ?: 'Caja Principal' }}
                            </span>
                        @else
                            <span style="color: #888;">👨‍🍳 Estación Cocina</span>
                        @endif
                    </td>
                    <td style="padding: 10px;">
                        @if($emp->active)
                            <span style="background: #E8F5E9; color: #2E7D32; padding: 3px 10px; border-radius: 12px; font-weight: 700; font-size: 0.8rem;">Activo</span>
                        @else
                            <span style="background: #FFEBEE; color: #C62828; padding: 3px 10px; border-radius: 12px; font-weight: 700; font-size: 0.8rem;">Inactivo</span>
                        @endif
                    </td>
                    <td style="padding: 10px;">
                        <div style="display: flex; gap: 6px;">
                            <button type="button" onclick="openEditModal({{ $emp->id }})" style="background: #FFF3E0; border: 1px solid #FFE082; color: #E65100; padding: 4px 8px; border-radius: 6px; font-size: 0.8rem; cursor: pointer; font-weight: 600;">
                                ✏️ Editar
                            </button>

                            <form action="{{ route('admin.employees.toggle', $emp->id) }}" method="POST">
                                @csrf
                                <button type="submit" style="background: #F5F2EB; border: 1px solid #D7CCC8; padding: 4px 8px; border-radius: 6px; font-size: 0.8rem; cursor: pointer; font-weight: 600;">
                                    {{ $emp->active ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>

                            <form action="{{ route('admin.employees.destroy', $emp->id) }}" method="POST" onsubmit="return confirm('¿Eliminar a {{ $emp->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: #FFEBEE; color: #C62828; border: 1px solid #FFCDD2; padding: 4px 8px; border-radius: 6px; font-size: 0.8rem; cursor: pointer; font-weight: 600;">
                                    🗑️
                                </button>
                            </form>
                        </div>

                        {{-- Modal de Edición del Empleado --}}
                        <div id="editModal-{{ $emp->id }}" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 250;">
                            <div style="background: white; width: 90%; max-width: 580px; padding: 1.8rem; border-radius: 14px; max-height: 90vh; overflow-y: auto;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                    <h3 style="color: #3E2723;">✏️ Editar Empleado: {{ $emp->name }}</h3>
                                    <button type="button" onclick="closeEditModal({{ $emp->id }})" style="background:none; border:none; font-size: 1.5rem; cursor:pointer;">&times;</button>
                                </div>

                                <form action="{{ route('admin.employees.update', $emp->id) }}" method="POST">
                                    @csrf
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                                        <div>
                                            <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 4px;">Nombre *</label>
                                            <input type="text" name="name" value="{{ $emp->name }}" required style="width: 100%; padding: 8px; border: 1px solid #D7CCC8; border-radius: 6px;">
                                        </div>
                                        <div>
                                            <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 4px;">Puesto / Rol *</label>
                                            <select name="job_role" id="editJobRole-{{ $emp->id }}" onchange="toggleEditRoleAssignments({{ $emp->id }})" style="width: 100%; padding: 8px; border: 1px solid #D7CCC8; border-radius: 6px;">
                                                <option value="mesero" {{ $emp->job_role === 'mesero' ? 'selected' : '' }}>🍷 Mesero</option>
                                                <option value="recepcionista" {{ $emp->job_role === 'recepcionista' ? 'selected' : '' }}>📋 Recepcionista</option>
                                                <option value="cocinero" {{ $emp->job_role === 'cocinero' ? 'selected' : '' }}>👨‍🍳 Cocinero</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                                        <div>
                                            <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 4px;">Sueldo ($) *</label>
                                            <input type="number" step="0.01" name="salary" value="{{ $emp->salary }}" required style="width: 100%; padding: 8px; border: 1px solid #D7CCC8; border-radius: 6px;">
                                        </div>
                                        <div>
                                            <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 4px;">Periodo de Pago *</label>
                                            <select name="salary_period" style="width: 100%; padding: 8px; border: 1px solid #D7CCC8; border-radius: 6px;">
                                                <option value="mensual" {{ $emp->salary_period === 'mensual' ? 'selected' : '' }}>Mensual</option>
                                                <option value="quincenal" {{ $emp->salary_period === 'quincenal' ? 'selected' : '' }}>Quincenal</option>
                                                <option value="diario" {{ $emp->salary_period === 'diario' ? 'selected' : '' }}>Diario</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                                        <div>
                                            <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 4px;">Correo</label>
                                            <input type="email" name="email" value="{{ $emp->email }}" style="width: 100%; padding: 8px; border: 1px solid #D7CCC8; border-radius: 6px;">
                                        </div>
                                        <div>
                                            <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 4px;">Teléfono</label>
                                            <input type="text" name="phone" value="{{ $emp->phone }}" style="width: 100%; padding: 8px; border: 1px solid #D7CCC8; border-radius: 6px;">
                                        </div>
                                    </div>

                                    {{-- Asignación de Mesas (para Meseros) --}}
                                    <div id="editWaiterField-{{ $emp->id }}" style="display: {{ $emp->job_role === 'mesero' ? 'block' : 'none' }}; background: #FAF7F2; padding: 1rem; border-radius: 8px; border: 1px dashed #D7CCC8; margin-bottom: 1rem;">
                                        <label style="display: block; font-weight: 700; color: #3E2723; margin-bottom: 6px; font-size: 0.85rem;">🍷 Modificar Mesas Asignadas:</label>
                                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                            @php $assignedIds = $emp->tables->pluck('id')->toArray(); @endphp
                                            @foreach($allTables as $tbl)
                                            <label style="background: white; border: 1px solid #D7CCC8; padding: 4px 8px; border-radius: 6px; font-size: 0.82rem; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                                                <input type="checkbox" name="table_ids[]" value="{{ $tbl->id }}" {{ in_array($tbl->id, $assignedIds) ? 'checked' : '' }}>
                                                Mesa {{ $tbl->number }}
                                            </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Asignación de Caja/Máquina (para Recepcionistas) --}}
                                    <div id="editRecepField-{{ $emp->id }}" style="display: {{ $emp->job_role === 'recepcionista' ? 'block' : 'none' }}; background: #E8F5E9; padding: 1rem; border-radius: 8px; border: 1px dashed #A5D6A7; margin-bottom: 1rem;">
                                        <label style="display: block; font-weight: 700; color: #2E7D32; margin-bottom: 4px; font-size: 0.85rem;">📋 Modificar Máquina / Caja Asignada:</label>
                                        <input type="text" name="register_station" value="{{ $emp->register_station ?: 'Caja Principal 01' }}" style="width: 100%; padding: 8px; border: 1px solid #A5D6A7; border-radius: 6px; font-size: 0.9rem;">
                                    </div>

                                    <button type="submit" style="width: 100%; background: #3E2723; color: white; border: none; padding: 10px; border-radius: 6px; font-weight: 700; cursor: pointer;">
                                        Guardar Cambios
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding: 20px; text-align: center; color: #888;">No hay empleados registrados en el sistema.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleRoleAssignments() {
    const role = document.getElementById('jobRoleSelect').value;
    const waiterField = document.getElementById('waiterAssignmentField');
    const recepField = document.getElementById('receptionistAssignmentField');

    if (role === 'mesero') {
        waiterField.style.display = 'block';
        recepField.style.display = 'none';
    } else if (role === 'recepcionista') {
        waiterField.style.display = 'none';
        recepField.style.display = 'block';
    } else {
        waiterField.style.display = 'none';
        recepField.style.display = 'none';
    }
}

function openEditModal(id) {
    document.getElementById('editModal-' + id).style.display = 'flex';
}

function closeEditModal(id) {
    document.getElementById('editModal-' + id).style.display = 'none';
}

function toggleEditRoleAssignments(id) {
    const role = document.getElementById('editJobRole-' + id).value;
    const waiterField = document.getElementById('editWaiterField-' + id);
    const recepField = document.getElementById('editRecepField-' + id);

    if (role === 'mesero') {
        waiterField.style.display = 'block';
        recepField.style.display = 'none';
    } else if (role === 'recepcionista') {
        waiterField.style.display = 'none';
        recepField.style.display = 'block';
    } else {
        waiterField.style.display = 'none';
        recepField.style.display = 'none';
    }
}
</script>
@endsection
