<?php 
session_start();
require 'db.php';

// Solo admin puede entrar
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
  header('Location: dashboard.php');
  exit;
}

// Alta de tarea
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titulo']) && !isset($_POST['editar_id'])) {
  $usuario_id = isset($_POST['usuario_id']) && $_POST['usuario_id'] !== '' ? $_POST['usuario_id'] : null;
  $stmt = $conn->prepare("
    INSERT INTO tareas (titulo, descripcion, urgencia, estado, sector_id, usuario_id, fecha_inicio, fecha_fin, es_diaria)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
  ");
  $stmt->execute([
    $_POST['titulo'],
    $_POST['descripcion'],
    $_POST['urgencia'],
    $_POST['estado'],
    $_POST['sector_id'],
    $usuario_id,
    $_POST['fecha_inicio'] ?: null,
    $_POST['fecha_fin']   ?: null,
    isset($_POST['es_diaria']) ? 1 : 0
  ]);
}

// Baja de tarea
if (isset($_GET['eliminar'])) {
  $stmt = $conn->prepare("DELETE FROM tareas WHERE id = ?");
  $stmt->execute([$_GET['eliminar']]);
}

// Modificación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_id'])) {
  $usuario_id = isset($_POST['usuario_id']) && $_POST['usuario_id'] !== '' ? $_POST['usuario_id'] : null;
  $stmt = $conn->prepare("
    UPDATE tareas SET titulo = ?, descripcion = ?, urgencia = ?, estado = ?, sector_id = ?, usuario_id = ?, fecha_inicio = ?, fecha_fin = ?, es_diaria = ?
    WHERE id = ?
  ");
  $stmt->execute([
    $_POST['titulo'],
    $_POST['descripcion'],
    $_POST['urgencia'],
    $_POST['estado'],
    $_POST['sector_id'],
    $usuario_id,
    $_POST['fecha_inicio'] ?: null,
    $_POST['fecha_fin']   ?: null,
    isset($_POST['es_diaria']) ? 1 : 0,
    $_POST['editar_id']
  ]);
}

