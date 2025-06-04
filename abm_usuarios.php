<?php
session_start();
require 'db.php';
include 'includes/navbar.php';

if ($_SESSION['rol'] !== 'admin') {
  header('Location: dashboard.php');
  exit;
}

$mensaje = "";

// Alta (solo si no es edición)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre']) && !isset($_POST['editar_id'])) {
  $email = $_POST['email'];
  $existe = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ?");
  $existe->execute([$email]);

  if ($existe->fetchColumn() > 0) {
    $mensaje = "<div class='alert alert-danger'>❌ El email ya está registrado.</div>";
  } else {
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, clave, sector_id, rol, equipo) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
      $_POST['nombre'],
      $email,
      password_hash($_POST['clave'], PASSWORD_DEFAULT),
      $_POST['sector_id'],
      $_POST['rol'],
      $_POST['equipo']
    ]);
    $mensaje = "<div class='alert alert-success'>✅ Usuario agregado correctamente.</div>";
  }
}

// Baja
if (isset($_GET['eliminar'])) {
  $conn->prepare("DELETE FROM usuarios WHERE id = ?")->execute([$_GET['eliminar']]);
  $mensaje = "<div class='alert alert-warning'>Usuario eliminado.</div>";
}

// Modificación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_id'])) {
  $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, email = ?, equipo = ?, sector_id = ?, rol = ? WHERE id = ?");
  $stmt->execute([
    $_POST['nombre'],
    $_POST['email'],
    $_POST['equipo'],
    $_POST['sector_id'],
    $_POST['rol'],
    $_POST['editar_id']
  ]);
  $mensaje = "<div class='alert alert-success'>✅ Usuario actualizado correctamente.</div>";
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

<?= $mensaje ?>

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
      <form method="POST">
        <input type="hidden" name="editar_id" value="<?= $u['id'] ?>">
        <td><input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($u['nombre']) ?>"></td>
        <td><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($u['email']) ?>"></td>
        <td>
          <select name="rol" class="form-select">
            <option value="usuario" <?= $u['rol'] === 'usuario' ? 'selected' : '' ?>>Usuario</option>
            <option value="admin" <?= $u['rol'] === 'admin' ? 'selected' : '' ?>>Admin</option>
          </select>
        </td>
        <td>
          <select name="sector_id" class="form-select">
            <?php foreach ($sectores as $s): ?>
              <option value="<?= $s['id'] ?>" <?= $s['id'] == $u['sector_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($s['nombre']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </td>
        <td><input type="text" name="equipo" class="form-control" value="<?= htmlspecialchars($u['equipo']) ?>"></td>
        <td>
          <button type="submit" class="btn btn-primary btn-sm">💾 Guardar</button>
          <a href="?eliminar=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este usuario?')">🗑</a>
        </td>
      </form>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

</body>
</html>
