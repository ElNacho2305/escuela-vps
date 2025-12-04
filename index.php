<?php include "conexion.php"; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema Escolar</title>

    <!-- Fuente moderna -->
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Archivo CSS -->
    <link rel="stylesheet" href="estilos.css">
</head>


<header class="header">
    <h1 class="titulo-principal">Sistema Escolar</h1>
    
</header>

<div class="contenedor">

    <h2 class="titulo-seccion">Módulos del Sistema</h2>

    <div class="menu animado">
        <a href="estudiantes.php" class="btn-menu"> Módulo de Estudiantes</a>
        <a href="profesores.php" class="btn-menu"> Módulo de Profesores</a>
        <a href="materias.php" class="btn-menu"> Módulo de Materias</a>
        <a href="asignaciones.php" class="btn-menu"> Asignación de Materias</a>
    </div>

</div>

<script src="funciones.js"></script>
</body>
</html>
