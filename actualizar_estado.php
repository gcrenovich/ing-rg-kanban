// actualizar_estado.php
<?php
// actualizar_estado.php   
require 'db.php';
$id = $_POST['id'];
$estado = $_POST['estado'];

$stmt = $conn->prepare("UPDATE tareas SET estado = ? WHERE id = ?");
$stmt->execute([$estado, $id]);

echo "ok";

?>