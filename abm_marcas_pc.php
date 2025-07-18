<?php
session_start();
include 'db.php';
include 'includes/navbar.php';
include 'includes/header.php';

// Solo admins pueden administrar catálogos
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'admin') {
    header('Location: dashboard.php');
    exit;
}

// Alta de marca
if (isset($_POST['nueva_marca'])) {
    $nombre = $_POST['nombre'];

    $sql = "INSERT INTO marcas_pc (nombre) VALUES ('$nombre')";
    mysqli_query($conexion, $sql);
    header('Location: abm_marcas_pc.php');
    exit;
}

// Eliminación
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $sql = "DELETE FROM marcas_pc WHERE id = $id";
    mysqli_query($conexion, $sql);
    header('Location: abm_marcas_pc.php');
    exit;
}

// Modificación
if (isset($_POST['editar_marca'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];

    $sql = "UPDATE marcas_pc SET nombre = '$nombre' WHERE id = $id";
    mysqli_query($conexion, $sql);
    header('Location: abm_marcas_pc.php');
    exit;
}

// Listar marcas
$marcas = mysqli_query($conexion, "SELECT * FROM marcas_pc ORDER BY nombre ASC");
?>

<div class="container mt-4">
    <h2>Administrar Marcas de PC</h2>

    <!-- Formulario de alta -->
    <form method="POST" class="mb-4">
        <div class="mb-3">
            <label>Nueva Marca</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>
        <button type="submit" name="nueva_marca" class="btn btn-success">Agregar</button>
    </form>

    <h5>Marcas existentes</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Marca</th>
                <th>Opciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($m = mysqli_fetch_assoc($marcas)): ?>
            <tr>
                <td><?php echo htmlspecialchars($m['nombre']); ?></td>
                <td>
                    <!-- Formulario de edición inline -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
                        <input type="text" name="nombre" value="<?php echo htmlspecialchars($m['nombre']); ?>" required>
                        <button type="submit" name="editar_marca" class="btn btn-sm btn-warning">Editar</button>
                    </form>

                    <a href="abm_marcas_pc.php?eliminar=<?php echo $m['id']; ?>" 
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('¿Seguro que quieres eliminar esta marca?');">
                       Eliminar
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
