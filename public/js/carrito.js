const cartStorageKey = "cafeteria-original-cart";
const favoritesStorageKey = "cafeteria-original-favorites";
const addButtons = [...document.querySelectorAll("[data-add]")];
const detailButtons = [...document.querySelectorAll("[data-details]")];
const comboButtons = [...document.querySelectorAll("[data-combo]")];
const productSources = [...detailButtons, ...comboButtons];
const products = Object.fromEntries(productSources.map(button => [button.dataset.code, {
    code: button.dataset.code,
    name: button.dataset.name,
    price: Number(button.dataset.price),
    stock: Number(button.dataset.stock),
    category: button.dataset.category ?? "",
    description: button.dataset.description ?? "",
    image: button.dataset.image ?? "/images/productos/cartoon-default.svg"
}]));

const drawer = document.querySelector("[data-cart]");
const overlay = document.querySelector("[data-overlay]");
const lines = document.querySelector("[data-cart-lines]");
const empty = document.querySelector("[data-cart-empty]");
const clearButton = document.querySelector("[data-clear-cart]");
const finishButton = document.querySelector("[data-finish]");
const searchInput = document.querySelector("[data-menu-search]");
const detailDialog = document.querySelector("[data-product-dialog]");
const serviceType = document.querySelector("[data-service-type]");
const tableField = document.querySelector("[data-table-field]");
const tableSelect = document.querySelector("[data-table-select]");
const zoneField = document.querySelector("[data-zone-select-field]");
const zoneFieldLabel = document.querySelector("[data-zone-field]");
const zoneStrip = document.querySelector("[data-zone-strip]");
const notesField = document.querySelector("[data-notes-field]");
const couponField = document.querySelector("[data-coupon-field]");
const paymentRadios = [...document.querySelectorAll('input[name="datos.MetodoPago"]')];
const cardFields = document.querySelector("[data-card-fields]");
const cardNumber = document.querySelector("[data-card-number]");
const cardCvv = document.querySelector("[data-card-cvv]");

let cart = loadCart();
let favorites = loadFavorites();
let activeFilter = "all";
let activeDetailCode = null;

function normalizeText(value) {
    return value.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLocaleLowerCase("es");
}

function loadCart() {
    try {
        const saved = JSON.parse(localStorage.getItem(cartStorageKey) || "{}");
        return Object.fromEntries(Object.entries(saved).filter(([code, quantity]) => products[code] && Number.isInteger(quantity) && quantity > 0));
    } catch { return {}; }
}

function loadFavorites() {
    try {
        const saved = JSON.parse(localStorage.getItem(favoritesStorageKey) || "[]");
        return new Set(saved.filter(code => products[code]));
    } catch { return new Set(); }
}

function saveCart() { localStorage.setItem(cartStorageKey, JSON.stringify(cart)); }
function saveFavorites() { localStorage.setItem(favoritesStorageKey, JSON.stringify([...favorites])); }
function money(value) { return new Intl.NumberFormat("es-MX", { style: "currency", currency: "MXN" }).format(value); }
function openCart() { drawer.classList.add("open"); drawer.setAttribute("aria-hidden", "false"); overlay.hidden = false; document.body.classList.add("cart-open"); }
function closeCart() { drawer.classList.remove("open"); drawer.setAttribute("aria-hidden", "true"); overlay.hidden = true; document.body.classList.remove("cart-open"); }

function change(code, amount) {
    if (!products[code]) return;
    const next = (cart[code] || 0) + amount;
    if (next <= 0) delete cart[code];
    else cart[code] = Math.min(next, products[code].stock);
    saveCart();
    renderCart();
}

function iconButton(symbol, label, action, code) {
    const button = document.createElement("button");
    button.type = "button";
    button.textContent = symbol;
    button.setAttribute("aria-label", label);
    button.dataset.action = action;
    button.dataset.code = code;
    return button;
}

