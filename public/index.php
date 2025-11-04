<?php
require_once __DIR__ . '/../includes/funciones.php';
require_login();

// Leer datos JSON
$trabajos = leer_json('trabajos.json');
$clientes = leer_json('clientes.json');
$dispositivos = leer_json('dispositivos.json');

function clienteById($clientes, $id) {
    foreach ($clientes as $c) if ((string)$c['id'] === (string)$id) return $c;
    return null;
}
function dispositivoById($disp, $id) {
    foreach ($disp as $d) if ((string)$d['id'] === (string)$id) return $d;
    return null;
}

$estados = ['Pendiente', 'En proceso', 'Finalizado', 'Entregado', 'Cancelado'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Reparaciones - Taller Tecnológico</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 0;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #1e3a8a;
            color: #fff;
            padding: 10px 20px;
        }
        header .btn {
            background: #2563eb;
            color: #fff;
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
            margin-left: 6px;
        }
        header .btn:hover { background: #1d4ed8; }

        h1 {
            margin: 0;
            font-size: 20px;
        }
        .kanban {
            display: flex;
            justify-content: space-around;
            align-items: flex-start;
            padding: 20px;
            gap: 15px;
        }
        .columna {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            width: 18%;
            min-width: 220px;
            padding: 10px;
        }
        .columna h2 {
            text-align: center;
            color: #fff;
            border-radius: 8px;
            margin: 0 0 10px;
            padding: 8px;
        }
        #pendiente h2 { background: #dc2626; }
        #en_proceso h2 { background: #f59e0b; }
        #finalizado h2 { background: #16a34a; }
        #entregado h2 { background: #2563eb; }
        #cancelado h2 { background: #6b7280; }

        .contenedor-tareas {
            min-height: 300px;
            padding: 5px;
            background: #f9fafb;
            border-radius: 8px;
        }

        .tarjeta {
            background: #fff;
            border: 1px solid #ddd;
            border-left: 5px solid #2563eb;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: transform 0.1s ease, box-shadow 0.1s ease;
        }
        .tarjeta:hover {
            transform: scale(1.02);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        small {
            color: #555;
        }
    </style>
</head>
<body>

<header>
    <h1>Gestión de Reparaciones - Taller Tecnológico</h1>
    <div>
        <a class="btn" href="registrar.php">+ Nuevo ingreso</a>
        <a class="btn" href="reportes.php">Reportes</a>
        <a class="btn" href="logout.php" style="background:#e63946">Salir</a>
    </div>
</header>

<div class="kanban">
<?php foreach ($estados as $estado): ?>
    <div class="columna" id="<?= strtolower(str_replace(' ', '_', $estado)) ?>">
        <h2><?= htmlspecialchars($estado) ?></h2>
        <div class="contenedor-tareas" data-estado="<?= htmlspecialchars($estado) ?>">
            <?php foreach ($trabajos as $t): ?>
                <?php if (($t['estado'] ?? '') === $estado): ?>
                    <?php 
                        $cli = clienteById($clientes, $t['cliente_id'] ?? null);
                        $dis = dispositivoById($dispositivos, $t['dispositivo_id'] ?? null);
                        $nombreCliente = $cli['nombre'] ?? 'Desconocido';
                        $tipoDisp = $dis['tipo'] ?? ($t['dispositivo'] ?? 'Sin especificar');
                        $modelo = $t['modelo'] ?? '';
                    ?>
                    <div class="tarjeta" data-id="<?= htmlspecialchars($t['id']) ?>">
                        <strong><?= htmlspecialchars($tipoDisp) ?></strong><br>
                        <?= htmlspecialchars($modelo) ?><br>
                        <small><?= htmlspecialchars($nombreCliente) ?></small>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>
</div>

<script>
document.querySelectorAll('.contenedor-tareas').forEach(list => {
    new Sortable(list, {
        group: 'kanban',
        animation: 150,
        onEnd: function (evt) {
            const id = evt.item.dataset.id;
            const estado = evt.to.dataset.estado;
            fetch('../api/trabajos.php', {
                method: 'POST',
                body: new URLSearchParams({ action: 'update_estado', id, estado })
            });
        }
    });
});

document.querySelectorAll('.tarjeta').forEach(card => {
    card.addEventListener('click', () => {
        const id = card.dataset.id;
        window.location.href = `detalles.php?id=${id}`;
    });
});
</script>

</body>
</html>
