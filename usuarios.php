<?php include 'conexion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ABM Integrantes</title>
  <link href="https://cdn.jsdelivr.net/npm/@picocss/pico@1/css/pico.min.css" rel="stylesheet">
</head>
<body>
<main class="container">
  <h2>Gestión de Integrantes</h2>
  <form method="POST">
    <input type="text" name="nombre" placeholder="Nombre del integrante" required>
    <select name="rol">
      <option value="miembro">Miembro</option>
      <option value="jefe_equipo">Jefe de equipo</option>
      <option value="admin">Administrador</option>
    </select>
    <select name="equipo_id">
      <option value="">Sin equipo</option>
      <?php
        $res = mysqli_query($conn, "SELECT * FROM equipos");
        while($row = mysqli_fetch_assoc($res)) {
          echo "<option value='{$row['id']}'>{$row['nombre']}</option>";
        }
      ?>
    </select>
    <button type="submit" name="agregar">Agregar Integrante</button>
  </form>
  <hr>
  <h3>Lista de Integrantes</h3>
  <table>
    <thead><tr><th>ID</th><th>Nombre</th><th>Rol</th><th>Equipo</th><th>Acciones</th></tr></thead>
    <tbody>
      <?php
      if (isset($_POST['agregar'])) {
        $nombre = $_POST['nombre'];
        $rol = $_POST['rol'];
        $equipo_id = $_POST['equipo_id'] ?: 'NULL';
        mysqli_query($conn, "INSERT INTO usuarios (nombre, rol, equipo_id) VALUES ('$nombre', '$rol', $equipo_id)");
        header("Location: usuarios.php");
        exit();
      }
      if (isset($_GET['eliminar'])) {
        $id = $_GET['eliminar'];
        mysqli_query($conn, "DELETE FROM usuarios WHERE id=$id");
        header("Location: usuarios.php");
        exit();
      }
      $res = mysqli_query($conn, "SELECT usuarios.*, equipos.nombre as equipo FROM usuarios LEFT JOIN equipos ON usuarios.equipo_id = equipos.id");
      while($row = mysqli_fetch_assoc($res)) {
        echo "<tr><td>{$row['id']}</td><td>{$row['nombre']}</td><td>{$row['rol']}</td><td>" . ($row['equipo'] ?: '—') . "</td><td><a href='?eliminar={$row['id']}'>🗑 Eliminar</a></td></tr>";
      }
      ?>
    </tbody>
  </table>
</main>
</body>
</html>