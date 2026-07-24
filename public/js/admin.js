const sidebar = document.querySelector("[data-sidebar]");
const overlay = document.querySelector("[data-sidebar-overlay]");
const menuToggle = document.querySelector("[data-menu-toggle]");
const toast = document.querySelector("[data-admin-toast]");
let editingTarget = null;

function closeMenu() { sidebar?.classList.remove("open"); if (overlay) overlay.hidden = true; }
menuToggle?.addEventListener("click", () => { sidebar.classList.toggle("open"); overlay.hidden = !sidebar.classList.contains("open"); });
overlay?.addEventListener("click", closeMenu);

window.showAdminToast = function (message) {
    if (!toast) return;
    toast.textContent = message; toast.hidden = false;
    clearTimeout(showAdminToast.timer); showAdminToast.timer = setTimeout(() => toast.hidden = true, 2400);
};

document.querySelectorAll("[data-open-dialog]").forEach(button => button.addEventListener("click", () => document.querySelector(button.dataset.openDialog)?.showModal()));
document.querySelectorAll("[data-close-dialog]").forEach(button => button.addEventListener("click", () => button.closest("dialog")?.close()));

function filterCollection(group, selected) {
    const query = selected.toLowerCase();
    const panel = group.closest(".panel");
    const tableRows = panel?.querySelectorAll("tbody tr");
    if (tableRows?.length) tableRows.forEach(row => row.hidden = query !== "todos" && !row.textContent.toLowerCase().includes(query));
    const recipes = document.querySelectorAll(".recipe-grid .recipe-card");
    if (!panel && recipes.length) recipes.forEach(card => card.hidden = query !== "todos" && !card.textContent.toLowerCase().includes(query));
}

document.querySelectorAll(".chips").forEach(group => group.addEventListener("click", event => {
    const chip = event.target.closest(".chip"); if (!chip) return;
    group.querySelectorAll(".chip").forEach(item => item.classList.remove("active", "gold-active"));
    chip.classList.add(group.dataset.gold !== undefined ? "gold-active" : "active");
    filterCollection(group, chip.textContent.trim());
}));

document.querySelectorAll("[data-table-search]").forEach(input => input.addEventListener("input", event => {
    const panel = event.target.closest(".panel"); const query = event.target.value.toLowerCase().trim();
    panel?.querySelectorAll("tbody tr").forEach(row => row.hidden = !row.textContent.toLowerCase().includes(query));
}));
const recipeSearch = document.querySelector('input[placeholder="Buscar receta..."]');
recipeSearch?.addEventListener("input", event => {
    const query = event.target.value.toLowerCase().trim();
    document.querySelectorAll(".recipe-grid .recipe-card").forEach(card => card.hidden = !card.textContent.toLowerCase().includes(query));
});

document.querySelectorAll("[data-card-search]").forEach(input => input.addEventListener("input", event => {
    const query = event.target.value.toLowerCase().trim();
    document.querySelectorAll("[data-recipe-card]").forEach(card => card.hidden = !card.textContent.toLowerCase().includes(query));
}));

function openRecordEditor(button) {
    editingTarget = button.closest("tr")?.querySelector("td strong");
    if (!editingTarget) return;
    document.querySelector('#record-dialog input[name="recordName"]').value = editingTarget.textContent.trim();
    document.querySelector("#record-dialog").showModal();
}
document.querySelector("[data-record-form]")?.addEventListener("submit", event => {
    event.preventDefault(); const value = event.currentTarget.elements.recordName.value.trim();
    if (editingTarget && value) editingTarget.textContent = value;
    event.currentTarget.closest("dialog").close(); showAdminToast("Registro actualizado"); editingTarget = null;
});

function exportVisibleTable() {
    const table = document.querySelector(".data-table"); if (!table) return;
    const rows = [...table.querySelectorAll("tr")].filter(row => !row.hidden).map(row => [...row.querySelectorAll("th,td")].map(cell => `"${cell.innerText.replaceAll('"', '""').trim()}"`).join(","));
    const blob = new Blob(["\ufeff" + rows.join("\n")], { type: "text/csv;charset=utf-8" });
    const link = document.createElement("a"); link.href = URL.createObjectURL(blob); link.download = `reporte-cafe-de-la-risa-${new Date().toISOString().slice(0, 10)}.csv`; link.click(); URL.revokeObjectURL(link.href);
    showAdminToast("Reporte CSV descargado");
}

function openRecipeDetail(link) {
    const card = link.closest(".recipe-card"); const dialog = document.querySelector("#recipe-detail-dialog");
    dialog.querySelector("[data-recipe-title]").textContent = card.querySelector("h3").textContent;
    dialog.querySelector("[data-recipe-icon]").textContent = card.querySelector(".food-photo").textContent.trim().slice(-2);
    dialog.querySelector("[data-recipe-summary]").textContent = "Resumen de costos y precio de venta de la receta seleccionada.";
    const values = card.querySelectorAll(".price-pair strong"); dialog.querySelector("[data-recipe-production]").textContent = `Producción ${values[0].textContent}`; dialog.querySelector("[data-recipe-sale]").textContent = `Venta ${values[1].textContent}`;
    dialog.showModal();
}

