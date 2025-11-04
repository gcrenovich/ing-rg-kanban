<?php
// api/comentarios.php
require_once __DIR__ . '/../includes/funciones.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo json_encode(['error'=>'method']); exit;
}
$id = $_POST['id'] ?? 0;
$texto = trim($_POST['texto'] ?? '');
$autor = trim($_POST['autor'] ?? ''); // opcional

if (!$id || $texto === '') { http_response_code(400); echo json_encode(['error'=>'params']); exit; }

$trabajos = leer_json('trabajos.json');
$ok = false;
foreach ($trabajos as &$t) {
    if ((string)$t['id'] === (string)$id) {
        if (!isset($t['comentarios']) || !is_array($t['comentarios'])) $t['comentarios'] = [];
        $t['comentarios'][] = ['fecha'=>date('Y-m-d H:i:s'), 'autor'=>$autor, 'texto'=>$texto];
        $ok = true; break;
    }
}
if ($ok) { escribir_json('trabajos.json', $trabajos); echo json_encode(['ok'=>true]); }
else { http_response_code(404); echo json_encode(['error'=>'not found']); }
