// abm_usuarios.php
<?php
include 'includes/header.php';
require 'db.php';
session_start();
if ($_SESSION['rol'] !== 'admin') exit('Acceso denegado');
$sectores = $conn->query("SELECT * FROM sectores")->fetchAll();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clave_hash = password_hash($_POST['clave'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, clave, sector_id, rol) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['nombre'], $_POST['email'], $clave_hash, $_POST['sector_id'], $_POST['rol']
    ]);
}
$usuarios = $conn->query("SELECT * FROM usuarios")->fetchAll();
?>
<form method="post">
    <input name="nombre" placeholder="Nombre" required>
    <input name="email" type="email" placeholder="Email" required>
    <input name="clave" type="password" placeholder="Contraseña" required>
    <select name="sector_id">
        <?php foreach ($sectores as $s) echo "<option value='{$s['id']}'>{$s['nombre']}</option>"; ?>
    </select>
    <select name="rol">
        <option value="usuario">Usuario</option>
        <option value="admin">Admin</option>
    </select>
    <button type="submit">Crear Usuario</button>
</form>
<ul>
<?php foreach ($usuarios as $u) echo "<li>{$u['nombre']} ({$u['email']})</li>"; ?>
</ul>
