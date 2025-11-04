<?php
// api/clientes.php
require_once __DIR__ . '/../includes/funciones.php';
header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'GET') {
    $clientes = leer_json('clientes.json');
    if (isset($_GET['id'])) {
        $c = find_by_id($clientes, $_GET['id']);
        echo json_encode($c ?: null);
    } else {
        echo json_encode($clientes);
    }
    exit;
}

if ($method === 'POST') {
    $clientes = leer_json('clientes.json');
    $data = $_POST;
    // validaciones mínimas
    if (empty($data['nombre'])) {
        http_response_code(400); echo json_encode(['error'=>'Nombre requerido']); exit;
    }
    $nuevo = [
        'id' => siguiente_id($clientes),
        'nombre' => $data['nombre'],
        'telefono' => $data['telefono'] ?? '',
        'email' => $data['email'] ?? '',
        'direccion' => $data['direccion'] ?? ''
    ];
    $clientes[] = $nuevo;
    if (escribir_json('clientes.json', $clientes)) {
        echo json_encode(['ok'=>true,'cliente'=>$nuevo]);
    } else {
        http_response_code(500); echo json_encode(['error'=>'No se pudo guardar']);
    }
    exit;
}

// otros métodos no permitidos aquí
http_response_code(405);
echo json_encode(['error'=>'method not allowed']);
