<?php
require 'db.php';
session_start();

if ($_SESSION['rol'] !== 'admin') {
  header('Location: dashboard.php');
  exit;
}

// Validación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['titulo'], $_POST['usuario_id'])) {
  // Buscar tarea original
  $stmt = $conn->prepare("SELECT * FROM tareas WHERE id = ?");
  $stmt->execute([$_POST['id']]);
  $tarea_original = $stmt->fetch();

  // Actualizar tarea
  $stmt = $conn->prepare("
    UPDATE tareas SET
      titulo = ?,
      descripcion = ?,
      urgencia = ?,
      estado = ?,
      usuario_id = ?,
      fecha_inicio = ?,
      fecha_fin = ?,
      es_diaria = ?
    WHERE id = ?
  ");
  $stmt->execute([
    $_POST['titulo'],
    $_POST['descripcion'],
    $_POST['urgencia'],
    $_POST['estado'],
    $_POST['usuario_id'],
    $_POST['fecha_inicio'],
    $_POST['fecha_fin'],
    isset($_POST['es_diaria']) ? 1 : 0,
    $_POST['id']
  ]);

  // Si la tarea es diaria y fue marcada como realizada → duplicar
  if ($tarea_original && $tarea_original['es_diaria'] && $_POST['estado'] === 'realizado') {
    $stmt = $conn->prepare("
      INSERT INTO tareas (titulo, descripcion, urgencia, estado, sector_id, usuario_id, fecha_inicio, fecha_fin, es_diaria)
      VALUES (?, ?, ?, 'pendiente', ?, ?, ?, ?, 1)
    ");
    $hoy = date('Y-m-d');
    $stmt->execute([
      $_POST['titulo'],
      $_POST['descripcion'],
      $_POST['urgencia'],
      $tarea_original['sector_id'],
      $_POST['usuario_id'],
      $hoy,
      NULL
    ]);
  }
}

header('Location: abm_tareas.php');
exit;
?>
