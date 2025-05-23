<?php include 'conexion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ABM Equipos</title>
  <link href="https://cdn.jsdelivr.net/npm/@picocss/pico@1/css/pico.min.css" rel="stylesheet">
</head>
<body>
<main class="container">
  <h2>Gestión de Equipos</h2>
  <form method="POST">
    <input type="text" name="nombre" placeholder="Nombre del equipo" required>
    <button type="submit" name="agregar">Agregar Equipo</button>
  </form>
  <hr>
  <h3>Lista de Equipos</h3>
  <table>
    <thead><tr><th>ID</th><th>Nombre</th><th>Acciones</th></tr></thead>
    <tbody>
      <?php
      if (isset($_POST['agregar'])) {
        $nombre = $_POST['nombre'];
        mysqli_query($conn, "INSERT INTO equipos (nombre) VALUES ('$nombre')");
        header("Location: equipos.php");
        exit();
      }
      if (isset($_GET['eliminar'])) {
        $id = $_GET['eliminar'];
        mysqli_query($conn, "DELETE FROM equipos WHERE id=$id");
        header("Location: equipos.php");
        exit();
      }
      $res = mysqli_query($conn, "SELECT * FROM equipos");
      while($row = mysqli_fetch_assoc($res)) {
        echo "<tr><td>{$row['id']}</td><td>{$row['nombre']}</td><td><a href='?eliminar={$row['id']}'>🗑 Eliminar</a></td></tr>";
      }
      ?>
    </tbody>
  </table>
</main>
</body>
</html>