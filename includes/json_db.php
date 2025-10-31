<?php
// includes/json_db.php
// Funciones simples para usar archivos JSON como "BD" local.
// Rutas relativas asumen que se incluye desde la carpeta principal del proyecto (kanban/).

function data_path($file) {
    $dir = __DIR__ . '/../data';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    return $dir . '/' . basename($file);
}

function read_json($file) {
    $path = data_path($file);
    if (!file_exists($path)) file_put_contents($path, json_encode([], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    $txt = file_get_contents($path);
    $arr = json_decode($txt, true);
    return is_array($arr) ? $arr : [];
}

function write_json($file, $data) {
    $path = data_path($file);
    file_put_contents($path, json_encode(array_values($data), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    return true;
}

function new_id($items) {
    if (empty($items)) return 1;
    $ids = array_column($items, 'id');
    return max($ids) + 1;
}

// Buscar por id
function find_by_id($items, $id) {
    foreach ($items as $i) if ((int)$i['id'] === (int)$id) return $i;
    return null;
}

// Actualiza un registro por id (merge)
function update_by_id(&$items, $id, $payload) {
    $updated = false;
    foreach ($items as &$row) {
        if ((int)$row['id'] === (int)$id) {
            $row = array_merge($row, $payload);
            $updated = true;
            break;
        }
    }
    return $updated;
}

// Simple login check helper
function login_find_user($username) {
    $users = read_json('usuarios.json');
    foreach ($users as $u) {
        if (isset($u['usuario']) && $u['usuario'] === $username) return $u;
    }
    return null;
}
?>
