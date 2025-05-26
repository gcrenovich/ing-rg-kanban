<?php
require 'db.php';
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header('Location: login.php');
  exit;
}
$sector_id = $_SESSION['sector_id'];
$stmt = $conn->prepare("SELECT * FROM tareas WHERE sector_id = ?");
$stmt->execute([$sector_id]);
$tareas = $stmt->fetchAll();
include 'includes/header.php';
?>

<h2 style="text-align:center;">Tablero Kanban</h2>
<div class="kanban">
  <div class="columna" data-estado="pendiente">
    <h3>Pendiente</h3>
    <div class="tareas">
      <?php foreach ($tareas as $t): if ($t['estado'] === 'pendiente'): ?>
        <div class='tarea' draggable='true' data-id='<?= $t['id'] ?>' data-urgencia='<?= $t['urgencia'] ?>'><?= htmlspecialchars($t['titulo']) ?></div>
      <?php endif; endforeach; ?>
    </div>
  </div>
  <div class="columna" data-estado="proceso">
    <h3>En Proceso</h3>
    <div class="tareas">
      <?php foreach ($tareas as $t): if ($t['estado'] === 'proceso'): ?>
        <div class='tarea' draggable='true' data-id='<?= $t['id'] ?>' data-urgencia='<?= $t['urgencia'] ?>'><?= htmlspecialchars($t['titulo']) ?></div>
      <?php endif; endforeach; ?>
    </div>
  </div>
  <div class="columna" data-estado="realizado">
    <h3>Realizado</h3>
    <div class="tareas">
      <?php foreach ($tareas as $t): if ($t['estado'] === 'realizado'): ?>
        <div class='tarea realizada' draggable='false' data-id='<?= $t['id'] ?>' data-urgencia='<?= $t['urgencia'] ?>'><?= htmlspecialchars($t['titulo']) ?></div>
      <?php endif; endforeach; ?>
    </div>
  </div>
</div>

<script src="js/kanban.js"></script>
<?php include 'includes/footer.php'; ?>
