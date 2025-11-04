<?php
header('Content-Type: application/json');
$trabajos = json_decode(file_get_contents(__DIR__ . '/../data/trabajos.json'), true);

$desde = $_GET['desde'] ?? '1900-01-01';
$hasta = $_GET['hasta'] ?? '2100-12-31';
$tipo  = $_GET['tipo'] ?? '';

$desde_t = strtotime($desde);
$hasta_t = strtotime($hasta);

$filtrados = array_filter($trabajos, function($t) use ($desde_t, $hasta_t, $tipo) {
    $f = strtotime($t['fecha_ingreso']);
    $ok = ($f >= $desde_t && $f <= $hasta_t);
    if ($tipo && $t['tipo'] !== $tipo) $ok = false;
    return $ok;
});

$resumen = [];
foreach ($filtrados as $t) {
    $fecha = date('Y-m-d', strtotime($t['fecha_ingreso']));
    $resumen[$fecha] = ($resumen[$fecha] ?? 0) + 1;
}

echo json_encode([
    'filtrados' => array_values($filtrados),
    'resumen' => $resumen
], JSON_PRETTY_PRINT);
