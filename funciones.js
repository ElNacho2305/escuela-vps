// Archivo funciones generales
console.log("Sistema Escolar activo");

// ====== MOSTRAR POPUP ======
function mostrarPopup(mensaje) {
    const popup = document.getElementById("popup");
    const texto = document.getElementById("popup-text");

    if (popup && texto) {
        texto.innerText = mensaje;
        popup.classList.remove("hidden");
    }
}

// ====== CERRAR POPUP ======
function cerrarPopup() {
    const popup = document.getElementById("popup");
    if (popup) {
        popup.classList.add("hidden");
    }
}

// ====== CONFIRMAR ELIMINACIÓN ======
function confirmarEliminacion(url) {
    if (confirm("¿Seguro que deseas eliminar este registro?")) {
        window.location.href = url;
    }
}
