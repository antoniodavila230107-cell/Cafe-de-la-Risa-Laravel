@extends('layouts.admin')

@section('title', 'Recetas — Administración')
@section('header_title', 'Administración de Recetas por Producto')

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
    <h3 style="color: #3E2723;">Relación Producto - Insumos (Receta de Consumo)</h3>
    <button onclick="document.getElementById('newRecipeItemModal').style.display='flex'" style="background: #3E2723; color: white; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 600; cursor: pointer;">
        + Agregar Insumo a Receta
    </button>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.5rem;">
    @foreach($products as $product)
        <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border-top: 4px solid #3E2723;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                <div>
                    <span style="font-family: monospace; color: #8D6E63; font-weight: 700;">{{ $product->code }}</span>
                    <h4 style="color: #3E2723; font-size: 1.2rem;">{{ $product->name }}</h4>
                </div>
                <span style="font-weight: 700; color: #2E7D32;">${{ number_format($product->price, 2) }}</span>
            </div>

            @if($product->recipe && $product->recipe->items->count() > 0)
                <div style="background: #F5F2EB; padding: 0.8rem; border-radius: 8px; margin-bottom: 1rem; font-size: 0.85rem;">
                    <div style="display: flex; justify-content: space-between; color: #3E2723; font-weight: 700; margin-bottom: 6px;">
                        <span>Insumos Consumidos:</span>
                        <span>Costo Teórico: ${{ number_format($product->recipe->theoretical_cost, 2) }}</span>
                    </div>

                    <ul style="list-style: none;">
                        @foreach($product->recipe->items as $item)
                            <li style="display: flex; justify-content: space-between; align-items: center; padding: 4px 0; border-bottom: 1px dashed #DDD;">
                                <span>• {{ $item->ingredient?->name }}: <strong>{{ number_format($item->quantity, 3) }} {{ $item->ingredient?->unit }}</strong></span>
                                <form action="{{ route('admin.recipes.removeItem', $item->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background:none; border:none; color:#C62828; cursor:pointer; font-weight:700;">&times;</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <p style="color: #888; font-size: 0.85rem; margin-bottom: 1rem; font-style: italic;">Sin receta asignada aún.</p>
            @endif

            <button onclick="openRecipeModal({{ $product->id }}, '{{ addslashes($product->name) }}')" style="width: 100%; background: none; border: 1px solid #3E2723; color: #3E2723; padding: 6px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 0.85rem;">
                + Configurar Insumo
            </button>
        </div>
    @endforeach
</div>

<!-- Modal Vincular Insumo a Receta -->
<div id="newRecipeItemModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 200;">
    <div style="background: white; width: 90%; max-width: 480px; padding: 2rem; border-radius: 12px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="color: #3E2723;">Vincular Insumo a Producto</h3>
            <button onclick="document.getElementById('newRecipeItemModal').style.display='none'" style="background:none; border:none; font-size: 1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form action="{{ route('admin.recipes.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px;">Producto *</label>
                <select name="product_id" id="modalProductId" required style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
                    @foreach($products as $prod)
                        <option value="{{ $prod->id }}">{{ $prod->code }} — {{ $prod->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px;">Insumo Requerido *</label>
                <select name="ingredient_id" required style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
                    @foreach($ingredients as $ing)
                        <option value="{{ $ing->id }}">{{ $ing->name }} (Unidad: {{ $ing->unit }})</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px;">Cantidad Consumida por Venta *</label>
                <input type="number" step="0.001" name="quantity" required placeholder="Ej. 0.018 (para 18g de café)" style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
                <small style="color: #666; font-size: 0.8rem;">Ejemplo: 0.180 L de Leche, 1 Pieza de Pan, etc.</small>
            </div>

            <button type="submit" style="width: 100%; background: #3E2723; color: white; border: none; padding: 12px; border-radius: 6px; font-weight: 700; cursor: pointer;">
                Guardar Receta en MySQL
            </button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openRecipeModal(productId, productName) {
        document.getElementById('modalProductId').value = productId;
        document.getElementById('newRecipeItemModal').style.display = 'flex';
    }
</script>
@endsection