function renderCart() {
    lines.replaceChildren();
    let count = 0;
    let total = 0;
    const entries = Object.entries(cart);

    entries.forEach(([code, quantity]) => {
        const product = products[code];
        count += quantity;
        total += product.price * quantity;
        const row = document.createElement("article");
        row.className = "cart-line";
        const copy = document.createElement("div");
        const name = document.createElement("strong");
        name.textContent = product.name;
        const subtotal = document.createElement("span");
        subtotal.textContent = money(product.price * quantity);
        copy.append(name, subtotal);
        const controls = document.createElement("div");
        controls.className = "line-controls";
        const minus = iconButton("-", `Disminuir ${product.name}`, "minus", code);
        const amount = document.createElement("b");
        amount.textContent = quantity;
        const plus = iconButton("+", `Aumentar ${product.name}`, "plus", code);
        plus.disabled = quantity >= product.stock;
        controls.append(minus, amount, plus);
        row.append(copy, controls);
        lines.append(row);
    });

    Object.keys(products).forEach(code => {
        const quantity = cart[code] || 0;
        const quantityLabel = document.querySelector(`[data-card-quantity="${code}"]`);
        const minus = document.querySelector(`[data-card-minus="${code}"]`);
        if (quantityLabel) quantityLabel.textContent = quantity;
        if (minus) minus.disabled = quantity === 0;
    });

    if (activeDetailCode) {
        document.querySelector("[data-detail-quantity]").textContent = cart[activeDetailCode] || 0;
        document.querySelector("[data-detail-minus]").disabled = !cart[activeDetailCode];
        document.querySelector("[data-detail-add]").disabled = (cart[activeDetailCode] || 0) >= products[activeDetailCode].stock;
    }

    empty.hidden = entries.length > 0;
    clearButton.disabled = entries.length === 0;
    finishButton.disabled = entries.length === 0;
    document.querySelector("[data-cart-count]").textContent = count;
    document.querySelector("[data-cart-products]").textContent = count;
    document.querySelector("[data-cart-total]").textContent = money(total);
    document.querySelector("[data-payload]").value = JSON.stringify(entries.map(([codigo, cantidad]) => ({ codigo, cantidad })));
}

function renderFavorites() {
    document.querySelectorAll("[data-favorite]").forEach(button => {
        const selected = favorites.has(button.dataset.favorite);
        button.textContent = selected ? "♥" : "♡";
        button.classList.toggle("selected", selected);
        button.setAttribute("aria-pressed", String(selected));
        button.setAttribute("aria-label", `${selected ? "Quitar" : "Agregar"} ${products[button.dataset.favorite]?.name ?? "producto"} ${selected ? "de" : "a"} favoritos`);
    });
    document.querySelector("[data-favorites-count]").textContent = favorites.size;
    applyProductFilters();
}

function applyProductFilters() {
    const query = normalizeText(searchInput.value.trim());
    let visible = 0;
    document.querySelectorAll("[data-menu-product]").forEach(card => {
        const matchesSearch = normalizeText(card.dataset.search).includes(query);
        const matchesFavorites = activeFilter === "all" || favorites.has(card.dataset.productCode);
        const show = matchesSearch && matchesFavorites;
        card.hidden = !show;
        if (show) visible++;
    });
    document.querySelector("[data-no-menu-results]").hidden = visible > 0;
}

function openDetails(button) {
    activeDetailCode = button.dataset.code;
    const product = products[activeDetailCode];
    const image = document.querySelector("[data-detail-image]");
    image.src = product.image;
    image.alt = product.name;
    document.querySelector("[data-detail-category]").textContent = product.category;
    document.querySelector("[data-detail-name]").textContent = product.name;
    document.querySelector("[data-detail-description]").textContent = product.description;
    document.querySelector("[data-detail-stock]").textContent = `${product.stock} disponibles`;
    document.querySelector("[data-detail-price]").textContent = money(product.price);
    renderCart();
    detailDialog.showModal();
}

function closeDetails() {
    detailDialog.close();
    activeDetailCode = null;
}

function toast(message) {
    const element = document.querySelector("[data-toast]");
    element.textContent = message;
    element.hidden = false;
    clearTimeout(toast.timer);
    toast.timer = setTimeout(() => element.hidden = true, 2000);
}

function renderCheckoutFields() {
    if (!serviceType || !tableField) return;
    serviceType.value = "para_llevar";
    tableField.hidden = false;
    if (zoneFieldLabel) zoneFieldLabel.hidden = false;
    if (zoneStrip) zoneStrip.hidden = false;
    renderPaymentFields();
    renderAvailableTables();
}

function renderAvailableTables() {
    if (!tableSelect) return;
    const selectedZone = zoneField?.value || "";
    const selectedZoneKey = normalizeText(selectedZone);
    let visible = 0;
    [...tableSelect.options].forEach(option => {
        if (!option.value) return;
        const show = Boolean(selectedZone) && normalizeText(option.dataset.zone || "") === selectedZoneKey;
        option.hidden = !show;
        option.disabled = !show;
        if (show) visible++;
    });
    tableSelect.options[0].textContent = selectedZone
        ? visible ? "Selecciona una mesa" : "No hay mesas libres en esta zona"
        : "Selecciona zona primero";
    tableSelect.options[0].disabled = true;
    if (!selectedZone || tableSelect.selectedOptions[0]?.disabled) tableSelect.value = "";
}

