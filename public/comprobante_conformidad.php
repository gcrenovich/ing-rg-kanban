<?php
require_once __DIR__ . '/../includes/funciones.php';
$id = $_GET['id'] ?? null;
if (!$id) { echo 'ID requerido'; exit; }

$t = find_by_id(leer_json('trabajos.json'), $id);
if (!$t) { echo 'Trabajo no encontrado'; exit; }

$c = find_by_id(leer_json('clientes.json'), $t['cliente_id']);
$disp = find_by_id(leer_json('dispositivos.json'), $t['dispositivo_id']);

$fecha_hora = date('d/m/Y H:i');
$leyenda_path = __DIR__ . '/../includes/leyenda_conformidad.txt';
$leyenda = file_exists($leyenda_path)
    ? trim(file_get_contents($leyenda_path))
    : 'El cliente deja constancia de su conformidad con el ingreso del equipo para diagnóstico y/o reparación.';
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Comprobante de Conformidad <?=htmlspecialchars($t['comprobante_id'])?></title>
<style>
body { font-family: Arial, sans-serif; background: #f4f6f8; padding: 20px; }
.comprobante { background: #fff; max-width: 800px; margin: auto; padding: 30px; border-radius: 10px; }
.header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #2563eb; padding-bottom: 10px; }
.logo img { max-height: 80px; border-radius: 6px; }
h1 { color: #1e3a8a; margin: 0; }
table { width: 100%; border-collapse: collapse; margin-top: 10px; }
th, td { border: 1px solid #ccc; padding: 8px; }
th { background: #e0e7ff; text-align: left; }
.firma { margin-top: 40px; display: flex; justify-content: space-between; }
.firma div { text-align: center; width: 45%; border-top: 1px solid #000; padding-top: 5px; }
.print-btn { background: #2563eb; color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; }
@media print { .print-btn { display: none; } body { background: #fff; } }
.leyenda { font-size: 12px; color: #555; margin-top: 30px; border-top: 1px dashed #aaa; padding-top: 10px; text-align: center; }
</style>
</head>
<body>
<div class="comprobante">
  <div class="header">
    <div class="logo"><img src="assets/img/logo.png" alt="Logo"></div>
    <div>
      <h1>Comprobante de Conformidad</h1>
      <strong>N°:</strong> <?=htmlspecialchars($t['comprobante_id'])?><br>
      <strong>Fecha:</strong> <?=$fecha_hora?>
    </div>
    <div><button class="print-btn" onclick="window.print()">🖨 Imprimir</button></div>
  </div>

  <h3>Cliente</h3>
  <table>
    <tr><th>Nombre</th><td><?=htmlspecialchars($c['nombre'] ?? '')?></td></tr>
    <tr><th>DNI / Teléfono</th><td><?=htmlspecialchars($c['telefono'] ?? '')?></td></tr>
  </table>

  <h3>Equipo recibido</h3>
  <table>
    <tr><th>Tipo</th><td><?=htmlspecialchars($disp['tipo'] ?? '')?></td></tr>
    <tr><th>Marca</th><td><?=htmlspecialchars($t['marca'] ?? '')?></td></tr>
    <tr><th>Modelo</th><td><?=htmlspecialchars($t['modelo'] ?? '')?></td></tr>
    <tr><th>Observaciones</th><td><?=nl2br(htmlspecialchars($t['problema'] ?? ''))?></td></tr>
  </table>

  <div class="firma">
    <div>Firma del Cliente</div>
    <div>Firma del Técnico</div>
  </div>

  <div class="leyenda">
    <?=nl2br(htmlspecialchars($leyenda))?>
  </div>
</div>
</body>
</html>
