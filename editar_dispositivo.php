<?php
session_start();
include 'db.php';
include 'includes/navbar.php';

// Verificación de acceso
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

$rol = $_SESSION['rol'];
$sector_usuario = $_SESSION['sector'];

// Obtener ID del dispositivo
$id = $_GET['id'] ?? null;
if (!$id) {
    echo "ID de dispositivo no especificado.";
    exit;
}

// Obtener datos actuales
$sql = "SELECT * FROM inventario_dispositivos WHERE id = $id";
$result = mysqli_query($conexion, $sql);
$dispositivo = mysqli_fetch_assoc($result);

if (!$dispositivo) {
    echo "Dispositivo no encontrado.";
    exit;
}

// Verificación de permisos por sector
if ($rol != 'admin' && $dispositivo['sector'] != $sector_usuario) {
    echo "Acceso denegado. Solo puede editar dispositivos de su sector.";
    exit;
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipo_dispositivo = $_POST['tipo_dispositivo'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $numero_serie = $_POST['numero_serie'];
    $ip = $_POST['ip'];
    $usuario_asignado = $_POST['usuario_asignado'];
    $estado = $_POST['estado'];
    $fecha_registro = $_POST['fecha_registro'];
    $observaciones = $_POST['observaciones'];

    if ($rol == 'admin') {
        $sector = $_POST['sector'];
    } else {
        $sector = $sector_usuario;
    }

    $sql_update = "UPDATE inventario_dispositivos SET 
        tipo_dispositivo='$tipo_dispositivo',
        marca='$marca',
        modelo='$modelo',
        numero_serie='$numero_serie',
        ip='$ip',
        usuario_asignado='$usuario_asignado',
        sector='$sector',
        estado='$estado',
        fecha_registro='$fecha_registro',
        observaciones='$observaciones'
        WHERE id=$id";

    if (mysqli_query($conexion, $sql_update)) {
        header('Location: inventario.php');
        exit;
    } else {
        echo "Error al actualizar: " . mysqli_error($conexion);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Dispositivo</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Editar Dispositivo</h2>

    <form method="POST">
        <div class="mb-3">
            <label>Tipo de Dispositivo</label>
            <select name="tipo_dispositivo" class="form-control" required>
                <option <?php if ($dispositivo['tipo_dispositivo'] == 'PC') echo 'selected'; ?>>PC</option>
                <option <?php if ($dispositivo['tipo_dispositivo'] == 'Notebook') echo 'selected'; ?>>Notebook</option>
                <option <?php if ($dispositivo['tipo_dispositivo'] == 'Monitor') echo 'selected'; ?>>Monitor</option>
                <option <?php if ($dispositivo['tipo_dispositivo'] == 'Impresora') echo 'selected'; ?>>Impresora</option>
                <option <?php if ($dispositivo['tipo_dispositivo'] == 'Periférico') echo 'selected'; ?>>Periférico</option>
                <option <?php if ($dispositivo['tipo_dispositivo'] == 'Otro') echo 'selected'; ?>>Otro</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Marca</label>
            <input type="text" name="marca" class="form-control" required value="<?php echo htmlspecialchars($dispositivo['marca']); ?>">
        </div>

        <div class="mb-3">
            <label>Modelo</label>
            <input type="text" name="modelo" class="form-control" required value="<?php echo htmlspecialchars($dispositivo['modelo']); ?>">
        </div>

        <div class="mb-3">
            <label>Número de Serie</label>
            <input type="text" name="numero_serie" class="form-control" value="<?php echo htmlspecialchars($dispositivo['numero_serie']); ?>">
        </div>

        <div class="mb-3">
            <label>IP</label>
            <input type="text" name="ip" class="form-control" value="<?php echo htmlspecialchars($dispositivo['ip']); ?>">
        </div>

        <div class="mb-3">
            <label>Usuario Asignado</label>
            <input type="text" name="usuario_asignado" class="form-control" value="<?php echo htmlspecialchars($dispositivo['usuario_asignado']); ?>">
        </div>

        <?php if ($rol == 'admin'): ?>
            <div class="mb-3">
                <label>Sector</label>
                <input type="text" name="sector" class="form-control" required value="<?php echo htmlspecialchars($dispositivo['sector']); ?>">
            </div>
        <?php endif; ?>

        <div class="mb-3">
            <label>Estado</label>
            <select name="estado" class="form-control">
                <option <?php if ($dispositivo['estado'] == 'Activo') echo 'selected'; ?>>Activo</option>
                <option <?php if ($dispositivo['estado'] == 'En Reparación') echo 'selected'; ?>>En Reparación</option>
                <option <?php if ($dispositivo['estado'] == 'Dado de Baja') echo 'selected'; ?>>Dado de Baja</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Fecha de Registro</label>
            <input type="date" name="fecha_registro" class="form-control" required value="<?php echo $dispositivo['fecha_registro']; ?>">
        </div>

        <div class="mb-3">
            <label>Observaciones</label>
            <textarea name="observaciones" class="form-control"><?php echo htmlspecialchars($dispositivo['observaciones']); ?></textarea>
        </div>

        <button type="submit" class="btn btn-success">Actualizar</button>
        <a href="inventario.php" class="btn btn-secondary">Volver</a>
    </form>
</div>
</body>
</html>
