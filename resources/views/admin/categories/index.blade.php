@extends('layouts.admin')

@section('title', 'Categorías — Administración')
@section('header_title', 'Administración de Categorías')

@section('content')

@if(session('success'))
    <div style="background-color: #E8F5E9; color: #2E7D32; padding: 1rem; border-radius: 8px; font-weight: 600; margin-bottom: 1.5rem;">
        {{ session('success') }}
    </div>
@endif

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h3 style="color: #3E2723;">Categorías del Menú</h3>
    <button onclick="document.getElementById('newCatModal').style.display='flex'" style="background: #3E2723; color: white; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 600; cursor: pointer;">
        + Nueva Categoría
    </button>
</div>

<div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden;">
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="background: #F5F2EB; border-bottom: 2px solid #D7CCC8;">
                <th style="padding: 12px;">ID</th>
                <th style="padding: 12px;">Nombre</th>
                <th style="padding: 12px;">Descripción</th>
                <th style="padding: 12px;">Productos Asociados</th>
                <th style="padding: 12px;">Orden</th>
                <th style="padding: 12px;">Estado</th>
                <th style="padding: 12px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $cat)
                <tr style="border-bottom: 1px solid #EFEBE9;">
                    <td style="padding: 12px; font-weight: 700; font-family: monospace;">#{{ $cat->id }}</td>
                    <td style="padding: 12px; font-weight: 700; color: #3E2723;">{{ $cat->name }}</td>
                    <td style="padding: 12px; color: #666;">{{ $cat->description ?: 'N/A' }}</td>
                    <td style="padding: 12px; font-weight: 600;">{{ $cat->products_count }} productos</td>
                    <td style="padding: 12px;">{{ $cat->order }}</td>
                    <td style="padding: 12px;">
                        @if($cat->active)
                            <span style="background: #E8F5E9; color: #2E7D32; padding: 4px 10px; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">Activa</span>
                        @else
                            <span style="background: #FFEBEE; color: #C62828; padding: 4px 10px; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">Inactiva</span>
                        @endif
                    </td>
                    <td style="padding: 12px;">
                        <form action="{{ route('admin.categories.toggle', $cat->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" style="background: none; border: 1px solid #8D6E63; color: #3E2723; padding: 4px 10px; border-radius: 4px; cursor: pointer; font-size: 0.85rem;">
                                {{ $cat->active ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Nueva Categoría -->
<div id="newCatModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 200;">
    <div style="background: white; width: 90%; max-width: 440px; padding: 2rem; border-radius: 12px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="color: #3E2723;">Crear Nueva Categoría</h3>
            <button onclick="document.getElementById('newCatModal').style.display='none'" style="background:none; border:none; font-size: 1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px;">Nombre *</label>
                <input type="text" name="name" required style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px;">Descripción</label>
                <input type="text" name="description" style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px;">Orden de despliegue</label>
                <input type="number" name="order" value="0" style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
            </div>

            <button type="submit" style="width: 100%; background: #3E2723; color: white; border: none; padding: 12px; border-radius: 6px; font-weight: 700; cursor: pointer;">
                Guardar Categoría
            </button>
        </form>
    </div>
</div>
@endsection
