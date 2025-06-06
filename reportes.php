<?php
include 'includes/header.php';
session_start();
require 'db.php';
include 'includes/navbar.php';

$usuarios = $conn->query("SELECT id, nombre FROM usuarios ORDER BY nombre")->fetchAll();
?>

<h2 class="text-center mb-4">Reportes</h2>
<div class="container">
  <div class="row">
    <div class="col-md-6 mb-4">
      <h3>Reporte de Tareas por Usuario</h3>
      <form id="reporte-usuario-form">
        <label for="usuario-select">Seleccionar Usuario</label>
        <select id="usuario-select" class="form-select">
          <option value="">Todos</option>
          <?php foreach ($usuarios as $u): ?>
            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary mt-2">Generar Reporte</button>
      </form>
      <div id="reporte-usuario-result" class="mt-3"></div>
    </div>

    <div class="col-md-6 mb-4">
      <h3>Reporte de Tareas por Estado</h3>
      <form id="reporte-estado-form">
        <label for="estado-select">Seleccionar Estado</label>
        <select id="estado-select" class="form-select">
          <!--<option value="">Todos</option>-->
          <option value="pendiente">Pendiente</option>
          <option value="proceso">En Proceso</option>
          <option value="realizado">Realizado</option>
          <!--<option value="guardado">Guardado</option>-->
        </select>
        <button type="submit" class="btn btn-primary mt-2">Generar Reporte</button>
      </form>
      <div id="reporte-estado-result" class="mt-3"></div>
    </div>
  </div>
</div>

<script>
document.getElementById('reporte-usuario-form').addEventListener('submit', function(e) {
  e.preventDefault();
  const usuarioId = document.getElementById('usuario-select').value;
  fetch('reportes/reporte_usuario.php?usuario_id=' + usuarioId)
    .then(res => res.text())
    .then(html => {
      document.getElementById('reporte-usuario-result').innerHTML = html;
    });
});

document.getElementById('reporte-estado-form').addEventListener('submit', function(e) {
  e.preventDefault();
  const estado = document.getElementById('estado-select').value;
  fetch('reportes/reporte_estado.php?estado=' + estado)
    .then(res => res.text())
    .then(html => {
      document.getElementById('reporte-estado-result').innerHTML = html;
    });
});
</script>

<?php include 'includes/footer.php'; ?>
