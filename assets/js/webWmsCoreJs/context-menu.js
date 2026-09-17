document.addEventListener("DOMContentLoaded", function () {
    const table = document.getElementById("articleTable");
    const contextMenu = document.querySelector(".context-menu");

    if (!table || !contextMenu) return;

    // Rechtsklick nur im Table aktivieren
    table.addEventListener("contextmenu", function (event) {
        event.preventDefault();
        const { clientX: mouseX, clientY: mouseY } = event;

        contextMenu.style.top = `${mouseY}px`;
        contextMenu.style.left = `${mouseX}px`;
        contextMenu.style.position = "absolute";
        contextMenu.style.zIndex = "10000";

        contextMenu.classList.remove("d-none");
    });

    // Klick außerhalb: Menü wieder ausblenden
    document.addEventListener("click", function (event) {
        if (!contextMenu.contains(event.target)) {
            contextMenu.classList.add("d-none");
        }
    });

    // Optional: Escape-Taste schließt Menü
    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape") {
            contextMenu.classList.add("d-none");
        }
    });
});