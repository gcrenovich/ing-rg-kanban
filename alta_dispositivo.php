<?php
session_start();
include 'db.php';
include 'includes/header.php';

// Verificación de acceso
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$rol = $_SESSION['rol'];
$sector_id = $_SESSION['sector_id'];

// Si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipo_dispositivo = $_POST['tipo_dispositivo'];
    $marca_id = $_POST['marca_id'];
    $modelo = $_POST['modelo'];
    $numero_serie = $_POST['numero_serie'];
    $ip = $_POST['ip'];
    $usuario_asignado = $_POST['usuario_asignado'];
    $estado = $_POST['estado'];
    $fecha_registro = $_POST['fecha_registro'];
    $observaciones = $_POST['observaciones'];

    // Campos adicionales si es PC o Notebook
    $procesador_id = $_POST['procesador_id'] ?? NULL;
    $memoria_ram_id = $_POST['memoria_ram_id'] ?? NULL;

    if ($rol == 'admin') {
        $sector_id = $_POST['sector_id'];
    }

    $sql = "INSERT INTO inventario_dispositivos 
        (tipo_dispositivo, marca_id, modelo, numero_serie, ip, usuario_asignado, sector_id, estado, fecha_registro, observaciones, procesador_id, memoria_ram_id)
        VALUES 
        ('$tipo_dispositivo', $marca_id, '$modelo', '$numero_serie', '$ip', '$usuario_asignado', $sector_id, '$estado', '$fecha_registro', '$observaciones', 
        " . ($procesador_id ?: "NULL") . ", " . ($memoria_ram_id ?: "NULL") . ")";

    if (mysqli_query($conexion, $sql)) {
        header('Location: inventario.php');
        exit;
    } else {
        echo "Error al guardar: " . mysqli_error($conexion);
    }
}

// Consultas para combos
$marcas = mysqli_query($conexion, "SELECT id, nombre FROM marcas_pc ORDER BY nombre ASC");
$procesadores = mysqli_query($conexion, "SELECT id, nombre FROM procesadores ORDER BY nombre ASC");
$ram = mysqli_query($conexion, "SELECT id, nombre FROM memorias_ram ORDER BY nombre ASC");
$sectores = mysqli_query($conexion, "SELECT id, nombre FROM sectores ORDER BY nombre ASC");
?>

<div class="container mt-4">
    <h2>Alta de Dispositivo</h2>

    <form method="POST" id="form_dispositivo">
        <div class="mb-3">
            <label>Tipo de Dispositivo</label>
            <select name="tipo_dispositivo" id="tipo_dispositivo" class="form-control" required onchange="mostrarCamposAdicionales()">
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
            <select name="marca_id" class="form-control" required>
                <option value="">Seleccione una marca...</option>
                <?php while($m = mysqli_fetch_assoc($marcas)): ?>
                    <option value="<?php echo $m['id']; ?>"><?php echo $m['nombre']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div id="campos_adicionales" style="display:none;">
            <div class="mb-3">
                <label>Procesador</label>
                <select name="procesador_id" class="form-control">
                    <option value="">Seleccione un procesador...</option>
                    <?php while($p = mysqli_fetch_assoc($procesadores)): ?>
                        <option value="<?php echo $p['id']; ?>"><?php echo $p['nombre']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Memoria RAM</label>
                <select name="memoria_ram_id" class="form-control">
                    <option value="">Seleccione memoria RAM...</option>
                    <?php while($r = mysqli_fetch_assoc($ram)): ?>
                        <option value="<?php echo $r['id']; ?>"><?php echo $r['nombre']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
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
            <div class="mb-3">
                <label>Sector</label>
                <select name="sector_id" class="form-control" required>
                    <option value="">Seleccione un sector...</option>
                    <?php while($s = mysqli_fetch_assoc($sectores)): ?>
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

<script>
// Mostrar combos adicionales si es PC o Notebook
function mostrarCamposAdicionales() {
    var tipo = document.getElementById('tipo_dispositivo').value;
    var campos = document.getElementById('campos_adicionales');

    if (tipo === 'PC' || tipo === 'Notebook') {
        campos.style.display = 'block';
    } else {
        campos.style.display = 'none';
    }
}
</script>

<?php include 'includes/footer.php'; ?>
