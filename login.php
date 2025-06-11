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
  <link rel="stylesheet" href="style.css">
</head>
<body class="login-body">
  <div class="login-container">
    <img src="img/logo.png" alt="Logo">
    <h1>Kanban - Ingreso</h1>
    <h2>Ingenio Río Grande S.A.</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="post">
      <input type="email" name="email" placeholder="Correo electrónico" required>
      <input type="password" name="clave" placeholder="Contraseña" required>
      <button type="submit">Ingresar</button>
    </form>
  </div>
</body>
</html>
