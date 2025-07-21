<?php
session_start();
require 'db.php';
include 'includes/header.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$rol = $_SESSION['rol'];
$sector_id = $_SESSION['sector_id'];

include 'includes/navbar.php';

// Definimos sector IT
$SECTOR_IT = 1; // Cambiar al ID correcto de IT en tu base

?>

<div class="container mt-4">

<?php
if ($rol != 'admin' && $sector_id != $SECTOR_IT) {
    // 🔒 Usuario no autorizado, mostramos mensaje pero dejamos el menú
    echo "<div class='alert alert-danger'>No tienes permisos para acceder al Inventario IT.</div>";
    include 'includes/footer.php';
    exit;
}
?>

<h2>Inventario de Parque Informático</h2>

<a href="alta_dispositivo.php" class="btn btn-primary mb-3">+ Agregar Dispositivo</a>

<?php if ($_SESSION['rol'] == 'admin'): ?>
<div class="mb-3">
    <a href="abm_marcas_pc.php" class="btn btn-outline-secondary btn-sm">ABM Marcas PC</a>
    <a href="abm_procesadores.php" class="btn btn-outline-secondary btn-sm">ABM Procesadores</a>
    <a href="abm_memorias_ram.php" class="btn btn-outline-secondary btn-sm">ABM Memorias RAM</a>
    <a href="abm_perifericos.php" class="btn btn-outline-secondary btn-sm">ABM Periféricos</a>
    <a href="abm_sistemas_operativos.php" class="btn btn-outline-secondary btn-sm">ABM Sistemas Operativos</a>
</div>
<?php endif; ?>

<?php
// Consulta inventario
if ($rol == 'admin') {
    $sql = "SELECT d.*, 
                   m.nombre AS marca_nombre, 
                   p.nombre AS procesador_nombre, 
                   r.nombre AS ram_nombre, 
                   so.nombre AS so_nombre,
                   s.nombre AS sector_nombre
            FROM inventario_dispositivos d
            LEFT JOIN marcas_pc m ON d.marca_id = m.id
            LEFT JOIN procesadores p ON d.procesador_id = p.id
            LEFT JOIN memorias_ram r ON d.memoria_ram_id = r.id
            LEFT JOIN sistemas_operativos so ON d.so_id = so.id
            JOIN sectores s ON d.sector_id = s.id
            ORDER BY d.fecha_registro DESC";
} else {
    $sql = "SELECT d.*, 
                   m.nombre AS marca_nombre, 
                   p.nombre AS procesador_nombre, 
                   r.nombre AS ram_nombre, 
                   so.nombre AS so_nombre,
                   s.nombre AS sector_nombre
            FROM inventario_dispositivos d
            LEFT JOIN marcas_pc m ON d.marca_id = m.id
            LEFT JOIN procesadores p ON d.procesador_id = p.id
            LEFT JOIN memorias_ram r ON d.memoria_ram_id = r.id
            LEFT JOIN sistemas_operativos so ON d.so_id = so.id
            JOIN sectores s ON d.sector_id = s.id
            WHERE d.sector_id = $sector_id
            ORDER BY d.fecha_registro DESC";
}

$result = mysqli_query($conexion, $sql);
?>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Tipo</th>
            <th>Marca / Modelo</th>
            <th>Procesador</th>
            <th>RAM</th>
            <th>Sistema Operativo</th>
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
            <td><?php echo htmlspecialchars($row['marca_nombre']) . ' / ' . htmlspecialchars($row['modelo']); ?></td>
            <td><?php echo $row['procesador_nombre'] ?? '-'; ?></td>
            <td><?php echo $row['ram_nombre'] ?? '-'; ?></td>
            <td><?php echo $row['so_nombre'] ?? '-'; ?></td>
            <td><?php echo htmlspecialchars($row['numero_serie']); ?></td>
            <td><?php echo htmlspecialchars($row['ip']); ?></td>
            <td><?php echo htmlspecialchars($row['usuario_asignado']); ?></td>
            <td><?php echo htmlspecialchars($row['sector_nombre']); ?></td>
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

<?php include 'includes/footer.php'; ?>
