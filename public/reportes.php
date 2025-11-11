<?php
// ================================
//   CARGA DE DATOS
// ================================
$trabajos = json_decode(file_get_contents(__DIR__ . "/../data/trabajos.json"), true);

// ================================
//   CAPTURA DE FILTROS
// ================================
$fecha_desde = $_GET['desde'] ?? date("Y-m-01");
$fecha_hasta = $_GET['hasta'] ?? date("Y-m-d");

// ================================
//   ESTADOS POSIBLES
// ================================
$estados_posibles = ['Pendiente','En proceso','Finalizado','Entregado','Cancelado','Guardado'];

// Normalización para evitar variaciones de texto
$map_estados = [
    'pendiente'  => 'Pendiente',
    'en proceso' => 'En proceso',
    'en_proceso' => 'En proceso',
    'proceso'    => 'En proceso',
    'finalizado' => 'Finalizado',
    'entregado'  => 'Entregado',
    'cancelado'  => 'Cancelado',
    'guardado'   => 'Guardado'
];

// ================================
//   CONTADORES
// ================================
$conteo_estados_mes = [];
$conteo_general_estados = array_fill_keys($estados_posibles, 0);

$ingresos_por_mes = [];
$entregas_por_mes = [];

// ================================
//   RECORREMOS LOS TRABAJOS
// ================================
foreach ($trabajos as $t) {

    // FILTRO POR FECHA DE INGRESO
    $fIngreso = $t['fecha_ingreso'];
    if ($fIngreso < $fecha_desde || $fIngreso > $fecha_hasta) continue;

    // Mes ejemplo: 2025-11
    $mes = date("Y-m", strtotime($fIngreso));

    // Normalizamos estado
    $estado_raw = strtolower(str_replace('_',' ',$t["estado"]));
    $estado = $map_estados[$estado_raw] ?? "Pendiente";

    // Conteo general actual
    $conteo_general_estados[$estado]++;

    // Inicializa mes si no existe
    if (!isset($conteo_estados_mes[$mes])) {
        $conteo_estados_mes[$mes] = array_fill_keys($estados_posibles, 0);
        $ingresos_por_mes[$mes] = 0;
        $entregas_por_mes[$mes] = 0;
    }

    // SUMA AL MES
    $conteo_estados_mes[$mes][$estado]++;

    // Contador de ingresos
    $ingresos_por_mes[$mes]++;

    // Contador de entregas en ese mes
    if ($estado === "Entregado" && !empty($t["fecha_entrega"])) {
        $mesEntrega = date("Y-m", strtotime($t["fecha_entrega"]));
        if (!isset($entregas_por_mes[$mesEntrega])) {
            $entregas_por_mes[$mesEntrega] = 0;
        }
        $entregas_por_mes[$mesEntrega]++;
    }
}

ksort($conteo_estados_mes);
ksort($ingresos_por_mes);
ksort($entregas_por_mes);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Reporte</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .box {
            padding:10px;
            background:#f5f5f5;
            border-radius:8px;
            margin-bottom:10px;
            width:300px;
        }
    </style>
</head>
<body>

<h2>📊 Reporte de Movimientos</h2>

<!-- ✅ BOTÓN IMPRIMIR -->
<button onclick="window.print()" style="padding:8px 16px;background:#16a34a;color:#fff;border:none;border-radius:4px;">
    🖨 Imprimir reporte
</button>

<hr>

<!-- ✅ ESTADOS EN TIEMPO REAL -->
<h3>📌 Estado actual del taller</h3>

<div style="display:flex; gap:20px; flex-wrap:wrap;">
<?php foreach ($conteo_general_estados as $estado => $cant): ?>
    <div class="box">
        <strong><?= $estado ?>:</strong> <?= $cant ?>
    </div>
<?php endforeach; ?>
</div>

<hr>

<!-- ✅ GRAFICO MES: INGRESADOS Y ENTREGADOS -->
<h3>📈 Ingresos vs Entregas por mes</h3>

<canvas id="graficoMovimientos" height="110"></canvas>

<script>
const meses = <?= json_encode(array_keys($ingresos_por_mes)); ?>;
const ingresos = <?= json_encode(array_values($ingresos_por_mes)); ?>;
const entregas = <?= json_encode(array_values($entregas_por_mes)); ?>;

new Chart(document.getElementById("graficoMovimientos"), {
    type: "bar",
    data: {
        labels: meses,
        datasets: [
            {
                label: "Ingresados",
                data: ingresos,
                borderWidth: 2
            },
            {
                label: "Entregados",
                data: entregas,
                borderWidth: 2
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>

</body>
</html>
