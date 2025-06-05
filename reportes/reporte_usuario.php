<?php
require '../db.php';
if (!isset($_GET['usuario_id'])) {
  echo "ID de usuario no recibido";
  exit;
}

$usuario_id = $_GET['usuario_id'];

$stmt = $conn->prepare("SELECT titulo, estado, urgencia, fecha_inicio, fecha_fin FROM tareas WHERE usuario_id = ?");
$stmt->execute([$usuario_id]);
$tareas = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$tareas) {
  echo "<p>No hay tareas asignadas a este usuario.</p>";
  exit;
}

echo "<ul class='list-group'>";
foreach ($tareas as $t) {
  echo "<li class='list-group-item'>";
  echo "<strong>" . htmlspecialchars($t['titulo']) . "</strong> — ";
  echo ucfirst($t['estado']) . " | Urgencia: " . ucfirst($t['urgencia']);
  echo "<br>Inicio: " . ($t['fecha_inicio'] ?? '-') . " | Fin: " . ($t['fecha_fin'] ?? '-');
  echo "</li>";
}
echo "</ul>";
