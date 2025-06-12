<?php
session_start();
require 'db.php';

if (!isset($_SESSION['usuario_id'])) {
  http_response_code(403);
  exit("No autorizado");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
  $tarea_id = $_POST['id'];

  $stmt = $conn->prepare("UPDATE tareas SET estado = 'guardado' WHERE id = ?");
  $stmt->execute([$tarea_id]);

  echo "Tarea guardada correctamente.";
} else {
  echo "Solicitud inválida.";
}
