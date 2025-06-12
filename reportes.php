<?php
require 'db.php';
session_start();
include 'includes/header.php';
include 'includes/navbar.php';

// Traemos solo los usuarios del sector actual
$sector_id = $_SESSION['sector_id'];
$stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE sector_id = ? ORDER BY nombre");
$stmt->execute([$sector_id]);
$usuarios = $stmt->fetchAll();
?>

<h2 class="text-center mb-4">Reportes de Tareas</h2>

<div class="container">
  <form id="reporte-form" class="mb-4">

    <div class="row g-3">
      <div class="col-md-4">
        <label for="usuario-select">Filtrar por Usuario</label>
        <select id="usuario-select" class="form-select">
          <option value="">Todos</option>
          <?php foreach ($usuarios as $u): ?>
            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-4">
        <label for="estado-select">Filtrar por Estado</label>
        <select id="estado-select" class="form-select">
          <option value="">Todos</option>
          <option value="pendiente">Pendiente</option>
          <option value="proceso">En Proceso</option>
          <option value="realizado">Realizado</option>
          <option value="guardado">Guardado</option>
        </select>
      </div>

      <div class="col-md-4 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100">Generar Reporte</button>
      </div>
    </div>

  </form>

  <div id="reporte-resultado" class="mt-4"></div>
</div>

<script>
document.getElementById('reporte-form').addEventListener('submit', function(e) {
  e.preventDefault();
  const usuarioId = document.getElementById('usuario-select').value;
  const estado = document.getElementById('estado-select').value;

  fetch(`reportes/reporte_unificado.php?usuario_id=${usuarioId}&estado=${estado}`)
    .then(res => res.text())
    .then(html => {
      document.getElementById('reporte-resultado').innerHTML = html;
    });
});
</script>

<?php include 'includes/footer.php'; ?>
