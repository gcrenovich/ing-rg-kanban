<?php
session_start();
require 'db.php';

// Solo admin puede ejecutar
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
  http_response_code(403);
  echo "Acceso denegado";
  exit;
}

$ayer = date('Y-m-d', strtotime('-1 day'));
$hoy = date('Y-m-d');

// Buscar tareas diarias del día anterior
$stmt = $conn->prepare("SELECT * FROM tareas WHERE es_diaria = 1 AND fecha_inicio = ?");
$stmt->execute([$ayer]);
$tareas = $stmt->fetchAll();

// Crear nuevas tareas para hoy
foreach ($tareas as $t) {
  $nuevo = $conn->prepare("
    INSERT INTO tareas (titulo, descripcion, urgencia, estado, sector_id, usuario_id, fecha_inicio, es_diaria)
    VALUES (?, ?, ?, 'pendiente', ?, ?, ?, 1)
  ");
  $nuevo->execute([
    $t['titulo'],
    $t['descripcion'],
    $t['urgencia'],
    $t['sector_id'],
    $t['usuario_id'],
    $hoy
  ]);
}

// Eliminar tareas realizadas del día anterior
$conn->prepare("DELETE FROM tareas WHERE estado = 'realizado' AND fecha_inicio < ?")->execute([$hoy]);

echo "Tareas diarias actualizadas.";
?>
