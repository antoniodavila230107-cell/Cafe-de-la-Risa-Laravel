<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor de Cocina — Café de la Risa</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Outfit', sans-serif; }
        body { background: #1E100A; color: #FFFFFF; min-height: 100vh; padding: 1.5rem; }
        .kitchen-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; border-bottom: 2px solid #3E2723; padding-bottom: 1rem; }
        .orders-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem; }
        .order-card { background: #2C1810; border-radius: 12px; padding: 1.5rem; border-top: 6px solid #8D6E63; display: flex; flex-direction: column; }
        .order-card.preparing { border-top-color: #F57C00; }
        .order-card.ready { border-top-color: #388E3C; }
    </style>
</head>
<body>

    <header class="kitchen-header">
        <div>
            <h1 style="font-size: 1.8rem; color: #D7CCC8;">👨‍🍳 Monitor de Cocina y Barra</h1>
            <p style="color: #A1887F; font-size: 0.9rem;">Pedidos en preparación en tiempo real (MySQL)</p>
        </div>
        <div style="font-size: 1.1rem; font-weight: 700; color: #D7CCC8;">
            Total Pendientes: {{ $activeOrders->count() }}
        </div>
    </header>

    <div class="orders-grid">
        @forelse($activeOrders as $ord)
            <div class="order-card {{ $ord->order_status }}">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                    <div>
                        <span style="font-family: monospace; font-weight: 700; font-size: 1.3rem; color: #D7CCC8;">{{ $ord->folio }}</span>
                        <h3 style="margin-top: 4px; font-size: 1.1rem;">{{ $ord->customer_name }}</h3>
                    </div>
                    <span style="background: #3E2723; color: #D7CCC8; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 700;">
                        {{ strtoupper($ord->order_status) }}
                    </span>
                </div>

                @if($ord->table)
                    <div style="background: #3E2723; padding: 6px 10px; border-radius: 6px; font-size: 0.85rem; margin-bottom: 1rem; color: #D7CCC8;">
                        📍 {{ $ord->table->zone?->name }} — Mesa {{ $ord->table->number }}
                    </div>
                @endif

                <ul style="list-style: none; margin-bottom: 1.5rem; flex: 1;">
                    @foreach($ord->items as $item)
                        <li style="padding: 6px 0; border-bottom: 1px dashed rgba(255,255,255,0.1); font-size: 1.05rem;">
                            <strong style="color: #FFB74D;">{{ $item->quantity }}x</strong> {{ $item->item_name }}
                        </li>
                    @endforeach
                </ul>

                <div style="display: flex; gap: 10px; margin-top: auto;">
                    @if($ord->order_status === 'received')
                        <form action="{{ route('kitchen.updateStatus', $ord->id) }}" method="POST" style="flex:1;">
                            @csrf
                            <input type="hidden" name="order_status" value="preparing">
                            <button type="submit" style="width: 100%; background: #F57C00; color: white; border: none; padding: 10px; border-radius: 6px; font-weight: 700; cursor: pointer;">
                                En Preparación ⏳
                            </button>
                        </form>
                    @elseif($ord->order_status === 'preparing')
                        <form action="{{ route('kitchen.updateStatus', $ord->id) }}" method="POST" style="flex:1;">
                            @csrf
                            <input type="hidden" name="order_status" value="ready">
                            <button type="submit" style="width: 100%; background: #388E3C; color: white; border: none; padding: 10px; border-radius: 6px; font-weight: 700; cursor: pointer;">
                                ¡Marcar Listo! ✅
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; color: #A1887F; padding: 3rem;">
                <h2>No hay pedidos pendientes en cocina.</h2>
            </div>
        @endforelse
    </div>

</body>
</html>
