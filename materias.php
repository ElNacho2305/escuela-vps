<?php 
include "conexion.php"; 
session_start(); // Para mensajes flash

$mensaje = "";

// Recuperar mensaje flash
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}

/* ============================================================
   REGISTRAR MATERIA – Validaciones + seguridad
   ============================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar'])) {

    // Sanitizar datos
    $nombre = trim($conexion->real_escape_string($_POST['nombre']));
    $descripcion = trim($conexion->real_escape_string($_POST['descripcion']));

    // Validaciones
    if ($nombre === "") {
        $_SESSION['mensaje'] = "El nombre de la materia es obligatorio.";
        header("Location: materias.php");
        exit;
    }

    // Verificar duplicado (misma materia)
    $consulta = $conexion->prepare("SELECT id FROM materias WHERE nombre = ?");
    $consulta->bind_param("s", $nombre);
    $consulta->execute();
    $consulta->store_result();

    if ($consulta->num_rows > 0) {
        $_SESSION['mensaje'] = "⚠️ Esta materia ya está registrada.";
        header("Location: materias.php");
        exit;
    }

    // Insertar con prepared statement
    $stmt = $conexion->prepare("INSERT INTO materias(nombre, descripcion) VALUES(?, ?)");
    $stmt->bind_param("ss", $nombre, $descripcion);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "✔ Materia registrada correctamente.";
    } else {
        $_SESSION['mensaje'] = "❌ Error al registrar la materia.";
    }

    // PRG (evitar duplicados por recarga)
    header("Location: materias.php");
    exit;
}

/* ============================================================
   ELIMINAR MATERIA – Validado
   ============================================================ */
if (isset($_GET['eliminar'])) {

    $id = intval($_GET['eliminar']);

    if ($id > 0) {
        $stmt = $conexion->prepare("DELETE FROM materias WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $_SESSION['mensaje'] = "✔ Materia eliminada correctamente.";
    }

    header("Location: materias.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Módulo Materias</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

<!-- ====== POPUP ====== -->
<div id="popup" class="popup hidden">
    <div class="popup-content">
        <p id="popup-text"></p>
        <button onclick="cerrarPopup()">Cerrar</button>
    </div>
</div>

<div class="contenedor">
    <h2>Módulo de Materias</h2>

    <!-- FORMULARIO -->
    <form method="POST">
        <input type="text" name="nombre" placeholder="Nombre de la materia" required>
        <textarea name="descripcion" placeholder="Descripción"></textarea>
        <button type="submit" name="guardar">Guardar</button>
    </form>

    <h3>Listado de materias</h3>

    <!-- TABLA -->
    <table>
        <tr>
            <th>ID</th>
            <th>Materia</th>
            <th>Descripción</th>
            <th>Acción</th>
        </tr>

        <?php
        $result = $conexion->query("SELECT * FROM materias ORDER BY id DESC");
        while ($fila = $result->fetch_assoc()) {
            echo "
            <tr>
                <td>{$fila['id']}</td>
                <td>{$fila['nombre']}</td>
                <td>{$fila['descripcion']}</td>
                <td><a class='btn-eliminar' href='materias.php?eliminar={$fila['id']}'>Eliminar</a></td>

            </tr>";
        }
        ?>
    </table>

    <a class="volver" href="index.php">Volver</a>
</div>

<!-- JS PARA POPUP -->
<script>
function mostrarPopup(mensaje) {
    document.getElementById("popup-text").innerText = mensaje;
    document.getElementById("popup").classList.remove("hidden");
}

function cerrarPopup() {
    document.getElementById("popup").classList.add("hidden");
}
</script>

<!-- Ejecutar popup si hay mensaje -->
<?php if (!empty($mensaje)) { ?>
<script>
    mostrarPopup("<?= $mensaje ?>");
</script>
<?php } ?>

<script src="funciones.js"></script>

</body>
</html>
