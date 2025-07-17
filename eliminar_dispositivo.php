<?php
session_start();
include 'db.php';

// Verificación de acceso: solo usuarios logueados
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$rol = $_SESSION['rol'];
$sector_usuario = $_SESSION['sector_id']; // Sector en ID

$id_dispositivo = $_GET['id'] ?? null;

if (!$id_dispositivo) {
    echo "ID de dispositivo no especificado.";
    exit;
}

// Verificar dispositivo y permisos
$sql = "SELECT * FROM inventario_dispositivos WHERE id = $id_dispositivo";
$result = mysqli_query($conexion, $sql);
$dispositivo = mysqli_fetch_assoc($result);

if (!$dispositivo) {
    echo "Dispositivo no encontrado.";
    exit;
}

// Control de acceso por sector_id
if ($rol != 'admin' && $dispositivo['sector_id'] != $sector_usuario) {
    echo "Acceso denegado.";
    exit;
}

// Eliminar dispositivo (los componentes se eliminan por ON DELETE CASCADE)
$sql_delete = "DELETE FROM inventario_dispositivos WHERE id = $id_dispositivo";

if (mysqli_query($conexion, $sql_delete)) {
    header('Location: inventario.php');
    exit;
} else {
    echo "Error al eliminar: " . mysqli_error($conexion);
}
