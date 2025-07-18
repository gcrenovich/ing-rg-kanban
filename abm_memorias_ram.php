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
if (isset($_POST['nueva_ram'])) {
    $nombre = $_POST['nombre'];
    $sql = "INSERT INTO memorias_ram (nombre) VALUES ('$nombre')";
    mysqli_query($conexion, $sql);
    header('Location: abm_memorias_ram.php');
    exit;
}

// Eliminación
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $sql = "DELETE FROM memorias_ram WHERE id = $id";
    mysqli_query($conexion, $sql);
    header('Location: abm_memorias_ram.php');
    exit;
}

// Edición
if (isset($_POST['editar_ram'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $sql = "UPDATE memorias_ram SET nombre = '$nombre' WHERE id = $id";
    mysqli_query($conexion, $sql);
    header('Location: abm_memorias_ram.php');
    exit;
}

$ram = mysqli_query($conexion, "SELECT * FROM memorias_ram ORDER BY nombre ASC");
?>

<div class="container mt-4">
    <h2>Administrar Memorias RAM</h2>

    <form method="POST" class="mb-4">
        <div class="mb-3">
            <label>Nueva Memoria RAM</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>
        <button type="submit" name="nueva_ram" class="btn btn-success">Agregar</button>
    </form>

    <h5>Memorias existentes</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Opciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($r = mysqli_fetch_assoc($ram)): ?>
            <tr>
                <td><?php echo htmlspecialchars($r['nombre']); ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
                        <input type="text" name="nombre" value="<?php echo htmlspecialchars($r['nombre']); ?>" required>
                        <button type="submit" name="editar_ram" class="btn btn-sm btn-warning">Editar</button>
                    </form>

                    <a href="abm_memorias_ram.php?eliminar=<?php echo $r['id']; ?>" 
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('¿Seguro que quieres eliminar esta memoria?');">
                       Eliminar
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
