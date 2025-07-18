<?php
session_start();
include 'db.php';
include 'includes/navbar.php';
include 'includes/header.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'admin') {
    header('Location: dashboard.php');
    exit;
}

// Alta
if (isset($_POST['nuevo_procesador'])) {
    $nombre = $_POST['nombre'];
    $sql = "INSERT INTO procesadores (nombre) VALUES ('$nombre')";
    mysqli_query($conexion, $sql);
    header('Location: abm_procesadores.php');
    exit;
}

// Eliminación
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $sql = "DELETE FROM procesadores WHERE id = $id";
    mysqli_query($conexion, $sql);
    header('Location: abm_procesadores.php');
    exit;
}

// Edición
if (isset($_POST['editar_procesador'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $sql = "UPDATE procesadores SET nombre = '$nombre' WHERE id = $id";
    mysqli_query($conexion, $sql);
    header('Location: abm_procesadores.php');
    exit;
}

$procesadores = mysqli_query($conexion, "SELECT * FROM procesadores ORDER BY nombre ASC");
?>

<div class="container mt-4">
    <h2>Administrar Procesadores</h2>

    <form method="POST" class="mb-4">
        <div class="mb-3">
            <label>Nuevo Procesador</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>
        <button type="submit" name="nuevo_procesador" class="btn btn-success">Agregar</button>
    </form>

    <h5>Procesadores existentes</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Opciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($p = mysqli_fetch_assoc($procesadores)): ?>
            <tr>
                <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                        <input type="text" name="nombre" value="<?php echo htmlspecialchars($p['nombre']); ?>" required>
                        <button type="submit" name="editar_procesador" class="btn btn-sm btn-warning">Editar</button>
                    </form>

                    <a href="abm_procesadores.php?eliminar=<?php echo $p['id']; ?>" 
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('¿Seguro que quieres eliminar este procesador?');">
                       Eliminar
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
