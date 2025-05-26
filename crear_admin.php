<?php
require 'db.php';

$nombre = "Admin Principal";
$email = "admin@kanban.local";
$clave_plana = "admin123";
$clave_hash = password_hash($clave_plana, PASSWORD_DEFAULT);
$sector = "Administración";

try {
    // Asegura que el sector exista
    $conn->prepare("INSERT IGNORE INTO sectores (nombre) VALUES (?)")->execute([$sector]);

    // Obtener ID del sector
    $stmt = $conn->prepare("SELECT id FROM sectores WHERE nombre = ?");
    $stmt->execute([$sector]);
    $sector_id = $stmt->fetchColumn();

    // Eliminar usuario anterior (opcional)
    $conn->prepare("DELETE FROM usuarios WHERE email = ?")->execute([$email]);

    // Insertar nuevo usuario admin
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, clave, sector_id, rol) VALUES (?, ?, ?, ?, 'admin')");
    $stmt->execute([$nombre, $email, $clave_hash, $sector_id]);

    echo "<p style='color:green;'>✅ Usuario admin creado con éxito.</p>";
    echo "<p>Email: $email</p>";
    echo "<p>Contraseña: $clave_plana</p>";
} catch (PDOException $e) {
    echo "<p style='color:red;'>Error al insertar: " . $e->getMessage() . "</p>";
}
?>
