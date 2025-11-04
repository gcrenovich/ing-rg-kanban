<?php
// public/reportes.php
require_once __DIR__ . '/../includes/funciones.php';
require_login();
$trabajos = leer_json('trabajos.json');
$dispositivos = leer_json('dispositivos.json');

function parseDate($d){ return strtotime($d); }

$desde = $_GET['desde'] ?? date('Y-m-01');
$hasta = $_GET['hasta'] ?? date('Y-m-d');
$group = $_GET['group'] ?? 'month'; // day, week, month, year

// filtrar por rango
$filtered = array_filter($trabajos, function($t) use($desde,$hasta){
    $f = strtotime($t['fecha_ingreso']);
    return $f >= strtotime($desde) && $f <= strtotime($hasta . ' 23:59:59');
});

// conteo total y por tipo
$total = count($filtered);
$por_tipo = [];
foreach ($filtered as $t) {
    $tipo = 'Desconocido';
    foreach ($dispositivos as $d) if ((string)$d['id'] === (string)$t['dispositivo_id']) $tipo = $d['tipo'];
    if (!isset($por_tipo[$tipo])) $por_tipo[$tipo]=0;
    $por_tipo[$tipo]++;
}

// preparar series para gráfico: agrupado por fecha (según $group)
$series = [];
foreach ($filtered as $t) {
    $ts = strtotime($t['fecha_ingreso']);
    if ($group === 'day') $key = date('Y-m-d', $ts);
    elseif ($group === 'week') $key = date('o-W', $ts); // ISO week
    elseif ($group === 'year') $key = date('Y', $ts);
    else $key = date('Y-m', $ts); // month
    if (!isset($series[$key])) $series[$key]=0;
    $series[$key]++;
}

// ordenar por key
ksort($series);
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Reportes</title>
<link rel="stylesheet" href="../assets/css/estilos.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<header class="topbar"><div class="logo">Reportes</div><div class="top-actions"><a class="btn" href="index.php">Volver</a></div></header>
<main style="padding:12px;">
<form method="get">
  Desde: <input type="date" name="desde" value="<?=htmlspecialchars($desde)?>">
  Hasta: <input type="date" name="hasta" value="<?=htmlspecialchars($hasta)?>">
  Agrupar por:
  <select name="group">
    <option value="day" <?=($group==='day')?'selected':''?>>Día</option>
    <option value="week" <?=($group==='week')?'selected':''?>>Semana</option>
    <option value="month" <?=($group==='month')?'selected':''?>>Mes</option>
    <option value="year" <?=($group==='year')?'selected':''?>>Año</option>
  </select>
  <button class="btn" type="submit">Generar</button>
</form>

<section style="margin-top:12px;">
  <h3>Resumen</h3>
  <p>Total trabajos en rango: <strong><?= $total ?></strong></p>
  <h4>Por tipo de dispositivo</h4>
  <table>
    <tr><th>Tipo</th><th>Total</th></tr>
    <?php foreach ($por_tipo as $tipo=>$n): ?>
      <tr><td><?=htmlspecialchars($tipo)?></td><td><?= $n ?></td></tr>
    <?php endforeach; ?>
  </table>
</section>

<section style="margin-top:12px;">
  <h4>Serie temporal (<?=htmlspecialchars($group)?>)</h4>
  <canvas id="chart" style="max-width:800px;"></canvas>
  <script>
    const labels = <?=json_encode(array_keys($series))?>;
    const data = <?=json_encode(array_values($series))?>;
    const ctx = document.getElementById('chart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{ label: 'Trabajos', data: data }]
      },
      options: { responsive:true }
    });
  </script>
</section>

</main>
</body>
</html>
