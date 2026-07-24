document.querySelector("[data-menu-toggle]")?.addEventListener("click", () => {
    document.querySelector("[data-sidebar]")?.classList.add("open");
    const overlay = document.querySelector("[data-sidebar-overlay]");
    if (overlay) overlay.hidden = false;
});

document.querySelector("[data-sidebar-overlay]")?.addEventListener("click", () => {
    document.querySelector("[data-sidebar]")?.classList.remove("open");
    document.querySelector("[data-sidebar-overlay]").hidden = true;
});

document.querySelectorAll("[data-open-dialog]").forEach(button => {
    button.addEventListener("click", () => document.querySelector(button.dataset.openDialog)?.showModal());
});

document.querySelectorAll("[data-close-dialog]").forEach(button => {
    button.addEventListener("click", () => button.closest("dialog")?.close());
});
