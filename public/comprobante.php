<?php
// public/comprobante.php
require_once __DIR__ . '/../includes/funciones.php';
$id = $_GET['id'] ?? null;
if (!$id) { echo 'ID requerido'; exit;}
$t = find_by_id(leer_json('trabajos.json'), $id);
if (!$t) { echo 'Trabajo no encontrado'; exit;}
$c = find_by_id(leer_json('clientes.json'), $t['cliente_id']);
$disp = find_by_id(leer_json('dispositivos.json'), $t['dispositivo_id']);
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Comprobante <?=htmlspecialchars($t['comprobante_id'])?></title>
<style>
body{font-family:Arial;padding:18px}
.header{display:flex;justify-content:space-between;align-items:center}
h1{margin:0}
.section{margin-top:12px}
table{width:100%;border-collapse:collapse}
td,th{padding:6px;border:1px solid #ccc}
.print-btn{padding:8px 12px;background:#457b9d;color:#fff;border:none;border-radius:4px;cursor:pointer}
</style>
</head>
<body>
<div class="header">
  <div>
    <h1>Comprobante de ingreso</h1>
    <div>Nº: <strong><?=htmlspecialchars($t['comprobante_id'])?></strong></div>
  </div>
  <div>
    <button class="print-btn" onclick="window.print()">Imprimir</button>
  </div>
</div>

<div class="section">
  <h3>Cliente</h3>
  <table>
    <tr><th>Nombre</th><td><?=htmlspecialchars($c['nombre'] ?? '')?></td></tr>
    <tr><th>Teléfono</th><td><?=htmlspecialchars($c['telefono'] ?? '')?></td></tr>
    <tr><th>Email</th><td><?=htmlspecialchars($c['email'] ?? '')?></td></tr>
    <tr><th>Dirección</th><td><?=htmlspecialchars($c['direccion'] ?? '')?></td></tr>
  </table>
</div>

<div class="section">
  <h3>Equipo</h3>
  <table>
    <tr><th>Tipo</th><td><?=htmlspecialchars($disp['tipo'] ?? '')?></td></tr>
    <tr><th>Marca</th><td><?=htmlspecialchars($t['marca'] ?? '')?></td></tr>
    <tr><th>Modelo</th><td><?=htmlspecialchars($t['modelo'] ?? '')?></td></tr>
    <tr><th>Problema</th><td><?=nl2br(htmlspecialchars($t['problema'] ?? ''))?></td></tr>
    <tr><th>Técnico</th><td><?=htmlspecialchars($t['tecnico'] ?? '')?></td></tr>
    <tr><th>Fecha ingreso</th><td><?=htmlspecialchars($t['fecha_ingreso'] ?? '')?></td></tr>
  </table>
</div>

<div class="section">
  <h3>Observaciones</h3>
  <table>
    <tr><td>
      <?php
        if (!empty($t['comentarios']) && is_array($t['comentarios'])) {
          foreach ($t['comentarios'] as $com) {
            echo '<div><small>'.htmlspecialchars($com['fecha']).' - '.htmlspecialchars($com['autor'] ?? '').':</small><div>'.nl2br(htmlspecialchars($com['texto'])).'</div></div><hr>';
          }
        } else {
          echo 'Sin comentarios';
        }
      ?>
    </td></tr>
  </table>
</div>
</body>
</html>
