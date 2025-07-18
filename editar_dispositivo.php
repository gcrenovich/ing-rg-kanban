<?php
session_start();
include 'db.php';
include 'includes/header.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$rol = $_SESSION['rol'];
$sector_usuario = $_SESSION['sector_id'];

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "ID de dispositivo no especificado.";
    exit;
}

// Obtener datos actuales con sector
$sql = "SELECT d.*, s.nombre AS sector_nombre 
        FROM inventario_dispositivos d
        JOIN sectores s ON d.sector_id = s.id
        WHERE d.id = $id";
$result = mysqli_query($conexion, $sql);
$dispositivo = mysqli_fetch_assoc($result);

if (!$dispositivo) {
    echo "Dispositivo no encontrado.";
    exit;
}

// Verificación de permisos
if ($rol != 'admin' && $dispositivo['sector_id'] != $sector_usuario) {
    echo "Acceso denegado.";
    exit;
}

// Procesar formulario
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
    $procesador_id = $_POST['procesador_id'] ?? NULL;
    $memoria_ram_id = $_POST['memoria_ram_id'] ?? NULL;

    $sector_id = ($rol == 'admin') ? $_POST['sector_id'] : $sector_usuario;

    $sql_update = "UPDATE inventario_dispositivos SET 
        tipo_dispositivo='$tipo_dispositivo',
        marca_id=$marca_id,
        modelo='$modelo',
        numero_serie='$numero_serie',
        ip='$ip',
        usuario_asignado='$usuario_asignado',
        sector_id=$sector_id,
        estado='$estado',
        fecha_registro='$fecha_registro',
        observaciones='$observaciones',
        procesador_id=" . ($procesador_id ?: "NULL") . ",
        memoria_ram_id=" . ($memoria_ram_id ?: "NULL") . "
        WHERE id=$id";

    if (mysqli_query($conexion, $sql_update)) {
        header('Location: inventario.php');
        exit;
    } else {
        echo "Error al actualizar: " . mysqli_error($conexion);
    }
}

// Consultas para combos
$marcas = mysqli_query($conexion, "SELECT id, nombre FROM marcas_pc ORDER BY nombre ASC");
$procesadores = mysqli_query($conexion, "SELECT id, nombre FROM procesadores ORDER BY nombre ASC");
$ram = mysqli_query($conexion, "SELECT id, nombre FROM memorias_ram ORDER BY nombre ASC");
$sectores = mysqli_query($conexion, "SELECT id, nombre FROM sectores ORDER BY nombre ASC");
?>

<div class="container mt-4">
    <h2>Editar Dispositivo</h2>

    <form method="POST" id="form_dispositivo">
        <div class="mb-3">
            <label>Tipo de Dispositivo</label>
            <select name="tipo_dispositivo" id="tipo_dispositivo" class="form-control" required onchange="mostrarCamposAdicionales()">
                <?php
                $tipos = ['PC','Notebook','Monitor','Impresora','Periférico','Otro'];
                foreach ($tipos as $tipo):
                    $sel = ($dispositivo['tipo_dispositivo'] == $tipo) ? 'selected' : '';
                    echo "<option $sel>$tipo</option>";
                endforeach;
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Marca</label>
            <select name="marca_id" class="form-control" required>
                <option value="">Seleccione una marca...</option>
                <?php while($m = mysqli_fetch_assoc($marcas)): ?>
                    <option value="<?php echo $m['id']; ?>" <?php if ($dispositivo['marca_id'] == $m['id']) echo 'selected'; ?>>
                        <?php echo $m['nombre']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div id="campos_adicionales" style="display:none;">
            <div class="mb-3">
                <label>Procesador</label>
                <select name="procesador_id" class="form-control">
                    <option value="">Seleccione un procesador...</option>
                    <?php while($p = mysqli_fetch_assoc($procesadores)): ?>
                        <option value="<?php echo $p['id']; ?>" <?php if ($dispositivo['procesador_id'] == $p['id']) echo 'selected'; ?>>
                            <?php echo $p['nombre']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Memoria RAM</label>
                <select name="memoria_ram_id" class="form-control">
                    <option value="">Seleccione memoria RAM...</option>
                    <?php while($r = mysqli_fetch_assoc($ram)): ?>
                        <option value="<?php echo $r['id']; ?>" <?php if ($dispositivo['memoria_ram_id'] == $r['id']) echo 'selected'; ?>>
                            <?php echo $r['nombre']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
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
                <select name="sector_id" class="form-control" required>
                    <?php while($s = mysqli_fetch_assoc($sectores)): ?>
                        <option value="<?php echo $s['id']; ?>" <?php if ($dispositivo['sector_id'] == $s['id']) echo 'selected'; ?>>
                            <?php echo $s['nombre']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
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

<script>
// Mostrar campos adicionales si es PC o Notebook
function mostrarCamposAdicionales() {
    var tipo = document.getElementById('tipo_dispositivo').value;
    var campos = document.getElementById('campos_adicionales');
    if (tipo === 'PC' || tipo === 'Notebook') {
        campos.style.display = 'block';
    } else {
        campos.style.display = 'none';
    }
}
document.addEventListener('DOMContentLoaded', mostrarCamposAdicionales);
</script>

<?php include 'includes/footer.php'; ?>
