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
?>

<h2 style="text-align:center;">Tablero Kanban</h2>
<div class="kanban">
  <div class="columna columna-pendiente" data-estado="pendiente">
    <h3>Pendiente</h3>
    <div class="tareas">
      <?php foreach ($tareas as $t): if ($t['estado'] === 'pendiente'): ?>
        <div class='tarea urgencia-<?= $t['urgencia'] ?>' draggable='true' data-id='<?= $t['id'] ?>' data-urgencia='<?= $t['urgencia'] ?>' data-equipo='<?= htmlspecialchars($t['equipo_usuario']) ?>'>
          <strong><?= htmlspecialchars($t['titulo']) ?></strong><br><small><?= htmlspecialchars($t['usuario_asignado']) ?></small>
        </div>
      <?php endif; endforeach; ?>
    </div>
  </div>
  <div class="columna columna-proceso" data-estado="proceso">
    <h3>En Proceso</h3>
    <div class="tareas">
      <?php foreach ($tareas as $t): if ($t['estado'] === 'proceso'): ?>
        <div class='tarea urgencia-<?= $t['urgencia'] ?>' draggable='true' data-id='<?= $t['id'] ?>' data-urgencia='<?= $t['urgencia'] ?>' data-equipo='<?= htmlspecialchars($t['equipo_usuario']) ?>'>
          <strong><?= htmlspecialchars($t['titulo']) ?></strong><br><small><?= htmlspecialchars($t['usuario_asignado']) ?></small>
        </div>
      <?php endif; endforeach; ?>
    </div>
  </div>
  <div class="columna columna-realizado" data-estado="realizado">
    <h3>Realizado</h3>
    <div class="tareas">
      <?php foreach ($tareas as $t): if ($t['estado'] === 'realizado'): ?>
        <div class='tarea realizada urgencia-<?= $t['urgencia'] ?>' draggable='false' data-id='<?= $t['id'] ?>' data-urgencia='<?= $t['urgencia'] ?>' data-equipo='<?= htmlspecialchars($t['equipo_usuario']) ?>'>
          <strong><?= htmlspecialchars($t['titulo']) ?></strong><br><small><?= htmlspecialchars($t['usuario_asignado']) ?></small>
        </div>
      <?php endif; endforeach; ?>
    </div>
  </div>
</div>

<script src="js/kanban.js"></script>
<?php include 'includes/footer.php'; ?>
