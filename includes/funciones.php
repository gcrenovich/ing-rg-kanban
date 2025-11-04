<?php
// includes/funciones.php
// Utilidades para manipular archivos JSON y autenticación básica

function data_path($file) {
    $dir = __DIR__ . '/../data';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    return $dir . '/' . basename($file);
}

function leer_json($file) {
    $path = data_path($file);
    if (!file_exists($path)) {
        file_put_contents($path, json_encode([], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    }
    $txt = file_get_contents($path);
    $arr = json_decode($txt, true);
    return is_array($arr) ? $arr : [];
}

function escribir_json($file, $data) {
    $path = data_path($file);
    // backup simple
    $bak = $path . '.bak';
    copy($path, $bak);
    $fp = fopen($path, 'c+');
    if (!$fp) return false;
    if (flock($fp, LOCK_EX)) {
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode(array_values($data), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        fflush($fp);
        flock($fp, LOCK_UN);
    }
    fclose($fp);
    return true;
}

function siguiente_id($lista) {
    $max = 0;
    foreach ($lista as $i) if (isset($i['id']) && (int)$i['id'] > $max) $max = (int)$i['id'];
    return $max + 1;
}

function generar_comprobante_id($trabajos) {
    // buscar max C-XXXX
    $max = 0;
    foreach ($trabajos as $t) {
        if (!empty($t['comprobante_id'])) {
            if (preg_match('/C-(\d+)/', $t['comprobante_id'], $m)) {
                $n = (int)$m[1];
                if ($n > $max) $max = $n;
            }
        }
    }
    $next = $max + 1;
    return 'C-' . str_pad($next, 4, '0', STR_PAD_LEFT);
}

function find_by_id($lista, $id) {
    foreach ($lista as $item) if ((string)$item['id'] === (string)$id) return $item;
    return null;
}

function save_and_return($file, $data) {
    $ok = escribir_json($file, $data);
    return $ok;
}

// autenticación: guardar password con password_hash, verificar con password_verify
function find_user_by_username($usuario) {
    $users = leer_json('usuarios.json');
    foreach ($users as $u) if (isset($u['usuario']) && $u['usuario'] === $usuario) return $u;
    return null;
}

function is_logged_in() {
    session_start();
    return !empty($_SESSION['usuario']);
}

function require_login() {
    session_start();
    if (empty($_SESSION['usuario'])) {
        header('Location: login.php');
        exit;
    }
}
?>
