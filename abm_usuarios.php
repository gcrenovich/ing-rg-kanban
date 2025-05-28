<?php
require 'db.php';
session_start();
if ($_SESSION['rol'] !== 'admin') {
  header('Location: dashboard.php');
  exit;
}

// Alta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
  $nombre = $_POST['nombre'];
  $email = $_POST['email'];
  $clave = password_hash($_POST['clave'], PASSWORD_DEFAULT);
  $sector_id = $_POST['sector_id'];
  $rol = $_POST['rol'];
  $equipo = $_POST['equipo'];

  $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, clave, sector_id, rol, equipo) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->execute([$nombre, $email, $clave, $sector_id, $rol, $equipo]);
}

// Baja
if (isset($_GET['eliminar'])) {
  $conn->prepare("DELETE FROM usuarios WHERE id = ?")->execute([$_GET['eliminar']]);
}

// Obtener usuarios y sectores
$usuarios = $conn->query("SELECT u.*, s.nombre AS sector FROM usuarios u JOIN sectores s ON u.sector_id = s.id")->fetchAll();
$sectores = $conn->query("SELECT * FROM sectores")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>ABM Usuarios</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<h2>Gestión de Usuarios</h2>

<!-- Alta de usuario -->
<form method="POST" class="row g-3 mb-4">
  <div class="col-md-3">
    <input type="text" name="nombre" class="form-control" placeholder="Nombre" required>
  </div>
  <div class="col-md-3">
    <input type="email" name="email" class="form-control" placeholder="Email" required>
  </div>
  <div class="col-md-2">
    <input type="password" name="clave" class="form-control" placeholder="Contraseña" required>
  </div>
  <div class="col-md-2">
    <select name="sector_id" class="form-select" required>
      <option disabled selected>Sector</option>
      <?php foreach ($sectores as $s): ?>
        <option value="<?= $s['id'] ?>"><?= $s['nombre'] ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-1">
    <select name="rol" class="form-select" required>
      <option value="usuario">Usuario</option>
      <option value="admin">Admin</option>
    </select>
  </div>
  <div class="col-md-1">
    <input type="text" name="equipo" class="form-control" placeholder="Equipo" required>
  </div>
  <div class="col-12">
    <button class="btn btn-success" type="submit">Agregar</button>
  </div>
</form>

<!-- Tabla de usuarios -->
<table class="table table-bordered table-hover">
  <thead class="table-dark">
    <tr>
      <th>Nombre</th><th>Email</th><th>Rol</th><th>Sector</th><th>Equipo</th><th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($usuarios as $u): ?>
    <tr>
      <td><?= $u['nombre'] ?></td>
      <td><?= $u['email'] ?></td>
      <td><?= $u['rol'] ?></td>
      <td><?= $u['sector'] ?></td>
      <td><?= $u['equipo'] ?></td>
      <td>
        <!-- Solo eliminación por ahora -->
        <a href="?eliminar=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este usuario?')">🗑</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

</body>
</html>
