<?php
require_once __DIR__ . '/../includes/funciones.php';
require_login();

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? '';

if (!$id) {
    die('ID no válido');
}

$trabajos = leer_json('trabajos.json');

switch ($action) {
    case 'eliminar':
        // Eliminar trabajo
        $trabajos = array_filter($trabajos, fn($t) => $t['id'] != $id);
        escribir_json('trabajos.json', array_values($trabajos));
        header('Location: index.php');
        exit;

    case 'guardar':
        // Cambiar estado a "Guardado"
        foreach ($trabajos as &$t) {
            if ($t['id'] == $id && $t['estado'] === 'Entregado') {
                $t['estado'] = 'Guardado';
                break;
            }
        }
        escribir_json('trabajos.json', $trabajos);
        header('Location: index.php');
        exit;

    default:
        echo "⚠️ Acción no reconocida.";
        exit;
}
