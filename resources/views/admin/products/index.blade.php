@extends('layouts.admin')

@section('title', 'Productos — Administración')
@section('header_title', 'Administración de Productos')

@section('content')

@if(session('success'))
    <div style="background-color: #E8F5E9; color: #2E7D32; padding: 1rem; border-radius: 8px; font-weight: 600; margin-bottom: 1.5rem;">
        {{ session('success') }}
    </div>
@endif

@if(session('info'))
    <div style="background-color: #E3F2FD; color: #1565C0; padding: 1rem; border-radius: 8px; font-weight: 600; margin-bottom: 1.5rem;">
        {{ session('info') }}
    </div>
@endif

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h3 style="color: #3E2723;">Catálogo de Productos en MySQL</h3>
    <button onclick="document.getElementById('newProductModal').style.display='flex'" style="background: #3E2723; color: white; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 600; cursor: pointer;">
        + Nuevo Producto
    </button>
</div>

<div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden;">
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="background: #F5F2EB; border-bottom: 2px solid #D7CCC8;">
                <th style="padding: 12px;">Código</th>
                <th style="padding: 12px;">Imagen</th>
                <th style="padding: 12px;">Nombre</th>
                <th style="padding: 12px;">Categoría</th>
                <th style="padding: 12px;">Precio</th>
                <th style="padding: 12px;">Stock MySQL</th>
                <th style="padding: 12px;">Estado</th>
                <th style="padding: 12px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr style="border-bottom: 1px solid #EFEBE9;">
                    <td style="padding: 12px; font-weight: 700; font-family: monospace;">{{ $product->code }}</td>
                    <td style="padding: 12px;">
                        <img src="{{ asset($product->image ?: 'images/cafe-risa-logo-principal.png') }}" alt="" style="width: 44px; height: 44px; object-fit: cover; border-radius: 6px;" onerror="this.src='{{ asset('images/cafe-risa-logo-principal.png') }}'">
                    </td>
                    <td style="padding: 12px; font-weight: 600; color: #3E2723;">{{ $product->name }}</td>
                    <td style="padding: 12px;">{{ $product->category?->name ?? 'Sin Categoría' }}</td>
                    <td style="padding: 12px; font-weight: 700;">${{ number_format($product->price, 2) }}</td>
                    <td style="padding: 12px;">
                        <span style="font-weight: 700; color: {{ $product->stock < 5 ? '#C62828' : '#2E7D32' }};">
                            {{ $product->stock }} unidades
                        </span>
                    </td>
                    <td style="padding: 12px;">
                        @if($product->active)
                            <span style="background: #E8F5E9; color: #2E7D32; padding: 4px 10px; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">Activo</span>
                        @else
                            <span style="background: #FFEBEE; color: #C62828; padding: 4px 10px; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">Inactivo</span>
                        @endif
                    </td>
                    <td style="padding: 12px;">
                        <form action="{{ route('admin.products.toggle', $product->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" style="background: none; border: 1px solid #8D6E63; color: #3E2723; padding: 4px 10px; border-radius: 4px; cursor: pointer; font-size: 0.85rem;">
                                {{ $product->active ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Nuevo Producto -->
<div id="newProductModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 200;">
    <div style="background: white; width: 90%; max-width: 500px; padding: 2rem; border-radius: 12px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="color: #3E2723;">Crear Nuevo Producto en MySQL</h3>
            <button onclick="document.getElementById('newProductModal').style.display='none'" style="background:none; border:none; font-size: 1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form action="{{ route('admin.products.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px;">Código * (Ej. P016)</label>
                <input type="text" name="code" required style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px;">Nombre *</label>
                <input type="text" name="name" required style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px;">Categoría</label>
                <select name="category_id" style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
                    <option value="">-- Seleccionar Categoría --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 4px;">Precio ($) *</label>
                    <input type="number" step="0.5" name="price" required style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 4px;">Stock Inicial *</label>
                    <input type="number" name="stock" value="20" required style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px;">Descripción</label>
                <textarea name="description" rows="3" style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;"></textarea>
            </div>

            <button type="submit" style="width: 100%; background: #3E2723; color: white; border: none; padding: 12px; border-radius: 6px; font-weight: 700; cursor: pointer;">
                Guardar Producto en MySQL
            </button>
        </form>
    </div>
</div>
@endsection
