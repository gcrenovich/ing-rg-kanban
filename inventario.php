<?php
session_start();
require 'db.php'; // Conexión a la base

// Verificación de acceso: solo usuarios logueados
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Variables de sesión
$rol = $_SESSION['rol'];
$sector_id = $_SESSION['sector_id']; // El sector se maneja como ID

include 'includes/navbar.php'; // Menú de navegación

// Consulta de inventario con sector_id y join a sectores
if ($rol == 'admin') {
    // Si es admin, ve todos los dispositivos y muestra el nombre del sector
    $sql = "SELECT d.*, s.nombre AS sector_nombre 
            FROM inventario_dispositivos d
            JOIN sectores s ON d.sector_id = s.id
            ORDER BY d.fecha_registro DESC";
} else {
    // Si es usuario, ve solo los dispositivos de su sector
    $sql = "SELECT d.*, s.nombre AS sector_nombre 
            FROM inventario_dispositivos d
            JOIN sectores s ON d.sector_id = s.id
            WHERE d.sector_id = $sector_id
            ORDER BY d.fecha_registro DESC";
}

$result = mysqli_query($conexion, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario de Parque Informático</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Inventario de Parque Informático</h2>

    <a href="alta_dispositivo.php" class="btn btn-primary mb-3">+ Agregar Dispositivo</a>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Marca / Modelo</th>
                <th>N° Serie</th>
                <th>IP</th>
                <th>Asignado a</th>
                <th>Sector</th>
                <th>Estado</th>
                <th>Fecha Registro</th>
                <th>Opciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['tipo_dispositivo']); ?></td>
                <td><?php echo htmlspecialchars($row['marca']) . ' / ' . htmlspecialchars($row['modelo']); ?></td>
                <td><?php echo htmlspecialchars($row['numero_serie']); ?></td>
                <td><?php echo htmlspecialchars($row['ip']); ?></td>
                <td><?php echo htmlspecialchars($row['usuario_asignado']); ?></td>
                <td><?php echo htmlspecialchars($row['sector_nombre']); ?></td> <!-- Muestra el nombre del sector -->
                <td><?php echo htmlspecialchars($row['estado']); ?></td>
                <td><?php echo htmlspecialchars($row['fecha_registro']); ?></td>
                <td>
                    <a href="editar_dispositivo.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                    <a href="componentes.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">Componentes</a>
                    <a href="eliminar_dispositivo.php?id=<?php echo $row['id']; ?>" 
                        class="btn btn-sm btn-danger"
                        onclick="return confirm('¿Seguro que quieres eliminar este dispositivo? Esto eliminará también sus componentes.');">
                        Eliminar
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
