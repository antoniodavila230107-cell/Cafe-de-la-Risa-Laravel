@extends('layouts.store')

@section('title', 'Menú & Pedido en Línea — Café de la Risa')

@section('styles')
<style>
    .store-hero {
        text-align: center;
        margin-bottom: 2rem;
    }
    .store-hero h2 {
        font-size: 2.2rem;
        color: var(--primary-coffee);
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .store-hero p {
        color: var(--text-muted);
        font-size: 1.1rem;
    }

    .filters-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        background: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: var(--shadow-sm);
    }

    .category-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .pill {
        background: #F5F2EB;
        border: none;
        padding: 8px 16px;
        border-radius: 20px;
        color: var(--primary-coffee);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .pill.active, .pill:hover {
        background: var(--primary-coffee);
        color: white;
    }

    .search-input {
        padding: 10px 16px;
        border: 1px solid var(--accent-gold);
        border-radius: 20px;
        font-size: 0.95rem;
        outline: none;
        width: 260px;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 1.5rem;
    }

    .product-card {
        background: var(--card-bg);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-md);
    }

    .product-img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        background: #EFEBE9;
    }

    .product-info {
        padding: 1.2rem;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .product-category {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: var(--secondary-caramel);
        font-weight: 700;
        margin-bottom: 4px;
    }

    .product-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--primary-coffee);
        margin-bottom: 6px;
    }

    .product-desc {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-bottom: 12px;
        flex: 1;
    }

    .product-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
    }

    .product-price {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--primary-coffee);
    }

    .btn-add {
        background: var(--primary-coffee);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-add:hover {
        background: var(--secondary-caramel);
    }

    .stock-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(62, 39, 35, 0.85);
        color: white;
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 12px;
    }

    /* Modal Carrito */
    .modal-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 200;
    }

    .modal-content {
        background: white;
        width: 90%;
        max-width: 600px;
        max-height: 90vh;
        border-radius: 16px;
        padding: 2rem;
        overflow-y: auto;
    }
</style>
@section('content')

<div class="store-hero">
    <h2>Ordena en Línea para Recoger</h2>
    <p>Selecciona tus productos favoritos, elige tu mesa o zona de referencia y retira con tu código QR.</p>
</div>

<div class="filters-bar">
    <div class="category-pills">
        <button class="pill active" onclick="filterCategory('all', this)">Todos</button>
        @foreach($categories as $category)
            <button class="pill" onclick="filterCategory('cat-{{ $category->id }}', this)">{{ $category->name }}</button>
        @endforeach
    </div>

    <input type="text" id="searchInput" class="search-input" placeholder="🔍 Buscar producto..." onkeyup="filterSearch()">
</div>

<div class="products-grid" id="productsGrid">
    @foreach($products as $product)
        <div class="product-card cat-{{ $product->category_id }}" data-name="{{ strtolower($product->name) }}">
            <span class="stock-badge">Stock: {{ $product->stock }}</span>
            <img src="{{ asset($product->image ?: 'images/cafe-risa-logo-principal.png') }}" alt="{{ $product->name }}" class="product-img" onerror="this.src='{{ asset('images/cafe-risa-logo-principal.png') }}'">
            
            <div class="product-info">
                <span class="product-category">{{ $product->category?->name ?? 'General' }}</span>
                <h3 class="product-title">{{ $product->name }}</h3>
                <p class="product-desc">{{ $product->description }}</p>

                <div class="product-footer">
                    <span class="product-price">${{ number_format($product->price, 2) }}</span>
                    @if($product->stock > 0)
                        <button class="btn-add" onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, '{{ $product->code }}')">Agregar +</button>
                    @else
                        <button class="btn-add" disabled style="background: #CCC; cursor: not-allowed;">Agotado</button>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Modal Carrito & Checkout -->
