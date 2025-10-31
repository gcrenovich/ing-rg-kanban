<?php
// actualizar_estado.php
session_start();
include 'includes/json_db.php';

// Requiere POST con: id (reparacion id) y estado (nuevo estado)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error'=>'Method not allowed']);
    exit;
}
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$estado = $_POST['estado'] ?? '';

if ($id <= 0 || $estado === '') {
    http_response_code(400);
    echo json_encode(['error'=>'Parámetros inválidos']);
    exit;
}

$reparaciones = read_json('reparaciones.json');
$ok = update_by_id($reparaciones, $id, ['estado' => $estado]);

if ($ok) {
    write_json('reparaciones.json', $reparaciones);
    echo json_encode(['ok'=>true]);
} else {
    http_response_code(404);
    echo json_encode(['error'=>'No se encontró reparación']);
}
