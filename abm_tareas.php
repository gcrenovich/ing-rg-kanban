// abm_tareas.php
<?php
include 'includes/header.php';

require 'db.php';
session_start();
if (!isset($_SESSION['usuario_id'])) exit('Acceso denegado');
$sector_id = $_SESSION['sector_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("INSERT INTO tareas (sector_id, titulo, descripcion, urgencia, fecha_inicio, fecha_fin) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $sector_id,
        $_POST['titulo'],
        $_POST['descripcion'],
        $_POST['urgencia'],
        $_POST['fecha_inicio'] ?: null,
        $_POST['fecha_fin'] ?: null
    ]);
}
$tareas = $conn->prepare("SELECT * FROM tareas WHERE sector_id = ?");
$tareas->execute([$sector_id]);
$tareas = $tareas->fetchAll();
?>
<form method="post">
    <input name="titulo" placeholder="Título" required>
    <textarea name="descripcion" placeholder="Descripción"></textarea>
    <select name="urgencia">
        <option value="baja">Baja</option>
        <option value="media">Media</option>
        <option value="alta">Alta</option>
    </select>
    <input name="fecha_inicio" type="date">
    <input name="fecha_fin" type="date">
    <button type="submit">Crear Tarea</button>
</form>
<ul>
<?php foreach ($tareas as $t) echo "<li>{$t['titulo']} ({$t['urgencia']})</li>"; ?>
</ul>
