@extends('layouts.admin')

@section('title', 'Gastos Operativos — Administración')
@section('header_title', 'Registro de Gastos y Nómina de Empleados')

@section('content')

@if(session('success'))
    <div style="background-color: #E8F5E9; color: #2E7D32; padding: 1rem; border-radius: 8px; font-weight: 600; margin-bottom: 1.5rem; border: 1px solid #A5D6A7;">
        ✅ {{ session('success') }}
    </div>
@endif

<!-- Tarjetas KPI de Egresos -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.2rem; margin-bottom: 2rem;">
    <div style="background: white; padding: 1.2rem; border-radius: 12px; border-left: 4px solid #C62828; box-shadow: 0 2px 6px rgba(0,0,0,0.05);">
        <span style="font-size: 0.8rem; color: #C62828; text-transform: uppercase; font-weight: 700;">Gastos Directos / Compras</span>
        <h2 style="font-size: 1.6rem; color: #C62828; font-weight: 800; margin-top: 4px;">${{ number_format($directExpenses, 2) }}</h2>
        <small style="color: #666;">Insumos, servicios, mantenimiento</small>
    </div>

    <div style="background: white; padding: 1.2rem; border-radius: 12px; border-left: 4px solid #6A1B9A; box-shadow: 0 2px 6px rgba(0,0,0,0.05);">
        <span style="font-size: 0.8rem; color: #6A1B9A; text-transform: uppercase; font-weight: 700;">💰 Nómina de Empleados</span>
        <h2 style="font-size: 1.6rem; color: #6A1B9A; font-weight: 800; margin-top: 4px;">${{ number_format($payrollExpenses, 2) }}</h2>
        <small style="color: #666;">Sueldos acumulados del personal</small>
    </div>

    <div style="background: white; padding: 1.2rem; border-radius: 12px; border-left: 4px solid #3E2723; box-shadow: 0 2px 6px rgba(0,0,0,0.05);">
        <span style="font-size: 0.8rem; color: #3E2723; text-transform: uppercase; font-weight: 700;">Total Egresos Operativos</span>
        <h2 style="font-size: 1.6rem; color: #3E2723; font-weight: 800; margin-top: 4px;">${{ number_format($totalExpenses, 2) }}</h2>
        <small style="color: #666;">Gastos directos + Sueldos de empleados</small>
    </div>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.2rem;">
    <h3 style="color: #3E2723; font-size: 1.2rem; font-weight: 700;">🧾 Gastos Directos Registrados</h3>
    <button onclick="document.getElementById('newExpenseModal').style.display='flex'" style="background: #3E2723; color: white; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 600; cursor: pointer;">
        + Registrar Nuevo Gasto
    </button>
</div>

<div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; margin-bottom: 2rem;">
    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
        <thead>
            <tr style="background: #F5F2EB; border-bottom: 2px solid #D7CCC8;">
                <th style="padding: 12px;">ID</th>
                <th style="padding: 12px;">Fecha</th>
                <th style="padding: 12px;">Categoría</th>
                <th style="padding: 12px;">Descripción / Concepto</th>
                <th style="padding: 12px;">Monto ($)</th>
                <th style="padding: 12px;">Registrado Por</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $exp)
                <tr style="border-bottom: 1px solid #EFEBE9;">
                    <td style="padding: 12px; font-weight: 700; font-family: monospace;">#{{ $exp->id }}</td>
                    <td style="padding: 12px;">{{ $exp->expense_date->format('d/m/Y') }}</td>
                    <td style="padding: 12px; font-weight: 600; color: #8D6E63;">{{ $exp->category }}</td>
                    <td style="padding: 12px;">{{ $exp->description }}</td>
                    <td style="padding: 12px; font-weight: 700; color: #C62828;">${{ number_format($exp->amount, 2) }}</td>
                    <td style="padding: 12px; color: #666;">{{ $exp->user?->name ?? 'Sistema' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="padding: 15px; text-align: center; color: #888;">No hay gastos directos registrados aún.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Desglose de Nómina de Empleados --}}
<div style="background: white; padding: 1.5rem; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h3 style="color: #3E2723; font-size: 1.15rem; font-weight: 700;">👥 Nómina de Empleados Reflejada en Egresos</h3>
        <a href="{{ route('admin.employees.index') }}" style="color: #6A1B9A; text-decoration: none; font-weight: 700; font-size: 0.88rem;">
            ⚙️ Gestionar Empleados &rarr;
        </a>
    </div>

    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
        <thead>
            <tr style="background: #F5F2EB; border-bottom: 2px solid #D7CCC8;">
                <th style="padding: 10px;">Empleado</th>
                <th style="padding: 10px;">Puesto</th>
                <th style="padding: 10px;">Sueldo Base</th>
                <th style="padding: 10px;">Impacto Mensual en Gastos</th>
            </tr>
        </thead>
        <tbody>
            @forelse($activeEmployees as $emp)
                <tr style="border-bottom: 1px solid #EFEBE9;">
                    <td style="padding: 10px; font-weight: 700; color: #3E2723;">{{ $emp->name }}</td>
                    <td style="padding: 10px;">{{ $emp->formatted_role }}</td>
                    <td style="padding: 10px; font-weight: 600;">${{ number_format($emp->salary, 2) }} ({{ $emp->salary_period }})</td>
                    <td style="padding: 10px; font-weight: 800; color: #6A1B9A;">${{ number_format($emp->monthly_salary, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="padding: 15px; text-align: center; color: #888;">No hay empleados activos registrados en nómina.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal Nuevo Gasto -->
<div id="newExpenseModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 200;">
    <div style="background: white; width: 90%; max-width: 440px; padding: 2rem; border-radius: 12px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="color: #3E2723;">Registrar Gasto Operativo</h3>
            <button onclick="document.getElementById('newExpenseModal').style.display='none'" style="background:none; border:none; font-size: 1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form action="{{ route('admin.expenses.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px;">Categoría *</label>
                <select name="category" required style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
                    <option value="Materia Prima">Materia Prima / Insumos</option>
                    <option value="Servicios">Servicios (Luz, Agua, Internet)</option>
                    <option value="Mantenimiento">Mantenimiento y Reparaciones</option>
                    <option value="Empaque">Empaques y Desechables</option>
                    <option value="Otros">Otros Gastos</option>
                </select>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px;">Descripción / Concepto *</label>
                <input type="text" name="description" required placeholder="Ej. Compra de grano de café, vasos..." style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 4px;">Monto ($) *</label>
                    <input type="number" step="0.5" name="amount" required placeholder="250.00" style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 4px;">Fecha *</label>
                    <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
                </div>
            </div>

            <button type="submit" style="width: 100%; background: #3E2723; color: white; border: none; padding: 12px; border-radius: 6px; font-weight: 700; cursor: pointer;">
                Guardar Gasto en MySQL
            </button>
        </form>
    </div>
</div>
@endsection
