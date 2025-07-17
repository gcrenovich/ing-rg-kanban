<?php
session_start();
include 'db.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

$rol = $_SESSION['rol'];
$sector_usuario = $_SESSION['sector'];

$id_componente = $_GET['id'] ?? null;

if (!$id_componente) {
    echo "ID de componente no especificado.";
    exit;
}

// Verificar componente y permisos
$sql = "SELECT c.*, d.sector 
        FROM inventario_componentes c
        JOIN inventario_dispositivos d ON c.dispositivo_id = d.id
        WHERE c.id = $id_componente";

$result = mysqli_query($conexion, $sql);
$componente = mysqli_fetch_assoc($result);

if (!$componente) {
    echo "Componente no encontrado.";
    exit;
}

if ($rol != 'admin' && $componente['sector'] != $sector_usuario) {
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
