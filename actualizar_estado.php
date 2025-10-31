<?php
// actualizar_estado.php
session_start();
require_once __DIR__ . '/includes/json_db.php';
if (empty($_SESSION['usuario_id'])) { http_response_code(401); echo json_encode(['error'=>'no auth']); exit; }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo json_encode(['error'=>'method']); exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$estado = trim($_POST['estado'] ?? '');

if ($id <= 0 || $estado === '') {
    http_response_code(400); echo json_encode(['error'=>'params']); exit;
}

$reparaciones = read_json('reparaciones.json');
$ok = update_by_id($reparaciones, $id, ['estado' => $estado, 'fecha_modificacion' => date('Y-m-d H:i:s')]);

if ($ok) {
    write_json('reparaciones.json', $reparaciones);
    echo json_encode(['ok' => true]);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'not found']);
}
