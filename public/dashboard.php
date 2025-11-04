<?php
// public/dashboard.php
require_once __DIR__ . '/../includes/funciones.php';
require_login();

$trabajos = leer_json('trabajos.json');
$clientes = leer_json('clientes.json');
$dispositivos = leer_json('dispositivos.json');

function clienteById($clientes, $id) {
    foreach ($clientes as $c) if ((string)$c['id'] === (string)$id) return $c;
    return null;
}
function dispositivoById($disp, $id) {
    foreach ($disp as $d) if ((string)$d['id'] === (string)$id) return $d;
    return null;
}

$estados = ['Pendiente','En proceso','Finalizado','Entregado','Cancelado'];
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Dashboard - Kanban</title>
<link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
<header class="topbar">
  <div class="logo">Taller - Kanban</div>
  <div class="top-actions">
    <a class="btn" href="registrar.php">+ Nuevo ingreso</a>
    <a class="btn" href="reportes.php">Reportes</a>
    <a class="btn" href="logout.php" style="background:#e63946">Salir</a>
  </div>
</header>

<main class="kanban-container">
  <?php foreach ($estados as $estado): ?>
    <section class="kanban-column" data-estado="<?=htmlspecialchars($estado)?>">
      <h3><?=htmlspecialchars($estado)?></h3>
      <div class="kanban-cards">
      <?php foreach ($trabajos as $t):
         if ($t['estado'] !== $estado) continue;
         $cli = clienteById($clientes, $t['cliente_id']);
         $dis = dispositivoById($dispositivos, $t['dispositivo_id']);
      ?>
        <article class="kanban-card" draggable="true" data-id="<?=htmlspecialchars($t['id'])?>">
          <strong><?=htmlspecialchars(($cli['nombre'] ?? 'Cliente #'.$t['cliente_id']))?></strong>
          <div class="small"><?=htmlspecialchars(($dis['tipo'] ?? '') . ' - ' . ($t['marca'] ?? '') . ' ' . ($t['modelo'] ?? ''))?></div>
          <div class="meta"><?=htmlspecialchars(substr($t['problema'],0,80))?></div>
          <div class="card-actions">
            <a class="link" href="detalles.php?id=<?=htmlspecialchars($t['id'])?>">Ver</a>
          </div>
        </article>
      <?php endforeach; ?>
      </div>
    </section>
  <?php endforeach; ?>
</main>

<script src="../assets/js/kanban.js"></script>
</body>
</html>
