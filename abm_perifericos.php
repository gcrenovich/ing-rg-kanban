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
if (isset($_POST['nuevo_periferico'])) {
    $tipo = $_POST['tipo'];
    $nombre = $_POST['nombre'];
    $sql = "INSERT INTO marcas_perifericos (tipo, nombre) VALUES ('$tipo', '$nombre')";
    mysqli_query($conexion, $sql);
    header('Location: abm_perifericos.php');
    exit;
}

// Eliminación
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $sql = "DELETE FROM marcas_perifericos WHERE id = $id";
    mysqli_query($conexion, $sql);
    header('Location: abm_perifericos.php');
    exit;
}

// Edición
if (isset($_POST['editar_periferico'])) {
    $id = $_POST['id'];
    $tipo = $_POST['tipo'];
    $nombre = $_POST['nombre'];
    $sql = "UPDATE marcas_perifericos SET tipo = '$tipo', nombre = '$nombre' WHERE id = $id";
    mysqli_query($conexion, $sql);
    header('Location: abm_perifericos.php');
    exit;
}

$perifericos = mysqli_query($conexion, "SELECT * FROM marcas_perifericos ORDER BY tipo, nombre ASC");
?>

<div class="container mt-4">
    <h2>Administrar Marcas de Periféricos</h2>

    <form method="POST" class="mb-4">
        <div class="mb-3">
            <label>Tipo</label>
            <select name="tipo" class="form-control" required>
                <option value="Mouse">Mouse</option>
                <option value="Teclado">Teclado</option>
                <option value="Monitor">Monitor</option>
                <option value="Impresora">Impresora</option>
                <option value="Scanner">Scanner</option>
                <option value="Parlantes">Parlantes</option>
                <option value="Multifuncion">Multifuncion</option>
                <option value="Otro">Otro</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Marca</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>

        <button type="submit" name="nuevo_periferico" class="btn btn-success">Agregar</button>
    </form>

    <h5>Periféricos existentes</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Marca</th>
                <th>Opciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($p = mysqli_fetch_assoc($perifericos)): ?>
            <tr>
                <td><?php echo htmlspecialchars($p['tipo']); ?></td>
                <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                        <select name="tipo" required>
                            <option value="Mouse" <?php if ($p['tipo']=='Mouse') echo 'selected'; ?>>Mouse</option>
                            <option value="Teclado" <?php if ($p['tipo']=='Teclado') echo 'selected'; ?>>Teclado</option>
                            <option value="Monitor" <?php if ($p['tipo']=='Monitor') echo 'selected'; ?>>Monitor</option>
                            <option value="Otro" <?php if ($p['tipo']=='Otro') echo 'selected'; ?>>Otro</option>
                        </select>
                        <input type="text" name="nombre" value="<?php echo htmlspecialchars($p['nombre']); ?>" required>
                        <button type="submit" name="editar_periferico" class="btn btn-sm btn-warning">Editar</button>
                    </form>

                    <a href="abm_perifericos.php?eliminar=<?php echo $p['id']; ?>" 
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('¿Seguro que quieres eliminar este periférico?');">
                       Eliminar
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
