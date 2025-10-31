<?php
// abm_tareas.php
session_start();
require_once __DIR__ . '/includes/json_db.php';
if (empty($_SESSION['usuario_id'])) { header('Location: login.php'); exit; }

$reparaciones = read_json('reparaciones.json');
$equipos = read_json('equipos.json');

function equipoById($equipos, $id) {
    foreach ($equipos as $e) if ((string)$e['id'] === (string)$id) return $e;
    return null;
}

$estados = ['En revisión','En proceso','Reparado','Entregado'];
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8"><title>Kanban - Reparaciones</title>
<style>
body{font-family:Arial;background:#f5f5f5;margin:0}
.header{background:#1d3557;color:#fff;padding:10px}
.container{display:flex;gap:10px;padding:12px;overflow-x:auto}
.col{flex:1;min-width:260px;background:#e8eaf0;border-radius:8px;padding:10px;}
.col h3{text-align:center;margin:0 0 8px 0}
.card{background:#fff;border-radius:6px;padding:8px;margin:8px 0;box-shadow:0 1px 3px rgba(0,0,0,0.12);cursor:grab}
.card.dragging{opacity:0.5}
.topbar{display:flex;gap:8px;align-items:center}
.btn{display:inline-block;padding:6px 10px;background:#2b7a78;color:#fff;border-radius:4px;text-decoration:none}
</style>
</head>
<body>
<div class="header">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <div><strong>Kanban - Reparaciones</strong><br><small>Usuario: <?=htmlspecialchars($_SESSION['usuario_nombre'])?></small></div>
    <div class="topbar">
      <a class="btn" href="alta_dispositivo.php">+ Nuevo ingreso</a>
      <a class="btn" href="logout.php" style="background:#e63946">Salir</a>
    </div>
  </div>
</div>

<div class="container">
<?php foreach ($estados as $estado): ?>
  <div class="col" data-estado="<?=htmlspecialchars($estado)?>">
    <h3><?=htmlspecialchars($estado)?></h3>
    <?php foreach ($reparaciones as $r):
       if ($r['estado'] !== $estado) continue;
       $eq = equipoById($equipos, $r['equipo_id']);
    ?>
      <div class="card" draggable="true" data-id="<?=htmlspecialchars($r['id'])?>">
        <strong><?=htmlspecialchars($r['titulo'] ?? (($eq['marca'] ?? '') . ' ' . ($eq['modelo'] ?? '')))?></strong><br>
        <small><?=htmlspecialchars(($eq['marca'] ?? '') . ' ' . ($eq['modelo'] ?? ''))?></small><br>
        <small><?=htmlspecialchars($eq['nro_serie'] ?? '')?></small><br>
        <a href="detalles.php?id=<?=htmlspecialchars($r['id'])?>">Detalles</a>
      </div>
    <?php endforeach; ?>
  </div>
<?php endforeach; ?>
</div>

<script>
let dragging = null;
document.querySelectorAll('.card').forEach(c=>{
  c.addEventListener('dragstart', ()=>{ dragging=c; c.classList.add('dragging'); });
  c.addEventListener('dragend', ()=>{ c.classList.remove('dragging'); dragging=null; });
});

document.querySelectorAll('.col').forEach(col=>{
  col.addEventListener('dragover', e=>{ e.preventDefault(); });
  col.addEventListener('drop', async e=>{
    e.preventDefault();
    if (!dragging) return;
    col.appendChild(dragging);
    const id = dragging.dataset.id;
    const estado = col.dataset.estado;
    const fd = new FormData();
    fd.append('id', id);
    fd.append('estado', estado);
    const res = await fetch('actualizar_estado.php', { method:'POST', body: fd });
    const j = await res.json();
    if (!j.ok) alert('Error al actualizar estado');
  });
});
</script>
</body>
</html>
