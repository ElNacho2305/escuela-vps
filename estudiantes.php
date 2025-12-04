<?php 
include "conexion.php"; 

session_start(); // Para mensajes flash

$mensaje = "";

// Mostrar mensaje si existe
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}

/* ============================================================
   GUARDAR ESTUDIANTE (POST) – Con validaciones profesionales
   ============================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar'])) {

    // Evitar inyección SQL y espacios
    $nombre = trim($conexion->real_escape_string($_POST['nombre']));
    $edad   = intval($_POST['edad']);

    // Validaciones
    if ($nombre === "" || $edad <= 0) {
        $_SESSION['mensaje'] = "Todos los campos son obligatorios.";
        header("Location: estudiantes.php");
        exit;
    }

    // Verificar que no exista estudiante con mismo nombre y edad
    $consulta = $conexion->prepare("SELECT id FROM estudiantes WHERE nombre = ? AND edad = ?");
    $consulta->bind_param("si", $nombre, $edad);
    $consulta->execute();
    $consulta->store_result();

    if ($consulta->num_rows > 0) {
        $_SESSION['mensaje'] = "⚠️ Este estudiante ya está registrado.";
        header("Location: estudiantes.php");
        exit;
    }

    // Insertar con prepared statements
    $stmt = $conexion->prepare("INSERT INTO estudiantes(nombre, edad) VALUES (?, ?)");
    $stmt->bind_param("si", $nombre, $edad);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "✔ Estudiante registrado correctamente.";
    } else {
        $_SESSION['mensaje'] = "❌ Error al registrar el estudiante.";
    }

    // Evita duplicados por recarga (PRG)
    header("Location: estudiantes.php");
    exit;
}

/* ============================================================
   ELIMINAR ESTUDIANTE – Validado
   ============================================================ */
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);

    if ($id > 0) {
        $stmt = $conexion->prepare("DELETE FROM estudiantes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $_SESSION['mensaje'] = "✔ Estudiante eliminado correctamente.";
    }

    header("Location: estudiantes.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Módulo Estudiantes</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

<div class="contenedor">
    <h2>Módulo de Estudiantes</h2>

    <!-- MENSAJE FLASH -->
    <?php if ($mensaje != ""): ?>
        <div class="alerta-verde">
            <?= $mensaje ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="nombre" placeholder="Nombre del estudiante" required>
        <input type="number" name="edad" placeholder="Edad" min="1" required>
        <button type="submit" name="guardar">Guardar</button>
    </form>

    <h3>Listado de estudiantes</h3>

    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Edad</th>
            <th>Acción</th>
        </tr>

        <?php
        $result = $conexion->query("SELECT * FROM estudiantes ORDER BY id DESC");
        while ($fila = $result->fetch_assoc()) {
            echo "
            <tr>
                <td>{$fila['id']}</td>
                <td>{$fila['nombre']}</td>
                <td>{$fila['edad']}</td>
                <td><a class='btn-eliminar' href='estudiantes.php?eliminar={$fila['id']}'>Eliminar</a></td>
            </tr>";
        }
        ?>
    </table>

    <a class="volver" href="index.php">Volver</a>
</div>

</body>
</html>
