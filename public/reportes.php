<?php
// public/reportes.php
require_once __DIR__ . '/../includes/funciones.php';
require_login();

$trabajos = leer_json('trabajos.json');
$dispositivos = leer_json('dispositivos.json');

// Parámetros de filtro
$desde = $_GET['desde'] ?? date('Y-m-01');
$hasta = $_GET['hasta'] ?? date('Y-m-d');
$group = $_GET['group'] ?? 'month'; // day, week, month, year

// Filtrar por rango de fechas
$filtered = array_filter($trabajos, function ($t) use ($desde, $hasta) {
    $f = strtotime($t['fecha_ingreso']);
    return $f >= strtotime($desde) && $f <= strtotime($hasta . ' 23:59:59');
});

$total = count($filtered);

// Conteo por tipo de dispositivo
$por_tipo = [];
foreach ($filtered as $t) {
    $tipo = 'Desconocido';
    foreach ($dispositivos as $d) {
        if ((string)$d['id'] === (string)$t['dispositivo_id']) {
            $tipo = $d['tipo'];
            break;
        }
    }
    if (!isset($por_tipo[$tipo])) $por_tipo[$tipo] = 0;
    $por_tipo[$tipo]++;
}

// Agrupar por período y por estado
$series_estados = [];
$estados_posibles = ['Pendiente','En proceso','Finalizado','Entregado','Cancelado','Guardado'];

foreach ($filtered as $t) {
    $ts = strtotime($t['fecha_ingreso']);
    if ($group === 'day') $key = date('Y-m-d', $ts);
    elseif ($group === 'week') $key = date('o-\S\e\m W', $ts);
    elseif ($group === 'year') $key = date('Y', $ts);
    else $key = date('Y-m', $ts);

    if (!isset($series_estados[$key])) {
        $series_estados[$key] = array_fill_keys($estados_posibles, 0);
    }

    $estado = ucfirst(strtolower($t['estado'] ?? 'Pendiente'));
    if (!in_array($estado, $estados_posibles)) $estado = 'Pendiente';
    $series_estados[$key][$estado]++;
}

// Ordenar
ksort($series_estados);

// Preparar datos para Chart.js
$labels = array_keys($series_estados);
$datasets = [];
$colores = [
    'Pendiente'  => '#f59e0b',
    'En proceso' => '#3b82f6',
    'Finalizado' => '#10b981',
    'Entregado'  => '#6366f1',
    'Cancelado'  => '#ef4444',
    'Guardado'   => '#6b7280'
];

foreach ($estados_posibles as $estado) {
    $datasets[] = [
        'label' => $estado,
        'data' => array_map(fn($v) => $v[$estado] ?? 0, $series_estados),
        'borderColor' => $colores[$estado],
        'backgroundColor' => $colores[$estado],
        'tension' => 0.3,
        'fill' => false,
        'borderWidth' => 2
    ];
}

