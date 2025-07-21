<?php
session_start();
require 'db.php';
include 'includes/navbar.php';
include 'includes/header.php';

// Solo admin puede acceder
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
  header('Location: dashboard.php');
  exit;
}

$mensaje = "";

// 1) ALTA DE USUARIO (POST trae 'nombre' y NO trae 'editar_id')
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre']) && !isset($_POST['editar_id'])) {
  $email = trim($_POST['email']);

  // Verificar si ya existe el email
  $existe = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ?");
  $existe->execute([$email]);

  if ($existe->fetchColumn() > 0) {
    $mensaje = "<div class='alert alert-danger'>❌ El email ya está registrado.</div>";
  } else {
    $stmt = $conn->prepare("
      INSERT INTO usuarios (nombre, email, clave, sector_id, rol, equipo)
      VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
      trim($_POST['nombre']),
      $email,
      password_hash($_POST['clave'], PASSWORD_DEFAULT),
      $_POST['sector_id'],
      $_POST['rol'],
      trim($_POST['equipo'])
    ]);
    $mensaje = "<div class='alert alert-success'>✅ Usuario agregado correctamente.</div>";
  }
}

// 2) BAJA DE USUARIO (GET trae 'eliminar')
if (isset($_GET['eliminar'])) {
  $conn->prepare("DELETE FROM usuarios WHERE id = ?")->execute([$_GET['eliminar']]);
  $mensaje = "<div class='alert alert-warning'>Usuario eliminado.</div>";
}

// 3) EDICIÓN DE USUARIO (POST trae 'editar_id')
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_id'])) {
  $id_editar   = $_POST['editar_id'];
  $nuevo_email = trim($_POST['email']);

  // Verificar si el nuevo email ya lo tiene otro usuario
  $existe2 = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ? AND id <> ?");
  $existe2->execute([$nuevo_email, $id_editar]);

  if ($existe2->fetchColumn() > 0) {
    $mensaje = "<div class='alert alert-danger'>❌ Ese email ya lo tiene otro usuario.</div>";
  } else {
    // Siempre actualizamos estos campos
    $campos = "nombre = ?, email = ?, equipo = ?, sector_id = ?, rol = ?";
    $params = [
      trim($_POST['nombre']),
      $nuevo_email,
      trim($_POST['equipo']),
      $_POST['sector_id'],
      $_POST['rol']
    ];

    // Si se envió una nueva contraseña, la agregamos al UPDATE
    if (!empty($_POST['clave'])) {
      $campos    .= ", clave = ?";
      $params[]  = password_hash($_POST['clave'], PASSWORD_DEFAULT);
    }

    // Luego agregamos el id al final del array
    $params[] = $id_editar;

    $sql = "UPDATE usuarios SET $campos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    $mensaje = "<div class='alert alert-success'>✅ Usuario actualizado correctamente.</div>";
  }
}

// 4) Obtener lista de usuarios y sectores
$usuarios = $conn->query("
  SELECT u.*, s.nombre AS sector
  FROM usuarios u
  JOIN sectores s ON u.sector_id = s.id
  ORDER BY u.nombre
")->fetchAll();

$sectores = $conn->query("SELECT * FROM sectores ORDER BY nombre")->fetchAll();
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

<!-- 1) FORMULARIO DE ALTA DE USUARIO -->
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
        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nombre']) ?></option>
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

<!-- 2) TABLA DE USUARIOS -->
<table class="table table-bordered table-hover">
  <thead class="table-dark">
    <tr>
      <th>Nombre</th>
      <th>Email</th>
      <th>Rol</th>
      <th>Sector</th>
      <th>Equipo</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($usuarios as $u): ?>
      <tr>
        <?php if (isset($_GET['editar']) && $_GET['editar'] == $u['id']): ?>
          <!-- FORMULARIO DE EDICIÓN IN‐LINE -->
          <form method="POST" class="row gx-2 gy-1 align-items-center">
            <input type="hidden" name="editar_id" value="<?= $u['id'] ?>">

            <td class="col-md-2">
              <input
                type="text"
                name="nombre"
                class="form-control"
                value="<?= htmlspecialchars($u['nombre']) ?>"
                required
              >
            </td>
            <td class="col-md-2">
              <input
                type="email"
                name="email"
                class="form-control"
                value="<?= htmlspecialchars($u['email']) ?>"
                required
              >
            </td>
            <td class="col-md-1">
              <select name="rol" class="form-select">
                <option value="usuario" <?= $u['rol'] === 'usuario' ? 'selected' : '' ?>>Usuario</option>
                <option value="admin" <?= $u['rol'] === 'admin' ? 'selected' : '' ?>>Admin</option>
              </select>
            </td>
            <td class="col-md-2">
              <select name="sector_id" class="form-select">
                <?php foreach ($sectores as $s): ?>
                  <option value="<?= $s['id'] ?>" <?= $s['id'] == $u['sector_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($s['nombre']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </td>
            <td class="col-md-1">
              <input
                type="text"
                name="equipo"
                class="form-control"
                value="<?= htmlspecialchars($u['equipo']) ?>"
              >
            </td>
            <td class="col-md-4">
              <!-- Campo Contraseña opcional: dejar vacío para no cambiar -->
              <label class="form-label mb-0 small">Contraseña:</label>
              <input
                type="password"
                name="clave"
                class="form-control"
                placeholder="Dejar vacío para no cambiar"
              >
              <div class="mt-1">
                <button type="submit" class="btn btn-primary btn-sm me-1">💾 Guardar</button>
                <a href="abm_usuarios.php" class="btn btn-secondary btn-sm">❌ Cancelar</a>
              </div>
            </td>
          </form>

        <?php else: ?>
          <!-- FILA NORMAL (LECTURA) -->
          <td><?= htmlspecialchars($u['nombre']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= htmlspecialchars($u['rol']) ?></td>
          <td><?= htmlspecialchars($u['sector']) ?></td>
          <td><?= htmlspecialchars($u['equipo']) ?></td>
          <td>
            <a
              href="?editar=<?= $u['id'] ?>"
              class="btn btn-sm btn-warning me-1"
            >✏️</a>
            <a
              href="?eliminar=<?= $u['id'] ?>"
              class="btn btn-sm btn-danger"
              onclick="return confirm('¿Eliminar este usuario?')"
            >🗑</a>
          </td>
        <?php endif; ?>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
