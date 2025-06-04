<?php
session_start();
require 'db.php';
include 'includes/navbar.php';

// Solo admin puede acceder
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
  header('Location: dashboard.php');
  exit;
}

// 1) ALTA DE SECTOR (cuando POST trae 'nombre' y NO trae 'editar_id')
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre']) && !isset($_POST['editar_id'])) {
  $nombre = trim($_POST['nombre']);
  if ($nombre !== '') {
    // INSERT IGNORE evita duplicados si el nombre ya existe
    $stmt = $conn->prepare("INSERT IGNORE INTO sectores (nombre) VALUES (?)");
    $stmt->execute([$nombre]);
  }
}

// 2) EDICIÓN DE SECTOR (cuando POST trae 'editar_id')
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_id'])) {
  $editar_id = $_POST['editar_id'];
  $nuevo_nombre = trim($_POST['nombre']);
  if ($nuevo_nombre !== '') {
    $stmt = $conn->prepare("UPDATE sectores SET nombre = ? WHERE id = ?");
    $stmt->execute([$nuevo_nombre, $editar_id]);
  }
}

// 3) BAJA DE SECTOR (cuando llega ?eliminar=ID)
if (isset($_GET['eliminar'])) {
  $stmt = $conn->prepare("DELETE FROM sectores WHERE id = ?");
  $stmt->execute([$_GET['eliminar']]);
}

// 4) Obtener todos los sectores para mostrar en tabla
$sectores = $conn->query("SELECT * FROM sectores ORDER BY nombre")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>ABM Sectores</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

  <h2>Gestión de Sectores</h2>

  <!-- 1) Formulario de Alta de Sector -->
  <form method="POST" class="row g-3 mb-4">
    <div class="col-md-6">
      <input type="text" name="nombre" class="form-control" placeholder="Nombre del sector" required>
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-success">Agregar</button>
    </div>
  </form>

  <!-- 2) Tabla de Sectores con edición in‐line y baja -->
  <table class="table table-bordered table-hover">
    <thead class="table-dark">
      <tr>
        <th>#</th>
        <th>Nombre del sector</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($sectores as $s): ?>
        <tr>
          <td><?= $s['id'] ?></td>
          <td>
            <!-- Si no estamos editando este registro, muestro el nombre normal.
                 Si queremos editar, reemplazo por un formulario de edición. -->
            <?php if (isset($_GET['editar']) && $_GET['editar'] == $s['id']): ?>
              <form method="POST" class="d-flex">
                <input type="hidden" name="editar_id" value="<?= $s['id'] ?>">
                <input
                  type="text"
                  name="nombre"
                  class="form-control me-2"
                  value="<?= htmlspecialchars($s['nombre']) ?>"
                  required
                >
                <button type="submit" class="btn btn-primary btn-sm">💾 Guardar</button>
              </form>
            <?php else: ?>
              <?= htmlspecialchars($s['nombre']) ?>
            <?php endif; ?>
          </td>
          <td>
            <?php if (!(isset($_GET['editar']) && $_GET['editar'] == $s['id'])): ?>
              <!-- Botón para iniciar edición cambiando la URL a ?editar=ID -->
              <a
                href="?editar=<?= $s['id'] ?>"
                class="btn btn-sm btn-warning me-1"
              >✏️</a>
            <?php endif; ?>

            <!-- Botón de eliminación (si no estamos en modo edición para este ID) -->
            <?php if (!isset($_GET['editar']) || $_GET['editar'] != $s['id']): ?>
              <a
                href="?eliminar=<?= $s['id'] ?>"
                class="btn btn-sm btn-danger"
                onclick="return confirm('¿Eliminar este sector?')"
              >🗑</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- Script de Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
