<?php 
include "conexion.php"; 

session_start(); // Para mensajes flash

$mensaje = "";

// Recuperar mensaje flash si existe
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}

/* ============================================================
   GUARDAR PROFESOR – Validaciones Profesionales
   ============================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar'])) {

    // Sanitizar datos
    $nombre = trim($conexion->real_escape_string($_POST['nombre']));
    $profesion = trim($conexion->real_escape_string($_POST['profesion']));

    // Validar datos vacíos
    if ($nombre === "" || $profesion === "") {
        $_SESSION['mensaje'] = "Todos los campos son obligatorios.";
        header("Location: profesores.php");
        exit;
    }

    // Verificar si existe un profesor duplicado
    $consulta = $conexion->prepare("SELECT id FROM profesores WHERE nombre = ? AND profesion = ?");
    $consulta->bind_param("ss", $nombre, $profesion);
    $consulta->execute();
    $consulta->store_result();

    if ($consulta->num_rows > 0) {
        $_SESSION['mensaje'] = "⚠️ Este profesor ya está registrado.";
        header("Location: profesores.php");
        exit;
    }

    // Insertar profesionalmente
    $stmt = $conexion->prepare("INSERT INTO profesores(nombre, profesion) VALUES(?, ?)");
    $stmt->bind_param("ss", $nombre, $profesion);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "✔ Profesor registrado correctamente.";
    } else {
        $_SESSION['mensaje'] = "❌ Error al registrar el profesor.";
    }

    // Evitar duplicado por F5 (PRG)
    header("Location: profesores.php");
    exit;
}


/* ============================================================
   ELIMINAR PROFESOR – Validación incluida
   ============================================================ */
if (isset($_GET['eliminar'])) {

    $id = intval($_GET['eliminar']);

    if ($id > 0) {
        $stmt = $conexion->prepare("DELETE FROM profesores WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $_SESSION['mensaje'] = "✔ Profesor eliminado correctamente.";
    }

    header("Location: profesores.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Módulo Profesores</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

<div class="contenedor">
    <h2>Módulo de Profesores</h2>

    <!-- MENSAJE VERDE -->
    <?php if ($mensaje != ""): ?>
        <div class="alerta-verde">
            <?= $mensaje ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="nombre" placeholder="Nombre del profesor" required>
        <input type="text" name="profesion" placeholder="Profesión" required>
        <button type="submit" name="guardar">Guardar</button>
    </form>

    <h3>Listado de profesores</h3>

    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Profesión</th>
            <th>Acción</th>
        </tr>

        <?php
        $result = $conexion->query("SELECT * FROM profesores ORDER BY id DESC");
        while ($fila = $result->fetch_assoc()) {
            echo "
            <tr>
                <td>{$fila['id']}</td>
                <td>{$fila['nombre']}</td>
                <td>{$fila['profesion']}</td>
                <td><a class='btn-eliminar' href='profesores.php?eliminar={$fila['id']}'>Eliminar</a></td>
            </tr>";
        }
        ?>
    </table>

    <a class="volver" href="index.php">Volver</a>
</div>

</body>
</html>
