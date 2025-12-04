<?php 
include "conexion.php"; 
session_start(); // Para manejar mensajes flash

$mensaje = "";

// Recuperar mensaje flash
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}

/* ============================================================
   ASIGNAR MATERIA A ESTUDIANTE
   ============================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['asignar_estudiante'])) {

    $est = intval($_POST['estudiante']);
    $mat = intval($_POST['materia']);

    if ($est <= 0 || $mat <= 0) {
        $_SESSION['mensaje'] = "Debe seleccionar un estudiante y una materia válida.";
        header("Location: asignaciones.php");
        exit;
    }

    // Verificar duplicado con prepared statement
    $stmt = $conexion->prepare("
        SELECT 1 FROM materias_estudiantes 
        WHERE id_estudiante = ? AND id_materia = ?
    ");
    $stmt->bind_param("ii", $est, $mat);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['mensaje'] = "⚠️ El estudiante ya tiene esta materia asignada.";
        header("Location: asignaciones.php");
        exit;
    }

    // Insertar asignación
    $insert = $conexion->prepare("
        INSERT INTO materias_estudiantes(id_estudiante, id_materia)
        VALUES(?, ?)
    ");
    $insert->bind_param("ii", $est, $mat);
    $insert->execute();

    $_SESSION['mensaje'] = "✔ Materia asignada al estudiante correctamente.";
    header("Location: asignaciones.php");
    exit;
}

/* ============================================================
   ASIGNAR MATERIA A PROFESOR
   ============================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['asignar_profesor'])) {

    $prof = intval($_POST['profesor']);
    $mat = intval($_POST['materia']);

    if ($prof <= 0 || $mat <= 0) {
        $_SESSION['mensaje'] = "Debe seleccionar un profesor y una materia válida.";
        header("Location: asignaciones.php");
        exit;
    }

    // Verificar duplicado
    $stmt = $conexion->prepare("
        SELECT 1 FROM materias_profesores 
        WHERE id_profesor = ? AND id_materia = ?
    ");
    $stmt->bind_param("ii", $prof, $mat);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['mensaje'] = "⚠️ El profesor ya tiene esta materia asignada.";
        header("Location: asignaciones.php");
        exit;
    }

    // Insertar asignación
    $insert = $conexion->prepare("
        INSERT INTO materias_profesores(id_profesor, id_materia)
        VALUES(?, ?)
    ");
    $insert->bind_param("ii", $prof, $mat);
    $insert->execute();

    $_SESSION['mensaje'] = "✔ Materia asignada al profesor correctamente.";
    header("Location: asignaciones.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignaciones</title>
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
<h2>Asignación de Materias</h2>

<!-- ==================== ASIGNAR A ESTUDIANTE ==================== -->
<h3>Asignar materia a estudiante</h3>

<form method="POST">
    <select name="estudiante" required>
        <option value="">Seleccione un estudiante</option>
        <?php
        $est = $conexion->query("SELECT * FROM estudiantes ORDER BY nombre");
        while ($e = $est->fetch_assoc()) {
            echo "<option value='{$e['id']}'>{$e['nombre']}</option>";
        }
        ?>
    </select>

    <select name="materia" required>
        <option value="">Seleccione una materia</option>
        <?php
        $mat = $conexion->query("SELECT * FROM materias ORDER BY nombre");
        while ($m = $mat->fetch_assoc()) {
            echo "<option value='{$m['id']}'>{$m['nombre']}</option>";
        }
        ?>
    </select>

    <button name="asignar_estudiante">Asignar</button>
</form>

<br>

<h3>Materias asignadas a cada estudiante</h3>

<table>
    <tr>
        <th>Estudiante</th>
        <th>Materias</th>
    </tr>

    <?php
    $estudiantes = $conexion->query("SELECT * FROM estudiantes ORDER BY nombre");

    while ($est = $estudiantes->fetch_assoc()) {

        $stmt = $conexion->prepare("
            SELECT m.nombre 
            FROM materias_estudiantes me
            INNER JOIN materias m ON me.id_materia = m.id
            WHERE me.id_estudiante = ?
        ");
        $stmt->bind_param("i", $est['id']);
        $stmt->execute();
        $materias = $stmt->get_result();

        $lista = ($materias->num_rows > 0)
            ? implode(", ", array_column($materias->fetch_all(MYSQLI_ASSOC), "nombre"))
            : "Sin materias asignadas";

        echo "
        <tr>
            <td>{$est['nombre']}</td>
            <td>{$lista}</td>
        </tr>";
    }
    ?>
</table>

<br><hr><br>

<!-- ==================== ASIGNAR A PROFESOR ==================== -->
<h3>Asignar materia a profesor</h3>

<form method="POST">
    <select name="profesor" required>
        <option value="">Seleccione un profesor</option>
        <?php
        $prof = $conexion->query("SELECT * FROM profesores ORDER BY nombre");
        while ($p = $prof->fetch_assoc()) {
            echo "<option value='{$p['id']}'>{$p['nombre']}</option>";
        }
        ?>
    </select>

    <select name="materia" required>
        <option value="">Seleccione una materia</option>
        <?php
        $mat = $conexion->query("SELECT * FROM materias ORDER BY nombre");
        while ($m = $mat->fetch_assoc()) {
            echo "<option value='{$m['id']}'>{$m['nombre']}</option>";
        }
        ?>
    </select>

    <button name="asignar_profesor">Asignar</button>
</form>

<br>

<h3>Materias asignadas a cada profesor</h3>

<table>
    <tr>
        <th>Profesor</th>
        <th>Materias</th>
    </tr>

    <?php
    $profesores = $conexion->query("SELECT * FROM profesores ORDER BY nombre");

    while ($pr = $profesores->fetch_assoc()) {

        $stmt = $conexion->prepare("
            SELECT m.nombre 
            FROM materias_profesores mp
            INNER JOIN materias m ON mp.id_materia = m.id
            WHERE mp.id_profesor = ?
        ");
        $stmt->bind_param("i", $pr['id']);
        $stmt->execute();
        $materias = $stmt->get_result();

        $lista = ($materias->num_rows > 0)
            ? implode(", ", array_column($materias->fetch_all(MYSQLI_ASSOC), "nombre"))
            : "Sin materias asignadas";

        echo "
        <tr>
            <td>{$pr['nombre']}</td>
            <td>{$lista}</td>
        </tr>";
    }
    ?>
</table>

<br><br>

<a class="volver" href="index.php">Volver</a>

</div>

<!-- ====== JS PARA POPUP ====== -->
<script>
function mostrarPopup(mensaje) {
    document.getElementById("popup-text").innerText = mensaje;
    document.getElementById("popup").classList.remove("hidden");
}

function cerrarPopup() {
    document.getElementById("popup").classList.add("hidden");
}
</script>

<?php if (!empty($mensaje)) { ?>
<script>
    mostrarPopup("<?= $mensaje ?>");
</script>
<?php } ?>

</body>
</html>
