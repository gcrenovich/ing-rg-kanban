<?php
session_start();
include 'db.php';
include 'includes/navbar.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

$rol = $_SESSION['rol'];
$sector_usuario = $_SESSION['sector'];

// Obtener ID del dispositivo
$id_dispositivo = $_GET['id'] ?? null;
if (!$id_dispositivo) {
    echo "Dispositivo no especificado.";
    exit;
}

// Verificar que el dispositivo exista y pertenece al sector correspondiente
$sql_dispositivo = "SELECT * FROM inventario_dispositivos WHERE id = $id_dispositivo";
$result_disp = mysqli_query($conexion, $sql_dispositivo);
$dispositivo = mysqli_fetch_assoc($result_disp);

if (!$dispositivo) {
    echo "Dispositivo no encontrado.";
    exit;
}

if ($rol != 'admin' && $dispositivo['sector'] != $sector_usuario) {
    echo "Acceso denegado al dispositivo.";
    exit;
}

// Si se envió el formulario de nuevo componente
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $componente = $_POST['componente'];
    $detalle = $_POST['detalle'];
    $fecha_instalacion = $_POST['fecha_instalacion'];

    $sql_insert = "INSERT INTO inventario_componentes (dispositivo_id, componente, detalle, fecha_instalacion)
                   VALUES ($id_dispositivo, '$componente', '$detalle', '$fecha_instalacion')";

    if (!mysqli_query($conexion, $sql_insert)) {
        echo "Error al guardar componente: " . mysqli_error($conexion);
    }
}

// Consultar componentes del dispositivo
$sql_componentes = "SELECT * FROM inventario_componentes WHERE dispositivo_id = $id_dispositivo";
$result_componentes = mysqli_query($conexion, $sql_componentes);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Componentes de <?php echo htmlspecialchars($dispositivo['tipo_dispositivo']); ?></title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h3>Componentes de <?php echo htmlspecialchars($dispositivo['tipo_dispositivo'] . " - " . $dispositivo['marca'] . " " . $dispositivo['modelo']); ?></h3>

    <form method="POST" class="mb-4">
        <h5>Agregar Componente</h5>

        <div class="mb-3">
            <label>Componente</label>
            <input type="text" name="componente" class="form-control" required placeholder="Ej: Memoria RAM, Disco SSD, etc.">
        </div>

        <div class="mb-3">
            <label>Detalle</label>
            <input type="text" name="detalle" class="form-control" placeholder="Ej: 16GB DDR4 3200MHz">
        </div>

        <div class="mb-3">
            <label>Fecha de Instalación</label>
            <input type="date" name="fecha_instalacion" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
        </div>

        <button type="submit" class="btn btn-primary">Agregar Componente</button>
        <a href="inventario.php" class="btn btn-secondary">Volver</a>
    </form>

    <h5>Lista de Componentes</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Componente</th>
                <th>Detalle</th>
                <th>Fecha Instalación</th>
            </tr>
        </thead>
        <tbody>
            <?php while($comp = mysqli_fetch_assoc($result_componentes)): ?>
            <tr>
                <td><?php echo htmlspecialchars($comp['componente']); ?></td>
                <td><?php echo htmlspecialchars($comp['detalle']); ?></td>
                <td><?php echo htmlspecialchars($comp['fecha_instalacion']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
