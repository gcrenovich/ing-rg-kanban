<?php
$trabajos = json_decode(file_get_contents(__DIR__ . '/../data/trabajos.json'), true);
$estados = ['Pendiente', 'En proceso', 'Finalizado', 'Entregado', 'Cancelado'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard de Reparaciones</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
</head>
<body>
<h1>Gestión de Reparaciones - Taller Tecnológico</h1>

<div class="kanban">
<?php foreach ($estados as $estado): ?>
    <div class="columna" id="<?= strtolower(str_replace(' ', '_', $estado)) ?>">
        <h2><?= $estado ?></h2>
        <div class="contenedor-tareas" data-estado="<?= $estado ?>">
            <?php foreach ($trabajos as $t): ?>
                <?php if ($t['estado'] === $estado): ?>
                    <div class="tarjeta" data-id="<?= $t['id'] ?>">
                        <strong><?= htmlspecialchars($t['dispositivo']) ?></strong><br>
                        <?= htmlspecialchars($t['modelo']) ?><br>
                        <small><?= htmlspecialchars($t['cliente']) ?></small>
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
