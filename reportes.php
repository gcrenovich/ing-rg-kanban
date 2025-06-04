<?php
include 'includes/header.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/navbar.php';
?>

<h2 class="text-center mb-4">Reportes</h2>
<div class="container">
    <div class="row">
        <div class="col-md-6 mb-4">
            <h3>Reporte de Tareas por Usuario</h3>
            <form id="reporte-usuario-form">
                <div class="mb-3">
                    <label for="usuario-select" class="form-label">Seleccionar Usuario</label>
                    <select id="usuario-select" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach ($usuarios as $usuario): ?>
                            <option value="<?= htmlspecialchars($usuario['id']) ?>"><?= htmlspecialchars($usuario['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Generar Reporte</button>
            </form>
            <div id="reporte-usuario-result" class="mt-4"></div>
        </div>

        <div class="col-md-6 mb-4">
            <h3>Reporte de Tareas por Estado</h3>
            <form id="reporte-estado-form">
                <div class="mb-3">
                    <label for="estado-select" class="form-label">Seleccionar Estado</label>
                    <select id="estado-select" class="form-select">
                        <option value="">Todos</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="proceso">En Proceso</option>
                        <option value="finalizado">Finalizado</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Generar Reporte</button>
            </form>
            <div id="reporte-estado-result" class="mt-4"></div>
        </div>
    </div>