<div class="modal-overlay" id="cartModal">
    <div class="modal-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid #EFEBE9; padding-bottom: 1rem;">
            <h3 style="color: var(--primary-coffee); font-size: 1.4rem;">🛒 Tu Carrito de Compra</h3>
            <button onclick="closeCartModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>

        <div id="cartItemsList" style="margin-bottom: 1.5rem;">
            <!-- Items del carrito -->
        </div>

        <div style="background: #FFF8E1; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 1.2rem; color: var(--primary-coffee);">
                <span>Total:</span>
                <span id="cartTotalText">$0.00</span>
            </div>
        </div>

        <form id="checkoutForm" onsubmit="processCheckout(event)">
            <h4 style="color: var(--primary-coffee); margin-bottom: 1rem;">Datos para Recoger</h4>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.9rem;">Nombre Completo *</label>
                <input type="text" id="custName" required placeholder="Tu nombre" style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.9rem;">Teléfono</label>
                    <input type="text" id="custPhone" placeholder="55 1234 5678" style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.9rem;">Método de Pago *</label>
                    <select id="payMethod" required onchange="toggleCardInput()" style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
                        <option value="online">Pago en Línea (Tarjeta Simulada)</option>
                        <option value="efectivo">Pagar al Recoger (Efectivo)</option>
                    </select>
                </div>
            </div>

            <div id="cardField" style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.9rem;">Tarjeta Simulada (16 dígitos) *</label>
                <input type="text" id="cardNumber" placeholder="4532 1234 5678 9010" maxlength="19" style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.9rem;">Zona / Mesa de Referencia (Opcional)</label>
                <select id="tableSelect" style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px;">
                    <option value="">-- Ninguna mesa seleccionada --</option>
                    @foreach($zones as $zone)
                        <optgroup label="{{ $zone->name }}">
                            @foreach($zone->tables as $table)
                                <option value="{{ $table->id }}">Mesa {{ $table->number }} (Capacidad: {{ $table->capacity }} personas)</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.9rem;">Cupón de Descuento (Ej. RISA10)</label>
                <input type="text" id="couponCode" placeholder="Código de cupón" style="width: 100%; padding: 8px 12px; border: 1px solid #D7CCC8; border-radius: 6px; text-transform: uppercase;">
            </div>

            <button type="submit" id="btnSubmitOrder" style="width: 100%; background: var(--primary-coffee); color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 700; font-size: 1.1rem; cursor: pointer;">
                Confirmar Pedido y Generar QR
            </button>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    let cart = [];

    function filterCategory(catClass, btn) {
        document.querySelectorAll('.pill').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');

        const cards = document.querySelectorAll('.product-card');
        cards.forEach(card => {
            if (catClass === 'all' || card.classList.contains(catClass)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function filterSearch() {
        const query = document.getElementById('searchInput').value.toLowerCase();
        const cards = document.querySelectorAll('.product-card');
        cards.forEach(card => {
            const name = card.dataset.name;
            if (name.includes(query)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function addToCart(id, name, price, code) {
        const existing = cart.find(item => item.product_id === id);
        if (existing) {
            existing.quantity++;
        } else {
            cart.push({ product_id: id, name, price, code, quantity: 1 });
        }
        updateCartUI();
    }

    function updateCartUI() {
        const count = cart.reduce((sum, i) => sum + i.quantity, 0);
        document.getElementById('cartCount').innerText = count;

        const list = document.getElementById('cartItemsList');
        let total = 0;

        if (cart.length === 0) {
            list.innerHTML = '<p style="text-align:center; color:#888;">Tu carrito está vacío.</p>';
            document.getElementById('cartTotalText').innerText = '$0.00';
            return;
        }

        let html = '';
        cart.forEach((item, index) => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;
            html += `
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; border-bottom:1px solid #F5F2EB; padding-bottom:8px;">
                    <div>
                        <strong>${item.name}</strong><br>
                        <small style="color:#666;">$${item.price.toFixed(2)} c/u</small>
                    </div>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <button onclick="changeQty(${index}, -1)" style="padding:2px 8px;">-</button>
                        <span>${item.quantity}</span>
                        <button onclick="changeQty(${index}, 1)" style="padding:2px 8px;">+</button>
                        <span style="font-weight:700; width:70px; text-align:right;">$${itemTotal.toFixed(2)}</span>
                    </div>
                </div>
            `;
        });

        list.innerHTML = html;
        document.getElementById('cartTotalText').innerText = `$${total.toFixed(2)}`;
    }

    function changeQty(index, delta) {
        cart[index].quantity += delta;
        if (cart[index].quantity <= 0) {
            cart.splice(index, 1);
        }
        updateCartUI();
    }

    document.getElementById('openCartBtn').addEventListener('click', () => {
        document.getElementById('cartModal').style.display = 'flex';
    });

    function closeCartModal() {
        document.getElementById('cartModal').style.display = 'none';
    }

    function toggleCardInput() {
        const pay = document.getElementById('payMethod').value;
        const cardField = document.getElementById('cardField');
        const cardInput = document.getElementById('cardNumber');

        if (pay === 'online') {
            cardField.style.display = 'block';
            cardInput.required = true;
        } else {
            cardField.style.display = 'none';
            cardInput.required = false;
        }
    }

    async function processCheckout(e) {
        e.preventDefault();

        if (cart.length === 0) {
            alert('Agrega al menos un producto al carrito antes de finalizar.');
            return;
        }

        const btn = document.getElementById('btnSubmitOrder');
        btn.disabled = true;
        btn.innerText = 'Procesando en MySQL...';

        const payload = {
            customer_name: document.getElementById('custName').value,
            customer_phone: document.getElementById('custPhone').value,
            payment_method: document.getElementById('payMethod').value,
            table_id: document.getElementById('tableSelect').value || null,
            coupon_code: document.getElementById('couponCode').value || null,
            card_number: document.getElementById('cardNumber').value || null,
            cart_items: cart.map(i => ({ product_id: i.product_id, quantity: i.quantity }))
        };

        try {
            const res = await fetch("{{ route('store.checkout') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();
            if (res.ok && data.success) {
                cart = [];
                window.location.href = data.redirect;
            } else {
                alert(data.error || 'Ocurrió un error al procesar el pedido.');
                btn.disabled = false;
                btn.innerText = 'Confirmar Pedido y Generar QR';
            }
        } catch (err) {
            alert('Error de conexión con el servidor.');
            btn.disabled = false;
            btn.innerText = 'Confirmar Pedido y Generar QR';
        }
    }
</script>
@endsection
