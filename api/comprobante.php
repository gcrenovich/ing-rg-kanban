<?php
$id = $_GET['id'] ?? null;
if (!$id) { die('ID no especificado'); }

$trabajos = json_decode(file_get_contents(__DIR__ . '/../data/trabajos.json'), true);
$trabajo = null;

foreach ($trabajos as $t) {
    if ($t['id'] == $id) { $trabajo = $t; break; }
}

if (!$trabajo) { die('Trabajo no encontrado'); }

$compID = sprintf("C-%04d", $id);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante <?= $compID ?></title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>
<div class="comprobante">
    <h2>Comprobante de Recepción</h2>
    <p><strong>N°:</strong> <?= $compID ?></p>
    <p><strong>Cliente:</strong> <?= htmlspecialchars($trabajo['cliente']) ?></p>
    <p><strong>Dispositivo:</strong> <?= htmlspecialchars($trabajo['dispositivo']) ?></p>
    <p><strong>Modelo:</strong> <?= htmlspecialchars($trabajo['modelo']) ?></p>
    <p><strong>Falla:</strong> <?= htmlspecialchars($trabajo['falla']) ?></p>
    <p><strong>Fecha de ingreso:</strong> <?= htmlspecialchars($trabajo['fecha_ingreso']) ?></p>
    <hr>
    <p>_____________________________<br>Firma del Cliente</p>
</div>
<script>window.print();</script>
</body>
</html>

