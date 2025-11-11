<?php
// public/comprobante.php
require_once __DIR__ . '/../includes/funciones.php';

$id = $_GET['id'] ?? null;
if (!$id) { echo 'ID requerido'; exit; }

// CARGO ARCHIVOS JSON CON RUTA CORRECTA
$trabajos = json_decode(file_get_contents(__DIR__ . '/../data/trabajos.json'), true);
$clientes = json_decode(file_get_contents(__DIR__ . '/../data/clientes.json'), true);
$dispositivos = json_decode(file_get_contents(__DIR__ . '/../data/dispositivos.json'), true);

// BUSCO REGISTROS
$t = find_by_id($trabajos, $id);
if (!$t) { echo 'Trabajo no encontrado'; exit; }

$c = find_by_id($clientes, $t['cliente_id']);
$disp = find_by_id($dispositivos, $t['dispositivo_id']);

// Fecha y hora actual
$fecha_hora = date('d/m/Y H:i');

// Leyenda legal editable desde archivo
$leyenda_path = __DIR__ . '/../includes/leyenda.txt';
$leyenda = file_exists($leyenda_path)
    ? trim(file_get_contents($leyenda_path))
    : '*** Este comprobante no tiene valor legal ***';
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Comprobante <?=htmlspecialchars($t['comprobante_id'])?></title>
<style>
body {
  font-family: Arial, sans-serif;
  padding: 20px;
  background: #f4f6f8;
}
.comprobante {
  max-width: 800px;
  margin: auto;
  background: #fff;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 2px solid #2563eb;
  padding-bottom: 10px;
}
.logo img {
  max-height: 80px;
  width: auto;
  border-radius: 8px;
  box-shadow: 0 0 6px rgba(0,0,0,0.2);
}
h1 {
  color: #1e3a8a;
  margin: 0;
}
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
}
td, th {
  padding: 8px;
  border: 1px solid #ccc;
}
th {
  background: #e0e7ff;
  text-align: left;
}
.section {
  margin-top: 20px;
}
.print-btn {
  padding: 10px 15px;
  background: #2563eb;
  color: #fff;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}
.print-btn:hover {
  background: #1e40af;
}
.leyenda {
  font-size: 12px;
  color: #555;
  margin-top: 30px;
  border-top: 1px dashed #aaa;
  padding-top: 10px;
  text-align: center;
}
@media print {
  .print-btn { display: none; }
  body { background: #fff; margin: 0; }
  .comprobante { box-shadow: none; margin: 0; }
  .logo img { max-height: 70px; }
}
</style>
</head>
<body>

<div class="comprobante">

  <div class="header">
    <div class="logo">
      <img src="assets/img/logo.png" alt="Logo DIGITEL MOBIL">
    </div>
    <div>
      <h1>Comprobante de Ingreso</h1>
      <div><strong>N°:</strong> <?=htmlspecialchars($t['comprobante_id'])?></div>
      <div><strong>Fecha:</strong> <?=$fecha_hora?></div>
    </div>
    <div>
      <button class="print-btn" onclick="window.print()">🖨 Imprimir</button>
    </div>
  </div>

  <div class="section">
    <h3>Cliente</h3>
    <table>
      <tr><th>Nombre</th><td><?=htmlspecialchars($c['nombre'] ?? '')?></td></tr>
      <tr><th>DNI</th><td><?=htmlspecialchars($c['dni'] ?? 'No registrado')?></td></tr>
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

  <div class="leyenda">
    <?=nl2br(htmlspecialchars($leyenda))?>
  </div>

</div>

</body>
</html>
