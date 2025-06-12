<?php
require '../db.php';
session_start();

$sector_id = $_SESSION['sector_id'];
$usuario_id = $_GET['usuario_id'] ?? '';
$estado = $_GET['estado'] ?? '';

// Armamos la query dinámica:
$sql = "
  SELECT 
    t.id, t.titulo, t.estado, t.fecha_creacion,
    MONTH(t.fecha_creacion) AS mes,
    YEAR(t.fecha_creacion) AS anio,
    u.nombre AS usuario
  FROM tareas t
  LEFT JOIN usuarios u ON t.usuario_id = u.id
  WHERE t.sector_id = ?
";

$params = [$sector_id];

// Si filtra por usuario
if ($usuario_id) {
  $sql .= " AND t.usuario_id = ?";
  $params[] = $usuario_id;
}

// Si filtra por estado
if ($estado) {
  $sql .= " AND t.estado = ?";
  $params[] = $estado;
}

$sql .= " ORDER BY anio DESC, mes DESC, t.fecha_creacion DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$tareas = $stmt->fetchAll();

if (empty($tareas)) {
  echo "<div class='alert alert-info'>No se encontraron tareas.</div>";
  exit;
}

// Agrupar por año y mes
$reporte = [];

foreach ($tareas as $t) {
  $key = "{$t['anio']}-{$t['mes']}";
  if (!isset($reporte[$key])) $reporte[$key] = [];
  $reporte[$key][] = $t;
}

// Mostrar reporte agrupado
foreach ($reporte as $periodo => $lista) {
  [$anio, $mes] = explode('-', $periodo);
  $mes_nombre = date('F', mktime(0, 0, 0, $mes, 1));

  echo "<h4 class='mt-4'>$mes_nombre $anio</h4>";
  echo "<table class='table table-sm table-bordered'>";
  echo "<thead><tr><th>Título</th><th>Estado</th><th>Usuario</th><th>Fecha Creación</th></tr></thead><tbody>";

  foreach ($lista as $t) {
    echo "<tr>";
    echo "<td>".htmlspecialchars($t['titulo'])."</td>";
    echo "<td>".ucfirst($t['estado'])."</td>";
    echo "<td>".($t['usuario'] ?: 'No asignado')."</td>";
    echo "<td>".$t['fecha_creacion']."</td>";
    echo "</tr>";
  }

  echo "</tbody></table>";
}
?>
