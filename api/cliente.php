<?php
// api/clientes.php
require_once __DIR__ . '/../includes/funciones.php';
header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];

/* ============================================================
   GET → Devolver 1 cliente o todos
   ============================================================ */
if ($method === 'GET') {
    $clientes = leer_json('clientes.json');

    if (isset($_GET['id'])) {
        $c = find_by_id($clientes, $_GET['id']);
        echo json_encode($c ?: null, JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode($clientes, JSON_UNESCAPED_UNICODE);
    }
    exit;
}

/* ============================================================
   POST → Crear un nuevo cliente
   ============================================================ */
if ($method === 'POST') {

    $clientes = leer_json('clientes.json');
    $data = $_POST;

    // ✅ Validaciones mínimas
    if (empty($data['nombre'])) {
        http_response_code(400);
        echo json_encode(['error' => 'El nombre es obligatorio']);
        exit;
    }

    if (empty($data['dni'])) {
        http_response_code(400);
        echo json_encode(['error' => 'El DNI es obligatorio']);
        exit;
    }

    // Validación básica de formato DNI (solo números)
    if (!preg_match('/^[0-9]{6,12}$/', $data['dni'])) {
        http_response_code(400);
        echo json_encode(['error' => 'DNI inválido (solo números, 6 a 12 dígitos)']);
        exit;
    }

    // ✅ Registrar cliente nuevo
    $nuevo = [
        'id'        => siguiente_id($clientes),
        'nombre'    => trim($data['nombre']),
        'dni'       => trim($data['dni']),
        'telefono'  => trim($data['telefono'] ?? ''),
        'email'     => trim($data['email'] ?? ''),
        'direccion' => trim($data['direccion'] ?? ''),
        'fecha_alta' => date('Y-m-d H:i:s')
    ];

    // Guardar en archivo
    $clientes[] = $nuevo;

    if (escribir_json('clientes.json', $clientes)) {
        echo json_encode(['ok'=>true, 'cliente'=>$nuevo], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['error'=>'No se pudo guardar el cliente']);
    }

    exit;
}

/* ============================================================
   Métodos no permitidos
   ============================================================ */
http_response_code(405);
echo json_encode(['error'=>'Método no permitido'], JSON_UNESCAPED_UNICODE);
