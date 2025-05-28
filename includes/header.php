<?php
if (session_status() == PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel Kanban</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
  <nav>
    <a href="dashboard.php">Inicio</a>
   <?php if ($_SESSION['rol'] === 'admin'): ?>
      <a href="abm_usuarios.php">Usuarios</a>
      <a href="abm_sectores.php">Sectores</a>
    <?php endif; ?>
    <a href="abm_tareas.php">Tareas</a>
    <a href="logout.php">Salir</a>
  </nav>
</header>