$fecha_generacion = date('d/m/Y H:i');
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Reportes - DIGITEL MOBIL</title>
<link rel="stylesheet" href="../assets/css/estilos.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body { font-family: 'Segoe UI', Arial, sans-serif; background:#f4f6f8; margin:0; color:#333; }
header.topbar {
  background: linear-gradient(90deg, #1d3557, #457b9d);
  color:#fff; display:flex; justify-content:space-between; align-items:center;
  padding:10px 20px; border-bottom:3px solid #2a9d8f;
}
.logo { display:flex; align-items:center; gap:12px; font-size:1.3em; font-weight:bold; text-transform:uppercase; }
.logo img { height:50px; width:auto; border-radius:10px; box-shadow:0 0 8px rgba(0,0,0,0.3); }
header .top-actions a { text-decoration:none; background:#2a9d8f; padding:8px 12px; border-radius:6px; color:#fff; transition:0.2s; }
header .top-actions a:hover { background:#21867a; }
main { max-width:1100px; margin:20px auto; padding:16px; }
.card {
  background:#fff; border-radius:12px; padding:20px;
  box-shadow:0 2px 10px rgba(0,0,0,0.08); margin-bottom:20px;
}
h2, h3 { color:#1e3a8a; margin-bottom:10px; }
table { width:100%; border-collapse:collapse; margin-top:8px; font-size:0.95em; }
th, td { border:1px solid #d1d5db; padding:8px; text-align:left; }
th { background:#e2e8f0; color:#1e293b; }
input, select, button { padding:8px; border-radius:6px; border:1px solid #d1d5db; }
button.btn { background:#2563eb; color:#fff; border:none; cursor:pointer; border-radius:6px; padding:8px 12px; }
.filters { display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-bottom:14px; }
canvas { max-width:100%; height:auto; margin-top:12px; }
.summary { background:#f1f5f9; padding:10px; border-radius:6px; margin-top:10px; }
.footer-legal {
  text-align:center; font-size:0.8em; color:#555; margin-top:30px; border-top:1px dashed #ccc;
  padding-top:10px;
}
</style>
</head>
<body>

<header class="topbar">
    <div class="logo">
  <img src="public/assets/img/logo.png" alt="DIGITEL MOBIL" class="logo-img">
</div>
  <div class="top-actions">
    <a class="btn" href="index.php">Volver</a>
  </div>
</header>

<main>
  <div class="card">
    <h2>Generar Reporte</h2>
    <form method="get" class="filters">
      <label>Desde:</label>
      <input type="date" name="desde" value="<?= htmlspecialchars($desde) ?>">
      <label>Hasta:</label>
      <input type="date" name="hasta" value="<?= htmlspecialchars($hasta) ?>">
      <label>Agrupar por:</label>
      <select name="group">
        <option value="day" <?= $group==='day'?'selected':'' ?>>Día</option>
        <option value="week" <?= $group==='week'?'selected':'' ?>>Semana</option>
        <option value="month" <?= $group==='month'?'selected':'' ?>>Mes</option>
        <option value="year" <?= $group==='year'?'selected':'' ?>>Año</option>
      </select>
      <button class="btn" type="submit">🔍 Generar</button>
    </form>
    <p style="font-size:0.9em; color:#555; margin-top:6px;">
      📅 Reporte generado el <?= $fecha_generacion ?>
    </p>
  </div>

  <div class="card">
    <h3>Resumen general</h3>
    <div class="summary">
      <p><strong>Total de trabajos en el rango:</strong> <?= $total ?></p>
      <p><strong>Período:</strong> <?= htmlspecialchars($desde) ?> → <?= htmlspecialchars($hasta) ?></p>
      <p><strong>Agrupado por:</strong> <?= ucfirst($group) ?></p>
    </div>

    <h4 style="margin-top:18px;">Trabajos por tipo de dispositivo</h4>
    <table>
      <tr><th>Tipo</th><th>Total</th></tr>
      <?php foreach ($por_tipo as $tipo => $n): ?>
        <tr><td><?= htmlspecialchars($tipo) ?></td><td><?= $n ?></td></tr>
      <?php endforeach; ?>
      <?php if (empty($por_tipo)): ?>
        <tr><td colspan="2" style="text-align:center;color:#6b7280;">Sin registros en el rango</td></tr>
      <?php endif; ?>
    </table>
  </div>

  <div class="card">
    <h3>Evolución de estados por <?= $group === 'month' ? 'mes' : ($group === 'week' ? 'semana' : 'período') ?></h3>
    <canvas id="chart_estados"></canvas>
  </div>

  <div class="footer-legal">
    <p><strong>© DIGITEL MOBIL - Servicio Técnico</strong></p>
    <p id="leyenda-legal">
      El presente informe es de carácter informativo. La información aquí contenida puede modificarse sin previo aviso.
    </p>
  </div>
</main>

<script>
const ctx = document.getElementById('chart_estados').getContext('2d');
new Chart(ctx, {
  type: 'line',
  data: {
    labels: <?= json_encode($labels) ?>,
    datasets: <?= json_encode($datasets) ?>
  },
  options: {
    responsive: true,
    plugins: {
      legend: { position: 'bottom' },
      title: { display: false }
    },
    scales: {
      x: { ticks: { color: '#334155' } },
      y: { beginAtZero: true, ticks: { color: '#334155' } }
    }
  }
});
</script>

</body>
</html>
