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

$id_componente = $_GET['id'] ?? null;

if (!$id_componente) {
    echo "ID de componente no especificado.";
    exit;
}

// Verificar componente y permisos: usamos sector_id
$sql = "SELECT c.*, d.sector_id, d.id as dispositivo_id
        FROM inventario_componentes c
        JOIN inventario_dispositivos d ON c.dispositivo_id = d.id
        WHERE c.id = $id_componente";

$result = mysqli_query($conexion, $sql);
$componente = mysqli_fetch_assoc($result);

if (!$componente) {
    echo "Componente no encontrado.";
    exit;
}

// Control de acceso por sector_id
if ($rol != 'admin' && $componente['sector_id'] != $sector_usuario) {
    echo "Acceso denegado.";
    exit;
}

// Eliminar componente
$sql_delete = "DELETE FROM inventario_componentes WHERE id = $id_componente";

if (mysqli_query($conexion, $sql_delete)) {
    header('Location: componentes.php?id=' . $componente['dispositivo_id']);
    exit;
} else {
    echo "Error al eliminar: " . mysqli_error($conexion);
}
