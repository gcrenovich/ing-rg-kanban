<?php
session_start();
include 'db.php'; // Conexión a la base
include 'includes/navbar.php'; // Menú de navegación
include 'includes/header.php';  // Carga CSS, estructura inicial y navbar


// Verificación de acceso: solo usuarios logueados
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Variables de sesión
$rol = $_SESSION['rol'];
$sector_id = $_SESSION['sector_id']; // Sector del usuario logueado

// Si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Captura de datos del formulario
    $tipo_dispositivo = $_POST['tipo_dispositivo'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $numero_serie = $_POST['numero_serie'];
    $ip = $_POST['ip'];
    $usuario_asignado = $_POST['usuario_asignado'];
    $estado = $_POST['estado'];
    $fecha_registro = $_POST['fecha_registro'];
    $observaciones = $_POST['observaciones'];

    // Definir el sector_id según el rol
    if ($rol == 'admin') {
        // Admin elige el sector con un select
        $sector_id = $_POST['sector_id'];
    }
    // Si es usuario común, se usa el sector de sesión

    // Inserción en la tabla (sector_id en vez de sector)
    $sql = "INSERT INTO inventario_dispositivos 
        (tipo_dispositivo, marca, modelo, numero_serie, ip, usuario_asignado, sector_id, estado, fecha_registro, observaciones)
        VALUES 
        ('$tipo_dispositivo', '$marca', '$modelo', '$numero_serie', '$ip', '$usuario_asignado', $sector_id, '$estado', '$fecha_registro', '$observaciones')";

    if (mysqli_query($conexion, $sql)) {
        header('Location: inventario.php');
        exit;
    } else {
        echo "Error al guardar: " . mysqli_error($conexion);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Alta de Dispositivo</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Alta de Dispositivo</h2>

    <form method="POST">
        <div class="mb-3">
            <label>Tipo de Dispositivo</label>
            <select name="tipo_dispositivo" class="form-control" required>
                <option value="">Seleccione...</option>
                <option>PC</option>
                <option>Notebook</option>
                <option>Monitor</option>
                <option>Impresora</option>
                <option>Periférico</option>
                <option>Otro</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Marca</label>
            <input type="text" name="marca" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Modelo</label>
            <input type="text" name="modelo" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Número de Serie</label>
            <input type="text" name="numero_serie" class="form-control">
        </div>

        <div class="mb-3">
            <label>IP</label>
            <input type="text" name="ip" class="form-control">
        </div>

        <div class="mb-3">
            <label>Usuario Asignado</label>
            <input type="text" name="usuario_asignado" class="form-control">
        </div>

        <?php if ($rol == 'admin'): ?>
            <!-- Si es admin, elige el sector desde un select -->
            <div class="mb-3">
                <label>Sector</label>
                <select name="sector_id" class="form-control" required>
                    <option value="">Seleccione un sector...</option>
                    <?php
                    $sectores = mysqli_query($conexion, "SELECT id, nombre FROM sectores");
                    while($s = mysqli_fetch_assoc($sectores)):
                    ?>
                        <option value="<?php echo $s['id']; ?>"><?php echo $s['nombre']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        <?php endif; ?>

        <div class="mb-3">
            <label>Estado</label>
            <select name="estado" class="form-control">
                <option>Activo</option>
                <option>En Reparación</option>
                <option>Dado de Baja</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Fecha de Registro</label>
            <input type="date" name="fecha_registro" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
        </div>

        <div class="mb-3">
            <label>Observaciones</label>
            <textarea name="observaciones" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="inventario.php" class="btn btn-secondary">Volver</a>
    </form>
</div>
</body>
</html>
<?php include 'includes/footer.php'; ?>