<?php
// public/login.php
require_once __DIR__ . '/../includes/funciones.php';
session_start();
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $clave = $_POST['clave'] ?? '';
    $u = find_user_by_username($usuario);
    if ($u && !empty($u['password_hash']) && password_verify($clave, $u['password_hash'])) {
        $_SESSION['usuario'] = ['id'=>$u['id'],'usuario'=>$u['usuario'],'rol'=>$u['rol'] ?? 'user'];
        header('Location: dashboard.php');
        exit;
    } else {
        $mensaje = 'Usuario o clave incorrectos';
    }
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Login</title>
<link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body class="body-login">
<div class="login-box">
  <h2>Iniciar sesión</h2>
  <?php if($mensaje): ?><p class="error"><?=htmlspecialchars($mensaje)?></p><?php endif; ?>
  <form method="post">
    <label>Usuario</label><input name="usuario" required autofocus>
    <label>Clave</label><input name="clave" type="password" required>
    <button class="btn">Ingresar</button>
  </form>
</div>
</body>
</html>