function renderPaymentFields() {
    if (!cardFields) return;
    const selectedPayment = document.querySelector('input[name="datos.MetodoPago"]:checked')?.value;
    const needsCard = selectedPayment === "online";
    cardFields.hidden = !needsCard;
    if (cardNumber) cardNumber.required = needsCard;
    if (cardCvv) cardCvv.required = needsCard;
}

addButtons.forEach(button => button.addEventListener("click", () => {
    change(button.dataset.code, 1);
    if (button.matches("[data-combo]")) toast(`${button.dataset.name} agregado al carrito`);
}));
document.querySelectorAll("[data-card-minus]").forEach(button => button.addEventListener("click", () => change(button.dataset.cardMinus, -1)));
lines.addEventListener("click", event => {
    const button = event.target.closest("[data-action]");
    if (!button) return;
    change(button.dataset.code, button.dataset.action === "plus" ? 1 : -1);
});
detailButtons.forEach(button => button.addEventListener("click", () => openDetails(button)));
document.querySelector("[data-close-details]").addEventListener("click", closeDetails);
detailDialog.addEventListener("click", event => { if (event.target === detailDialog) closeDetails(); });
document.querySelector("[data-detail-add]").addEventListener("click", () => change(activeDetailCode, 1));
document.querySelector("[data-detail-minus]").addEventListener("click", () => change(activeDetailCode, -1));
document.querySelectorAll("[data-favorite]").forEach(button => button.addEventListener("click", () => {
    const code = button.dataset.favorite;
    if (favorites.has(code)) favorites.delete(code); else favorites.add(code);
    saveFavorites();
    renderFavorites();
}));
document.querySelectorAll("[data-product-filter]").forEach(button => button.addEventListener("click", () => {
    activeFilter = button.dataset.productFilter;
    document.querySelectorAll("[data-product-filter]").forEach(item => item.classList.toggle("active", item === button));
    applyProductFilters();
}));
document.querySelectorAll("[data-zone-select]").forEach(button => button.addEventListener("click", () => {
    if (zoneField) zoneField.value = button.dataset.zoneSelect;
    renderCheckoutFields();
    toast(`Zona seleccionada: ${button.dataset.zoneSelect}`);
}));
document.querySelectorAll("[data-product-filter-shortcut]").forEach(button => button.addEventListener("click", () => {
    const target = document.querySelector(`[data-product-filter="${button.dataset.productFilterShortcut}"]`);
    target?.click();
    couponField?.focus();
    toast("Favoritos activados. Puedes aplicar un cupon en el carrito.");
}));
searchInput.addEventListener("input", applyProductFilters);
if (serviceType) serviceType.addEventListener("change", renderCheckoutFields);
if (zoneField) zoneField.addEventListener("change", renderAvailableTables);
paymentRadios.forEach(radio => radio.addEventListener("change", renderPaymentFields));
document.querySelector("[data-open-cart]").addEventListener("click", openCart);
document.querySelector("[data-close-cart]").addEventListener("click", closeCart);
document.querySelector("[data-continue]").addEventListener("click", closeCart);
overlay.addEventListener("click", closeCart);
clearButton.addEventListener("click", () => {
    if (!Object.keys(cart).length) return;
    if (!confirm("Eliminar todos los productos del carrito?")) return;
    cart = {};
    saveCart();
    renderCart();
    toast("Carrito vacio");
});
document.querySelector("[data-checkout-form]").addEventListener("submit", event => {
    if (!Object.keys(cart).length) {
        event.preventDefault();
        return;
    }
    const selectedPayment = document.querySelector('input[name="datos.MetodoPago"]:checked')?.value;
    if (selectedPayment === "online") {
        const digits = (cardNumber?.value || "").replace(/\D/g, "");
        const cvvDigits = (cardCvv?.value || "").replace(/\D/g, "");
        if (digits.length < 12 || cvvDigits.length < 3) {
            event.preventDefault();
            toast("Agrega una tarjeta ficticia valida para simular el pago en linea.");
        }
    }
});
document.addEventListener("keydown", event => { if (event.key === "Escape") { if (detailDialog.open) closeDetails(); else closeCart(); } });

renderFavorites();
renderCart();
renderCheckoutFields();
