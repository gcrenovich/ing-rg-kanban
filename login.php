<?php
// login.php
session_start();
require_once __DIR__ . '/includes/json_db.php';

// Si ya está logueado
if (!empty($_SESSION['usuario_id'])) {
    header('Location: abm_tareas.php');
    exit;
}

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $clave = trim($_POST['clave'] ?? '');

    if ($usuario === '' || $clave === '') {
        $mensaje = 'Complete usuario y clave';
    } else {
        $u = find_user_by_username($usuario);
        if ($u) {
            // Soportamos clave en claro (legacy) o clave hasheada en campo 'clave_hash'
            $ok = false;
            if (isset($u['clave_hash']) && password_verify($clave, $u['clave_hash'])) $ok = true;
            if (isset($u['clave']) && $u['clave'] === $clave) $ok = true;

            if ($ok) {
                // Guardar sesión
                $_SESSION['usuario_id'] = $u['id'];
                $_SESSION['usuario_nombre'] = $u['usuario'];
                $_SESSION['usuario_rol'] = $u['rol'] ?? 'user';
                header('Location: abm_tareas.php');
                exit;
            }
        }
        $mensaje = 'Usuario o clave incorrectos';
    }
}
?>
<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><title>Login</title></head>
<body>
<h2>Iniciar sesión</h2>
<?php if ($mensaje): ?><p style="color:red;"><?=htmlspecialchars($mensaje)?></p><?php endif; ?>
<form method="post">
  <label>Usuario</label><br>
  <input name="usuario" autofocus><br>
  <label>Clave</label><br>
  <input type="password" name="clave"><br><br>
  <button type="submit">Ingresar</button>
</form>
</body>
</html>
