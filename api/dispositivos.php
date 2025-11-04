<?php
// api/dispositivos.php
require_once __DIR__ . '/../includes/funciones.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(leer_json('dispositivos.json'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lista = leer_json('dispositivos.json');
    $tipo = trim($_POST['tipo'] ?? '');
    if ($tipo === '') { http_response_code(400); echo json_encode(['error'=>'tipo requerido']); exit; }
    $nuevo = ['id'=>siguiente_id($lista),'tipo'=>$tipo];
    $lista[] = $nuevo;
    if (escribir_json('dispositivos.json', $lista)) echo json_encode(['ok'=>true,'item'=>$nuevo]);
    else { http_response_code(500); echo json_encode(['error'=>'no guardado']); }
    exit;
}
http_response_code(405); echo json_encode(['error'=>'method']);
