<?php
include 'includes/navbar.php';
require 'db.php';
session_start();
if ($_SESSION['rol'] !== 'admin') {
  header('Location: dashboard.php');
  exit;
}

// Alta de sector
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
  $nombre = trim($_POST['nombre']);
  if ($nombre !== '') {
    $stmt = $conn->prepare("INSERT IGNORE INTO sectores (nombre) VALUES (?)");
    $stmt->execute([$nombre]);
  }
}

// Baja de sector
if (isset($_GET['eliminar'])) {
  $stmt = $conn->prepare("DELETE FROM sectores WHERE id = ?");
  $stmt->execute([$_GET['eliminar']]);
}

// Obtener todos los sectores
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

  <!-- Formulario de alta -->
  <form method="POST" class="row g-3 mb-4">
    <div class="col-md-6">
      <input type="text" name="nombre" class="form-control" placeholder="Nombre del sector" required>
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-success">Agregar</button>
    </div>
  </form>

  <!-- Tabla de sectores -->
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
          <td><?= htmlspecialchars($s['nombre']) ?></td>
          <td>
            <a href="?eliminar=<?= $s['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este sector?')">🗑</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>