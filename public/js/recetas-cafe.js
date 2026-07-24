const suppliesNode = document.querySelector("#recipe-supplies-data");
const recipesNode = document.querySelector("#recipes-data");
const recipeForm = document.querySelector("#recipe-editor-dialog form");
const rowsHost = document.querySelector("[data-recipe-ingredient-rows]");
const addIngredientButton = document.querySelector("[data-add-recipe-ingredient]");
const productSelect = document.querySelector("[data-recipe-product]");
const recipeNameInput = document.querySelector("[data-recipe-name]");
const recipeYieldInput = document.querySelector("[data-recipe-yield]");
const recipeJsonInput = document.querySelector("[data-recipe-ingredients-json]");
const recipeCost = document.querySelector("[data-recipe-cost]");

const supplies = suppliesNode ? JSON.parse(suppliesNode.textContent || "[]") : [];
const recipes = recipesNode ? JSON.parse(recipesNode.textContent || "[]") : [];
const recipesByProduct = Object.fromEntries(recipes.map(recipe => [recipe.productoCodigo, recipe]));

function money(value) {
    return new Intl.NumberFormat("es-MX", { style: "currency", currency: "MXN" }).format(value || 0);
}

function optionMarkup(selectedId) {
    return supplies.map(item => {
        const selected = Number(selectedId) === Number(item.insumoId) ? "selected" : "";
        return `<option value="${item.insumoId}" data-cost="${item.costo}" data-unit="${item.unidad}" ${selected}>${item.nombre} (${item.existencia} ${item.unidad})</option>`;
    }).join("");
}

function addIngredientRow(value = {}) {
    if (!rowsHost || supplies.length === 0) return;
    const row = document.createElement("div");
    row.className = "ingredient-row";
    row.innerHTML = `
        <label>Insumo<select data-recipe-insumo required>${optionMarkup(value.insumoId)}</select></label>
        <label>Cantidad<input data-recipe-cantidad type="number" min="0.001" step="0.001" required value="${value.cantidad ?? 1}" /></label>
        <span data-recipe-unit></span>
        <button type="button" aria-label="Eliminar insumo">x</button>`;
    row.querySelector("button").addEventListener("click", () => { row.remove(); updateRecipePayload(); });
    row.querySelectorAll("select,input").forEach(control => control.addEventListener("input", updateRecipePayload));
    rowsHost.append(row);
    updateRecipePayload();
}

function updateRecipePayload() {
    const rows = [...document.querySelectorAll(".ingredient-row")];
    const payload = rows.map(row => {
        const select = row.querySelector("[data-recipe-insumo]");
        const quantity = Number(row.querySelector("[data-recipe-cantidad]").value || 0);
        const selected = select.selectedOptions[0];
        row.querySelector("[data-recipe-unit]").textContent = selected?.dataset.unit || "";
        return {
            insumoId: Number(select.value),
            cantidad: quantity,
            costo: Number(selected?.dataset.cost || 0)
        };
    }).filter(item => item.insumoId > 0 && item.cantidad > 0);

    recipeJsonInput.value = JSON.stringify(payload.map(item => ({ insumoId: item.insumoId, cantidad: item.cantidad })));
    const rendimiento = Math.max(Number(recipeYieldInput?.value || 1), 0.001);
    const total = payload.reduce((sum, item) => sum + item.cantidad * item.costo, 0) / rendimiento;
    if (recipeCost) recipeCost.textContent = money(total);
}

function loadRecipe(productCode) {
    if (!rowsHost) return;
    rowsHost.replaceChildren();
    const recipe = recipesByProduct[productCode];
    if (recipe) {
        if (recipeNameInput) recipeNameInput.value = recipe.nombre || "";
        if (recipeYieldInput) recipeYieldInput.value = recipe.rendimiento || 1;
        if (recipe.preparacion && recipeForm?.elements.preparacion) recipeForm.elements.preparacion.value = recipe.preparacion;
        (recipe.insumos || []).forEach(addIngredientRow);
    } else {
        const selectedText = productSelect?.selectedOptions[0]?.textContent?.split("-")[0]?.trim() || "";
        if (recipeNameInput) recipeNameInput.value = selectedText ? `Receta ${selectedText}` : "";
        if (recipeYieldInput) recipeYieldInput.value = 1;
        if (recipeForm?.elements.preparacion) recipeForm.elements.preparacion.value = "";
        addIngredientRow();
    }
    updateRecipePayload();
}

addIngredientButton?.addEventListener("click", () => addIngredientRow());
productSelect?.addEventListener("change", () => loadRecipe(productSelect.value));
recipeYieldInput?.addEventListener("input", updateRecipePayload);

document.querySelectorAll("[data-edit-recipe]").forEach(button => button.addEventListener("click", () => {
    if (productSelect) productSelect.value = button.dataset.editRecipe;
    loadRecipe(button.dataset.editRecipe);
}));

recipeForm?.addEventListener("submit", event => {
    updateRecipePayload();
    if (supplies.length === 0 || JSON.parse(recipeJsonInput.value || "[]").length === 0) {
        event.preventDefault();
        alert("Agrega al menos un insumo del inventario para guardar la receta.");
    }
});

if (productSelect) loadRecipe(productSelect.value);