// Obtener datos
$tareas = $conn->query("
  SELECT t.*, u.nombre AS usuario, s.nombre AS sector
  FROM tareas t
  LEFT JOIN usuarios u ON t.usuario_id = u.id
  JOIN sectores s ON t.sector_id = s.id
  ORDER BY t.fecha_creacion DESC
")->fetchAll();

$usuarios = $conn->query("SELECT * FROM usuarios ORDER BY nombre")->fetchAll();
$sectores = $conn->query("SELECT * FROM sectores ORDER BY nombre")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>ABM Tareas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

<?php include 'includes/navbar.php'; ?>

<h2>Gestión de Tareas</h2>

<!-- Formulario -->
<form method="POST" class="row g-3 mb-4">
  <div class="col-md-4">
    <input type="text" name="titulo" class="form-control" placeholder="Título" required>
  </div>
  <div class="col-md-4">
    <input type="text" name="descripcion" class="form-control" placeholder="Descripción">
  </div>
  <div class="col-md-2">
    <select name="urgencia" class="form-select" required>
      <option value="baja">Baja</option>
      <option value="media">Media</option>
      <option value="alta">Alta</option>
    </select>
  </div>
  <div class="col-md-2">
    <select name="estado" class="form-select" required>
      <option value="pendiente">Pendiente</option>
      <option value="proceso">En Proceso</option>
      <option value="realizado">Realizado</option>
    </select>
  </div>
  <div class="col-md-3">
    <select name="sector_id" class="form-select" required>
      <option disabled selected>Sector</option>
      <?php foreach ($sectores as $s): ?>
        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nombre']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-3">
    <select name="usuario_id" class="form-select">
      <option value="">Sin asignar</option>
      <?php foreach ($usuarios as $u): ?>
        <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nombre']) ?> (<?= htmlspecialchars($u['equipo']) ?>)</option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-2">
    <input type="date" name="fecha_inicio" class="form-control">
  </div>
  <div class="col-md-2">
    <input type="date" name="fecha_fin" class="form-control">
  </div>
  <div class="col-md-2 form-check mt-2">
    <input class="form-check-input" type="checkbox" name="es_diaria" value="1" id="diaria">
    <label class="form-check-label" for="diaria">¿Tarea diaria?</label>
  </div>
  <div class="col-12">
    <button class="btn btn-success" type="submit">Agregar tarea</button>
  </div>
</form>

<!-- Tabla de tareas -->
<table class="table table-bordered table-hover">
  <thead class="table-dark">
    <tr>
      <th>Título</th><th>Usuario</th><th>Urgencia</th><th>Estado</th><th>Sector</th><th>Inicio</th><th>Fin</th><th>Diaria</th><th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($tareas as $t): ?>
    <tr>
      <td><?= htmlspecialchars($t['titulo']) ?></td>
      <td><?= $t['usuario'] ?: '<em>Sin asignar</em>' ?></td>
      <td><?= ucfirst($t['urgencia']) ?></td>
      <td><?= ucfirst($t['estado']) ?></td>
      <td><?= htmlspecialchars($t['sector']) ?></td>
      <td><?= $t['fecha_inicio'] ?: '-' ?></td>
      <td><?= $t['fecha_fin']   ?: '-' ?></td>
      <td><?= $t['es_diaria'] ? '✅' : '—' ?></td>
      <td>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $t['id'] ?>">✏️</button>
        <a href="?eliminar=<?= $t['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta tarea?')">🗑</a>
      </td>
    </tr>

    <!-- Modal de edición -->
    <div class="modal fade" id="modalEditar<?= $t['id'] ?>" tabindex="-1">
      <div class="modal-dialog">
        <form method="POST" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Editar Tarea</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="editar_id" value="<?= $t['id'] ?>">
            <label>Título</label>
            <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($t['titulo']) ?>" required>
            <label>Descripción</label>
            <input type="text" name="descripcion" class="form-control" value="<?= htmlspecialchars($t['descripcion']) ?>">
            <label>Urgencia</label>
            <select name="urgencia" class="form-select">
              <option value="baja" <?= $t['urgencia'] === 'baja' ? 'selected' : '' ?>>Baja</option>
              <option value="media" <?= $t['urgencia'] === 'media' ? 'selected' : '' ?>>Media</option>
              <option value="alta" <?= $t['urgencia'] === 'alta' ? 'selected' : '' ?>>Alta</option>
            </select>
            <label>Estado</label>
            <select name="estado" class="form-select">
              <option value="pendiente" <?= $t['estado'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
              <option value="proceso" <?= $t['estado'] === 'proceso' ? 'selected' : '' ?>>En Proceso</option>
              <option value="realizado" <?= $t['estado'] === 'realizado' ? 'selected' : '' ?>>Realizado</option>
            </select>
            <label>Usuario asignado</label>
            <select name="usuario_id" class="form-select">
              <option value="">Sin asignar</option>
              <?php foreach ($usuarios as $u): ?>
                <option value="<?= $u['id'] ?>" <?= $u['id'] == $t['usuario_id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($u['nombre']) ?> (<?= htmlspecialchars($u['equipo']) ?>)
                </option>
              <?php endforeach; ?>
            </select>
            <label>Sector</label>
            <select name="sector_id" class="form-select">
              <?php foreach ($sectores as $s): ?>
                <option value="<?= $s['id'] ?>" <?= $s['id'] == $t['sector_id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($s['nombre']) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <label>Fecha inicio</label>
            <input type="date" name="fecha_inicio" class="form-control" value="<?= $t['fecha_inicio'] ?>">
            <label>Fecha fin</label>
            <input type="date" name="fecha_fin" class="form-control" value="<?= $t['fecha_fin'] ?>">
            <div class="form-check mt-2">
              <input class="form-check-input" type="checkbox" name="es_diaria" value="1" id="diaria<?= $t['id'] ?>" <?= $t['es_diaria'] ? 'checked' : '' ?>>
              <label class="form-check-label" for="diaria<?= $t['id'] ?>">¿Tarea diaria?</label>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-primary" type="submit">Guardar</button>
            <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
    <?php endforeach; ?>
  </tbody>
</table>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
