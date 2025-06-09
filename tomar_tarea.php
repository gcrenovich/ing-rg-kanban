<?php
session_start();
require 'db.php';

if (!isset($_SESSION['usuario_id'])) {
  http_response_code(403);
  exit("No autorizado");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
  $tarea_id = $_POST['id'];
  $usuario_id = $_SESSION['usuario_id'];

  // Verificamos que la tarea no esté ya asignada
  $stmt = $conn->prepare("SELECT usuario_id FROM tareas WHERE id = ?");
  $stmt->execute([$tarea_id]);
  $tarea = $stmt->fetch();

  if ($tarea && !$tarea['usuario_id']) {
    $update = $conn->prepare("UPDATE tareas SET usuario_id = ? WHERE id = ?");
    $update->execute([$usuario_id, $tarea_id]);
    echo "Tarea asignada correctamente.";
  } else {
    echo "La tarea ya está asignada.";
  }
} else {
  echo "Solicitud inválida.";
}
