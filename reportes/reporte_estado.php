<?php
require '../db.php';
if (!isset($_GET['estado'])) {
  echo "Estado no recibido";
  exit;
}

$estado = $_GET['estado'];

$stmt = $conn->prepare("SELECT t.titulo, u.nombre AS usuario, t.urgencia, t.fecha_inicio, t.fecha_fin 
                        FROM tareas t 
                        JOIN usuarios u ON t.usuario_id = u.id
                        WHERE t.estado = ?");
$stmt->execute([$estado]);
$tareas = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$tareas) {
  echo "<p>No hay tareas con el estado seleccionado.</p>";
  exit;
}

echo "<ul class='list-group'>";
foreach ($tareas as $t) {
  echo "<li class='list-group-item'>";
  echo "<strong>" . htmlspecialchars($t['titulo']) . "</strong> — ";
  echo "Usuario: " . htmlspecialchars($t['usuario']) . " | Urgencia: " . ucfirst($t['urgencia']);
  echo "<br>Inicio: " . ($t['fecha_inicio'] ?? '-') . " | Fin: " . ($t['fecha_fin'] ?? '-');
  echo "</li>";
}
echo "</ul>";