document.querySelectorAll("[data-demo-action]").forEach(control => control.addEventListener("click", event => {
    event.preventDefault(); const label = control.textContent.trim().toUpperCase();
    if (label.includes("EXPORTAR")) return exportVisibleTable();
    if (label.includes("GESTIONAR")) { location.href = "/Admin/Catalogo"; return; }
    if (label.includes("ACTUALIZAR")) { location.reload(); return; }
    if (label.includes("VER LOGS")) { document.querySelector("#history-dialog")?.showModal(); return; }
    if (control.closest(".recipe-card")) return openRecipeDetail(control);
    if (control.classList.contains("icon-action")) {
        const deleting = control.textContent.includes("⌫");
        if (!deleting) return openRecordEditor(control);
        if (confirm("¿Eliminar este registro del prototipo?")) { control.closest("tr")?.remove(); showAdminToast("Registro eliminado"); }
        return;
    }
    if (label.includes("AUDITAR")) { control.textContent = "Auditando…"; control.disabled = true; setTimeout(() => { control.textContent = "Auditado"; showAdminToast("Auditoría marcada para revisión"); }, 500); return; }
    showAdminToast(control.dataset.demoAction);
}));

document.querySelectorAll("[data-modal-form]").forEach(form => form.addEventListener("submit", event => {
    event.preventDefault(); const dialog = form.closest("dialog");
    if (dialog.id === "insumo-dialog") {
        const fields = form.querySelectorAll("input,select"); const tbody = document.querySelector(".data-table tbody");
        const row = document.createElement("tr"); row.innerHTML = `<td><div class="item-cell"><span class="item-thumb">📦</span><div><strong></strong><small>SKU: NUEVO</small></div></div></td><td><span class="status-pill green"></span></td><td></td><td><span class="stock-value"></span></td><td><strong></strong></td><td><div class="icon-actions"><button type="button" class="icon-action" data-new-edit>✎</button><button type="button" class="icon-action danger" data-new-delete>⌫</button></div></td>`;
        row.querySelector(".item-cell strong").textContent = fields[0].value; row.querySelector(".status-pill").textContent = fields[1].value; row.children[2].textContent = fields[2].value.includes("Piezas") ? "pzas" : "kg"; row.querySelector(".stock-value").textContent = `${fields[3].value || 0} ${row.children[2].textContent}`; row.children[4].querySelector("strong").textContent = `$${Number(fields[4].value || 0).toFixed(2)}`; tbody.append(row);
        row.querySelector("[data-new-edit]").addEventListener("click", event => openRecordEditor(event.currentTarget)); row.querySelector("[data-new-delete]").addEventListener("click", () => { row.remove(); showAdminToast("Registro eliminado"); });
    }
    if (dialog.id === "receta-dialog") {
        const fields = form.querySelectorAll("input,select,textarea"); const card = document.createElement("article"); card.className = "recipe-card"; card.innerHTML = `<div class="food-photo"><span class="status-pill yellow"></span>🍽️</div><div class="recipe-body"><div class="recipe-title-row"><h3></h3><span class="status-pill green">Nueva</span></div><div class="price-pair"><div><span>PRODUCCIÓN</span><strong>$0.00</strong></div><div><span>VENTA</span><strong></strong></div></div><footer class="recipe-footer"><strong>Por calcular</strong><button type="button" class="detail-button">Ver detalle →</button></footer></div>`;
        card.querySelector("h3").textContent = fields[0].value; card.querySelector(".food-photo .status-pill").textContent = fields[1].value; card.querySelectorAll(".price-pair strong")[1].textContent = `$${Number(fields[2].value || 0).toFixed(2)}`; card.querySelector(".detail-button").addEventListener("click", event => openRecipeDetail(event.currentTarget)); document.querySelector(".recipe-grid").append(card);
    }
    if (dialog.id === "sucursal-dialog") {
        const fields = form.querySelectorAll("input"); const card = document.createElement("article"); card.className = "branch-card"; card.innerHTML = `<div class="branch-photo modern">🏪<span class="status-pill green">Abierto</span></div><div class="branch-info"><div><strong></strong><small></small></div><span>›</span></div>`; card.querySelector("strong").textContent = fields[0].value; card.querySelector("small").textContent = fields[1].value; document.querySelector(".branch-grid").append(card);
    }
    dialog.close(); form.reset(); showAdminToast(form.dataset.success || "Cambios guardados correctamente");
}));

const orderSelect = [...document.querySelectorAll("select")].find(select => select.textContent.includes("Ordenar por"));
orderSelect?.addEventListener("change", () => {
    const tbody = orderSelect.closest(".panel").querySelector("tbody"); const rows = [...tbody.rows];
    rows.sort((a, b) => orderSelect.selectedIndex === 1 ? parseFloat(a.cells[3].innerText.replace(/[^0-9.]/g, "")) - parseFloat(b.cells[3].innerText.replace(/[^0-9.]/g, "")) : a.cells[0].innerText.localeCompare(b.cells[0].innerText, "es")); rows.forEach(row => tbody.append(row));
});
const branchSelect = [...document.querySelectorAll("select")].find(select => select.textContent.includes("Todas las sucursales"));
branchSelect?.addEventListener("change", () => { const value = branchSelect.value; branchSelect.closest(".panel").querySelectorAll("tbody tr").forEach(row => row.hidden = value !== "Todas las sucursales" && !row.textContent.includes(value)); });

document.querySelectorAll(".view-toggle button").forEach(button => button.addEventListener("click", () => {
    button.parentElement.querySelectorAll("button").forEach(item => item.classList.remove("active")); button.classList.add("active");
    document.querySelector(".branch-grid")?.classList.toggle("list-view", button.getAttribute("aria-label") === "Lista");
}));
