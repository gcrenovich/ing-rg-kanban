<?php
// includes/json_db.php
// Helpers para usar archivos JSON como "BD" local

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
    // Asegurar que sea array indexado
    file_put_contents($path, json_encode(array_values($data), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    return true;
}

function new_id($items) {
    if (empty($items)) return 1;
    $ids = array_column($items, 'id');
    return max($ids) + 1;
}

function find_by_id($items, $id) {
    foreach ($items as $it) if ((string)$it['id'] === (string)$id) return $it;
    return null;
}

function update_by_id(&$items, $id, $payload) {
    $updated = false;
    foreach ($items as &$row) {
        if ((string)$row['id'] === (string)$id) {
            $row = array_merge($row, $payload);
            $updated = true;
            break;
        }
    }
    return $updated;
}

// login helper
function find_user_by_username($username) {
    $users = read_json('usuarios.json');
    foreach ($users as $u) {
        if (isset($u['usuario']) && $u['usuario'] === $username) return $u;
    }
    return null;
}
?>
