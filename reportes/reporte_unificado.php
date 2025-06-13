<?php
require '../db.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
  http_response_code(403);
  exit("Acceso no autorizado");
}

$sector_id = $_SESSION['sector_id'];

// Captura de filtros
$usuario_id = $_GET['usuario_id'] ?? '';
$estado = $_GET['estado'] ?? '';

// Armado de filtros dinámicos
$filtros = "WHERE t.sector_id = ?";
$params = [$sector_id];

if (!empty($usuario_id)) {
  $filtros .= " AND t.usuario_id = ?";
  $params[] = $usuario_id;
}

if (!empty($estado)) {
  $filtros .= " AND t.estado = ?";
  $params[] = $estado;
}

$sql = "
  SELECT t.*, u.nombre AS usuario_nombre, 
         DATE_FORMAT(t.fecha_creacion, '%Y-%m') AS mes_anio
  FROM tareas t
  LEFT JOIN usuarios u ON t.usuario_id = u.id
  $filtros
  ORDER BY mes_anio DESC, t.estado, t.fecha_creacion DESC
";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$tareas = $stmt->fetchAll();

if (count($tareas) === 0) {
  echo "<p class='alert alert-warning'>No se encontraron tareas según los filtros.</p>";
  exit;
}

// Agrupamos por mes y estado:
$agrupado = [];
foreach ($tareas as $fila) {
  $mes = $fila['mes_anio'];
  $estado_actual = $fila['estado'];
  $agrupado[$mes][$estado_actual][] = $fila;
}

// Render del reporte:
foreach ($agrupado as $mes => $estados) {
  echo "<h4 class='mt-4'>Mes: $mes</h4>";
  foreach ($estados as $estado_label => $grupo_tareas) {
    echo "<h5>Estado: " . ucfirst($estado_label) . " (" . count($grupo_tareas) . " tareas)</h5>";
    echo "<ul>";
    foreach ($grupo_tareas as $t) {
      echo "<li><strong>" . htmlspecialchars($t['titulo']) . "</strong> - " . htmlspecialchars($t['usuario_nombre'] ?? 'Sin usuario') . "</li>";
    }
    echo "</ul>";
  }
}
?>
