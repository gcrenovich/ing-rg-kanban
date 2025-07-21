<?php
session_start();
include 'db.php';
include 'includes/header.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'admin') {
    header('Location: login.php');
    exit;
}

// Alta
if (isset($_POST['accion']) && $_POST['accion'] == 'alta') {
    $nombre = $_POST['nombre'];
    $categoria = $_POST['categoria'];
    $sql = "INSERT INTO sistemas_operativos (nombre, categoria) VALUES ('$nombre', '$categoria')";
    mysqli_query($conexion, $sql);
    header('Location: abm_sistemas_operativos.php');
    exit;
}

// Baja
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $sql = "DELETE FROM sistemas_operativos WHERE id = $id";
    mysqli_query($conexion, $sql);
    header('Location: abm_sistemas_operativos.php');
    exit;
}

// Consulta
$result = mysqli_query($conexion, "SELECT * FROM sistemas_operativos ORDER BY nombre ASC");
?>

<div class="container mt-4">
    <h2>ABM Sistemas Operativos</h2>

    <form method="POST" class="mb-4">
        <input type="hidden" name="accion" value="alta">
        <div class="row">
            <div class="col-md-5">
                <input type="text" name="nombre" class="form-control" placeholder="Nombre del SO" required>
            </div>
            <div class="col-md-4">
                <select name="categoria" class="form-control" required>
                    <option value="">Categoría...</option>
                    <option>PC/Notebook</option>
                    <option>Móvil/Tablet</option>
                    <option>SmartTV</option>
                    <option>Otros</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-success">Agregar</button>
                <a href="inventario.php" class="btn btn-secondary">Volver</a>
            </div>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                <td><?php echo htmlspecialchars($row['categoria']); ?></td>
                <td>
                    <a href="abm_sistemas_operativos.php?eliminar=<?php echo $row['id']; ?>" 
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('¿Seguro que deseas eliminar este sistema operativo?');">
                        Eliminar
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
