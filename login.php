<?php
require 'db.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $clave = $_POST['clave'];
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();
    if ($usuario && password_verify($clave, $usuario['clave'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['sector_id'] = $usuario['sector_id'];
        $_SESSION['rol'] = $usuario['rol'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Credenciales incorrectas";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login Kanban</title>
</head>
<body>
  <div class="contenido">
    <h1>Kanban - Ingreso al sistema</h1>
    <h2>Ingenio Rio Grande S.A.</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="post">
      <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>
      </div>
      <div class="form-group">
        <label for="clave">Contraseña:</label>
        <input type="password" name="clave" id="clave" required>
      </div>
    <button type="submit">Ingresar</button>
  </form>
</body>
</html>