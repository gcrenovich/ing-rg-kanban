<?php
// public/detalles.php
require_once __DIR__ . '/../includes/funciones.php';
require_login();

$id = $_GET['id'] ?? null;
if (!$id) { echo 'ID requerido'; exit; }

$t = find_by_id(leer_json('trabajos.json'), $id);
if (!$t) { echo 'Trabajo no encontrado'; exit; }

$c = find_by_id(leer_json('clientes.json'), $t['cliente_id']);
$disp = find_by_id(leer_json('dispositivos.json'), $t['dispositivo_id']);
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Detalle trabajo #<?=htmlspecialchars($t['id'])?></title>
<link rel="stylesheet" href="../assets/css/estilos.css">
<style>
.btn-accion {
  display:inline-block;
  margin:4px;
  padding:8px 12px;
  border:none;
  border-radius:6px;
  cursor:pointer;
  color:#fff;
  text-decoration:none;
}
.btn-eliminar { background:#e63946; }
.btn-modificar { background:#457b9d; }
.btn-imprimir { background:#1d3557; }
.btn-guardar { background:#2a9d8f; }
.btn-volver { background:#6c757d; color:#fff; }
.section-actions { margin-top:16px; padding-top:10px; border-top:1px solid #ccc; }
</style>
</head>
<body>

<header class="topbar">
  <div class="logo">Detalle del Trabajo</div>
  <div class="top-actions">
    <a class="btn btn-volver" href="dashboard.php">Volver</a>
  </div>
</header>

<main style="padding:12px;">
  <h2>Trabajo <?=htmlspecialchars($t['comprobante_id'] ?? '')?></h2>

  <section>
    <h3>Cliente</h3>
    <p><strong><?=htmlspecialchars($c['nombre'] ?? '')?></strong></p>
    <p><?=htmlspecialchars($c['telefono'] ?? '')?> - <?=htmlspecialchars($c['email'] ?? '')?></p>
  </section>

  <section>
    <h3>Equipo</h3>
    <p><?=htmlspecialchars($disp['tipo'] ?? '')?> — <?=htmlspecialchars($t['marca'].' '.$t['modelo'])?></p>
    <p><strong>Problema:</strong> <?=nl2br(htmlspecialchars($t['problema']))?></p>
    <p><strong>Estado:</strong> <?=htmlspecialchars($t['estado'])?></p>
    <p><strong>Técnico:</strong> <?=htmlspecialchars($t['tecnico'] ?? '')?></p>
    <p><strong>Fecha ingreso:</strong> <?=htmlspecialchars($t['fecha_ingreso'] ?? '')?></p>
  </section>

  <section>
    <h3>Comentarios</h3>
    <div>
      <?php if (!empty($t['comentarios']) && is_array($t['comentarios'])): ?>
        <?php foreach ($t['comentarios'] as $com): ?>
          <div style="padding:8px;border:1px solid #ddd;margin-bottom:6px;border-radius:6px;">
            <small><?=htmlspecialchars($com['fecha'])?> — <?=htmlspecialchars($com['autor'] ?? 'Sistema')?></small>
            <div><?=nl2br(htmlspecialchars($com['texto']))?></div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>Sin comentarios</p>
      <?php endif; ?>
    </div>

    <form id="formComentario" method="post" action="../api/comentarios.php">
      <input type="hidden" name="id" value="<?=htmlspecialchars($t['id'])?>">
      <label>Autor</label><br>
      <input name="autor" value="<?=htmlspecialchars($_SESSION['usuario']['usuario'] ?? '')?>"><br>
      <label>Comentario</label><br>
      <textarea name="texto" required></textarea><br>
      <button class="btn" type="submit">Agregar comentario</button>
    </form>
  </section>

  <section class="section-actions">
    <h3>Acciones</h3>
    <a href="editar_trabajo.php?id=<?=htmlspecialchars($t['id'])?>" class="btn-accion btn-modificar">Modificar</a>
    <a href="comprobante.php?id=<?=htmlspecialchars($t['id'])?>" target="_blank" class="btn-accion btn-imprimir">Reimprimir</a>
    <a href="acciones.php?action=eliminar&id=<?=htmlspecialchars($t['id'])?>" 
       onclick="return confirm('¿Eliminar este trabajo definitivamente?')" 
       class="btn-accion btn-eliminar">Eliminar</a>

    <?php if ($t['estado'] === 'Entregado'): ?>
      <a href="acciones.php?action=guardar&id=<?=htmlspecialchars($t['id'])?>" 
         class="btn-accion btn-guardar">Guardar</a>
    <?php endif; ?>
  </section>

</main>
</body>
</html>
