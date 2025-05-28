<?php
require 'db.php';
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header('Location: login.php');
  exit;
}
$sector_id = $_SESSION['sector_id'];
$stmt = $conn->prepare("SELECT t.*, u.nombre AS usuario_asignado, u.equipo AS equipo_usuario FROM tareas t JOIN usuarios u ON t.usuario_id = u.id WHERE t.sector_id = ?");
$stmt->execute([$sector_id]);
$tareas = $stmt->fetchAll();

include 'includes/header.php';
include 'includes/navbar.php'; // ✅ agregamos el menú aquí
?>

<h2 style="text-align:center;">Tablero Kanban</h2>

<!-- Filtros -->
<div class="container mb-4">
  <div class="row g-3">
    <div class="col-md-4">
      <label>Filtrar por urgencia</label>
      <select id="filtro-urgencia" class="form-select">
        <option value="">Todas</option>
        <option value="alta">Alta</option>
        <option value="media">Media</option>
        <option value="baja">Baja</option>
      </select>
    </div>
    <div class="col-md-4">
      <label>Filtrar por usuario</label>
      <input type="text" id="filtro-usuario" class="form-control" placeholder="Nombre del usuario">
    </div>
    <div class="col-md-4">
      <label>Filtrar por título</label>
      <input type="text" id="filtro-titulo" class="form-control" placeholder="Buscar por título">
    </div>
  </div>
</div>

<div class="kanban">
  <div class="columna columna-pendiente" data-estado="pendiente">
    <h3>Pendiente</h3>
    <div class="tareas">
      <?php foreach ($tareas as $t): if ($t['estado'] === 'pendiente'): ?>
        <div class='tarea urgencia-<?= $t['urgencia'] ?>' draggable='true'
             data-id='<?= $t['id'] ?>'
             data-urgencia='<?= $t['urgencia'] ?>'
             data-usuario='<?= strtolower(htmlspecialchars($t['usuario_asignado'])) ?>'
             data-titulo='<?= strtolower(htmlspecialchars($t['titulo'])) ?>'>
          <strong><?= htmlspecialchars($t['titulo']) ?></strong><br><small><?= htmlspecialchars($t['usuario_asignado']) ?></small>
        </div>
      <?php endif; endforeach; ?>
    </div>
  </div>
  <div class="columna columna-proceso" data-estado="proceso">
    <h3>En Proceso</h3>
    <div class="tareas">
      <?php foreach ($tareas as $t): if ($t['estado'] === 'proceso'): ?>
        <div class='tarea urgencia-<?= $t['urgencia'] ?>' draggable='true'
             data-id='<?= $t['id'] ?>'
             data-urgencia='<?= $t['urgencia'] ?>'
             data-usuario='<?= strtolower(htmlspecialchars($t['usuario_asignado'])) ?>'
             data-titulo='<?= strtolower(htmlspecialchars($t['titulo'])) ?>'>
          <strong><?= htmlspecialchars($t['titulo']) ?></strong><br><small><?= htmlspecialchars($t['usuario_asignado']) ?></small>
        </div>
      <?php endif; endforeach; ?>
    </div>
  </div>
  <div class="columna columna-realizado" data-estado="realizado">
    <h3>Realizado</h3>
    <div class="tareas">
      <?php foreach ($tareas as $t): if ($t['estado'] === 'realizado'): ?>
        <div class='tarea realizada urgencia-<?= $t['urgencia'] ?>' draggable='true'
             data-id='<?= $t['id'] ?>'
             data-urgencia='<?= $t['urgencia'] ?>'
             data-usuario='<?= strtolower(htmlspecialchars($t['usuario_asignado'])) ?>'
             data-titulo='<?= strtolower(htmlspecialchars($t['titulo'])) ?>'>
          <strong><?= htmlspecialchars($t['titulo']) ?></strong><br><small><?= htmlspecialchars($t['usuario_asignado']) ?></small>
        </div>
      <?php endif; endforeach; ?>
    </div>
  </div>
</div>

<script src="js/kanban.js"></script>
<script>
  // Filtrado dinámico
  const filtroUrgencia = document.getElementById('filtro-urgencia');
  const filtroUsuario = document.getElementById('filtro-usuario');
  const filtroTitulo = document.getElementById('filtro-titulo');

  function aplicarFiltros() {
    const urgencia = filtroUrgencia.value;
    const usuario = filtroUsuario.value.toLowerCase();
    const titulo = filtroTitulo.value.toLowerCase();
    
    document.querySelectorAll('.tarea').forEach(tarea => {
      const tareaUrgencia = tarea.dataset.urgencia;
      const tareaUsuario = tarea.dataset.usuario;
      const tareaTitulo = tarea.dataset.titulo;

      const coincideUrgencia = !urgencia || tareaUrgencia === urgencia;
      const coincideUsuario = !usuario || tareaUsuario.includes(usuario);
      const coincideTitulo = !titulo || tareaTitulo.includes(titulo);

      tarea.style.display = (coincideUrgencia && coincideUsuario && coincideTitulo) ? '' : 'none';
    });
  }

  filtroUrgencia.addEventListener('change', aplicarFiltros);
  filtroUsuario.addEventListener('input', aplicarFiltros);
  filtroTitulo.addEventListener('input', aplicarFiltros);
</script>
<?php include 'includes/footer.php'; ?>
