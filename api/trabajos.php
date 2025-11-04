
<?php
function update_by_id(&$array, $id, $data) {
    foreach ($array as &$item) {
        if ($item['id'] == $id) {
            foreach ($data as $k => $v) {
                $item[$k] = $v;
            }
            return true;
        }
    }
    return false;
}

// api/trabajos.php
require_once __DIR__ . '/../includes/funciones.php';
header('Content-Type: application/json; charset=utf-8');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $t = leer_json('trabajos.json');
    if (isset($_GET['id'])) {
        $item = find_by_id($t, $_GET['id']);
        echo json_encode($item ?: null);
    } else {
        echo json_encode($t);
    }
    exit;
}

if ($method === 'POST') {
    $action = $_POST['action'] ?? 'create';
    $trabajos = leer_json('trabajos.json');

    if ($action === 'create') {
        // campos obligatorios: cliente_id, dispositivo_id, marca/modelo/problema
        if (empty($_POST['cliente_id']) || empty($_POST['dispositivo_id']) || empty($_POST['problema'])) {
            http_response_code(400); echo json_encode(['error'=>'Faltan campos']); exit;
        }
        $nuevo = [
            'id' => siguiente_id($trabajos),
            'cliente_id' => (int)$_POST['cliente_id'],
            'dispositivo_id' => (int)$_POST['dispositivo_id'],
            'marca' => $_POST['marca'] ?? '',
            'modelo' => $_POST['modelo'] ?? '',
            'problema' => $_POST['problema'],
            'estado' => $_POST['estado'] ?? 'Pendiente',
            'tecnico' => $_POST['tecnico'] ?? '',
            'fecha_ingreso' => date('Y-m-d'),
            'fecha_entrega' => null,
            'comentarios' => [],
            'comprobante_id' => '' // asignar tras guardar
        ];
        $trabajos[] = $nuevo;
        if (escribir_json('trabajos.json', $trabajos)) {
            // asignar comprobante_id (post-guardar)
            $trabajos = leer_json('trabajos.json');
            $nidx = count($trabajos) - 1;
            $trabajos[$nidx]['comprobante_id'] = generar_comprobante_id($trabajos);
            escribir_json('trabajos.json', $trabajos);
            echo json_encode(['ok'=>true,'id'=>$nuevo['id'],'comprobante_id'=>$trabajos[$nidx]['comprobante_id']]);
        } else { http_response_code(500); echo json_encode(['error'=>'No guardado']); }
        exit;
    }

    if ($action === 'update_estado') {
        $id = $_POST['id'] ?? 0;
        $estado = $_POST['estado'] ?? '';
        if (!$id || $estado === '') { http_response_code(400); echo json_encode(['error'=>'params']); exit; }
        $ok = update_by_id($trabajos, $id, ['estado'=>$estado]);
        if ($ok) { escribir_json('trabajos.json', $trabajos); echo json_encode(['ok'=>true]); }
        else { http_response_code(404); echo json_encode(['error'=>'not found']); }
        exit;
    }

    if ($action === 'update') {
        $id = $_POST['id'] ?? 0;
        if (!$id) { http_response_code(400); echo json_encode(['error'=>'id required']); exit; }
        $payload = [];
        $fields = ['marca','modelo','problema','tecnico','fecha_entrega','estado'];
        foreach ($fields as $f) if (isset($_POST[$f])) $payload[$f] = $_POST[$f];
        $ok = update_by_id($trabajos, $id, $payload);
        if ($ok) { escribir_json('trabajos.json', $trabajos); echo json_encode(['ok'=>true]); }
        else { http_response_code(404); echo json_encode(['error'=>'not found']); }
        exit;
    }
}

// DELETE (por id)
if ($method === 'DELETE') {
    parse_str(file_get_contents('php://input'), $data);
    $id = $data['id'] ?? 0;
    if (!$id) { http_response_code(400); echo json_encode(['error'=>'id required']); exit; }
    $trabajos = leer_json('trabajos.json');
    $found=false;
    foreach ($trabajos as $i=>$t) if ((string)$t['id'] === (string)$id) { array_splice($trabajos,$i,1); $found=true; break; }
    if ($found) { escribir_json('trabajos.json', $trabajos); echo json_encode(['ok'=>true]); } 
    else { http_response_code(404); echo json_encode(['error'=>'not found']); }
    exit;
}

http_response_code(405); echo json_encode(['error'=>'method not allowed']);
