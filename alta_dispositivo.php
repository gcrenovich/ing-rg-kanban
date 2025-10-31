<?php
// alta_dispositivo.php
session_start();
require_once __DIR__ . '/includes/json_db.php';
if (empty($_SESSION['usuario_id'])) { header('Location: login.php'); exit; }

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = trim($_POST['tipo_dispositivo'] ?? 'Otro');
    $marca = trim($_POST['marca'] ?? '');
    $modelo = trim($_POST['modelo'] ?? '');
    $nro_serie = trim($_POST['nro_serie'] ?? '');
    $cliente_nombre = trim($_POST['cliente_nombre'] ?? '');
    $cliente_telefono = trim($_POST['cliente_telefono'] ?? '');
    $observaciones = trim($_POST['observaciones'] ?? '');
    $titulo = trim($_POST['titulo'] ?? '');
    if ($titulo === '') $titulo = trim($marca . ' ' . $modelo);

    // equipos.json
    $equipos = read_json('equipos.json');
    $eq_id = new_id($equipos);
    $nuevo_equipo = [
        'id' => $eq_id,
        'tipo' => $tipo,
        'marca' => $marca,
        'modelo' => $modelo,
        'nro_serie' => $nro_serie,
        'cliente_nombre' => $cliente_nombre,
        'cliente_telefono' => $cliente_telefono,
        'observaciones' => $observaciones,
        'fecha_ingreso' => date('Y-m-d'),
    ];
    $equipos[] = $nuevo_equipo;
    write_json('equipos.json', $equipos);

    // reparaciones.json
    $reparaciones = read_json('reparaciones.json');
    $rep_id = new_id($reparaciones);
    $nueva_rep = [
        'id' => $rep_id,
        'equipo_id' => $eq_id,
        'titulo' => $titulo,
        'diagnostico' => '',
        'tecnico_id' => null,
        'fecha_ingreso' => date('Y-m-d'),
        'fecha_entrega' => null,
        'costo' => 0,
        'estado' => 'En revisión',
        'notas' => ''
    ];
    $reparaciones[] = $nueva_rep;
    write_json('reparaciones.json', $reparaciones);

    header('Location: abm_tareas.php?msg=ingreso_creado&id=' . $rep_id);
    exit;
}
?>
<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><title>Nuevo ingreso</title></head>
<body>
<h2>Registrar nuevo ingreso</h2>
<form method="post">
  <label>Tipo</label><br><input name="tipo_dispositivo" placeholder="Celular, Impresora..."><br>
  <label>Marca</label><br><input name="marca"><br>
  <label>Modelo</label><br><input name="modelo"><br>
  <label>Nro. Serie</label><br><input name="nro_serie"><br>
  <label>Nombre cliente</label><br><input name="cliente_nombre"><br>
  <label>Teléfono cliente</label><br><input name="cliente_telefono"><br>
  <label>Título (ej. Parlante Sony)</label><br><input name="titulo"><br>
  <label>Observaciones</label><br><textarea name="observaciones"></textarea><br><br>
  <button type="submit">Registrar ingreso</button>
</form>
<p><a href="abm_tareas.php">Volver al Kanban</a></p>
</body>
</html>
