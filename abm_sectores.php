// abm_sectores.php
<?php
include 'includes/header.php';
require 'db.php';
session_start();
if ($_SESSION['rol'] !== 'admin') exit('Acceso denegado');
if (isset($_POST['nombre'])) {
    $stmt = $conn->prepare("INSERT INTO sectores (nombre) VALUES (?)");
    $stmt->execute([$_POST['nombre']]);
}
$sectores = $conn->query("SELECT * FROM sectores")->fetchAll();
?>
<form method="post">
    <input name="nombre" placeholder="Nombre del sector" required>
    <button type="submit">Agregar</button>
</form>
<ul>
<?php foreach ($sectores as $s) echo "<li>{$s['nombre']}</li>"; ?>
</ul>