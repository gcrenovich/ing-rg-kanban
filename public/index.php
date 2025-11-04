<?php
$trabajos = json_decode(file_get_contents(__DIR__ . '/../data/trabajos.json'), true) ?? [];
$estados = ['Pendiente', 'En proceso', 'Finalizado', 'Entregado', 'Cancelado'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard de Reparaciones</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
            background: #1e3a8a;
            color: #fff;
            padding: 15px;
            margin: 0;
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
<h1>Gestión de Reparaciones - Taller Tecnológico</h1>

<div class="kanban">
<?php foreach ($estados as $estado): ?>
    <div class="columna" id="<?= strtolower(str_replace(' ', '_', $estado)) ?>">
        <h2><?= htmlspecialchars($estado) ?></h2>
        <div class="contenedor-tareas" data-estado="<?= htmlspecialchars($estado) ?>">
            <?php foreach ($trabajos as $t): ?>
                <?php if (($t['estado'] ?? '') === $estado): ?>
                    <?php 
                        $dispositivo = $t['dispositivo'] ?? 'Sin especificar';
                        $modelo = $t['modelo'] ?? '';
                        $cliente = $t['cliente'] ?? 'Desconocido';
                    ?>
                    <div class="tarjeta" data-id="<?= htmlspecialchars($t['id']) ?>">
                        <strong><?= htmlspecialchars($dispositivo) ?></strong><br>
                        <?= htmlspecialchars($modelo) ?><br>
                        <small><?= htmlspecialchars($cliente) ?></small>
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
