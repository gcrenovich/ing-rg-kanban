<?php
// public/login.php
// Login simple compatible con:
// - usuarios.json con "password_hash" (recomendado)
// - usuarios.json con "password" (texto plano, para pruebas/local)
//
// Ubicación esperada del JSON: ../data/usuarios.json
// Incluye sesiones y redirección a index.php

session_start();

// función local para leer usuarios (si no usás includes/funciones.php)
function data_path($file) {
    $dir = __DIR__ . '/../data';
    return $dir . '/' . basename($file);
}
function leer_json($file) {
    $path = data_path($file);
    if (!file_exists($path)) return [];
    $txt = file_get_contents($path);
    $arr = json_decode($txt, true);
    return is_array($arr) ? $arr : [];
}
function find_user_by_username($usuario) {
    $users = leer_json('usuarios.json');
    foreach ($users as $u) {
        if (isset($u['usuario']) && $u['usuario'] === $usuario) return $u;
    }
    return null;
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $clave = $_POST['clave'] ?? '';

    if ($usuario === '' || $clave === '') {
        $mensaje = 'Complete usuario y clave';
    } else {
        $u = find_user_by_username($usuario);
        if ($u) {
            $ok = false;
            // Si existe password_hash, verificar con password_verify (seguro)
            if (!empty($u['password_hash']) && password_verify($clave, $u['password_hash'])) {
                $ok = true;
            }
            // Si existe password en texto plano (prueba), comparar directamente
            if (!$ok && isset($u['password']) && $u['password'] === $clave) {
                $ok = true;
            }

            if ($ok) {
                // Guardar sesión (datos mínimos)
                $_SESSION['usuario'] = [
                    'id' => $u['id'] ?? null,
                    'usuario' => $u['usuario'],
                    'nombre' => $u['nombre'] ?? $u['usuario'],
                    'rol' => $u['rol'] ?? 'user'
                ];
                // Redirigir a index
                header('Location: index.php');
                exit;
            } else {
                $mensaje = 'Usuario o clave incorrectos';
            }
        } else {
            $mensaje = 'Usuario no encontrado';
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Login - Taller</title>
<link rel="stylesheet" href="../assets/css/estilos.css">
<style>
/* Estilos mínimos por si no cargás el CSS global */
body { font-family: Arial, sans-serif; background:#f4f6f8; margin:0; }
.login-box { width:360px; margin:80px auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 3px 10px rgba(0,0,0,0.12); }
.login-box h2 { margin:0 0 12px 0; }
.login-box label { display:block; margin-top:8px; font-size:14px; color:#333; }
.login-box input { width:100%; padding:8px; margin-top:6px; box-sizing:border-box; border:1px solid #ddd; border-radius:4px; }
.btn { display:inline-block; background:#457b9d; color:#fff; padding:8px 12px; border-radius:6px; border:none; cursor:pointer; margin-top:12px; }
.error { color:#e63946; margin-top:8px; }
.small { font-size:13px; color:#666; margin-top:8px; }
</style>
</head>
<body>
<div class="login-box">
  <h2>Iniciar sesión</h2>

  <?php if ($mensaje): ?>
    <div class="error"><?= htmlspecialchars($mensaje) ?></div>
  <?php endif; ?>

  <form method="post" autocomplete="off">
    <label for="usuario">Usuario</label>
    <input id="usuario" name="usuario" type="text" required autofocus>

    <label for="clave">Clave</label>
    <input id="clave" name="clave" type="password" required>

    <button class="btn" type="submit">Ingresar</button>
  </form>

  <div class="small">
    Usuario de prueba: <strong>admin</strong> / Clave: <strong>admin123</strong> (si pegaste ese hash o password)
  </div>
</div>
</body>
</html>
