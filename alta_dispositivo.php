<?php
session_start();
include 'includes/json_db.php';

// Requiere login - adapta según tu sistema
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuarios = read_json('usuarios.json'); // opcional
$tecnicos = read_json('tecnicos.json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Campos esperados desde el formulario:
    // tipo_dispositivo, marca, modelo, nro_serie, cliente_nombre, cliente_telefono, observaciones, titulo (opcional)
    $tipo = $_POST['tipo_dispositivo'] ?? 'Otro';
    $marca = $_POST['marca'] ?? '';
    $modelo = $_POST['modelo'] ?? '';
    $nro_serie = $_POST['nro_serie'] ?? '';
    $cliente_nombre = $_POST['cliente_nombre'] ?? '';
    $cliente_telefono = $_POST['cliente_telefono'] ?? '';
    $observaciones = $_POST['observaciones'] ?? '';
    $titulo = trim($_POST['titulo'] ?? '');
    if ($titulo === '') $titulo = trim($marca . ' ' . $modelo);
    // --- Guardar en equipos.json ---
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

    // --- Crear reparación en reparaciones.json ---
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
        'estado' => 'En revisión', // estado inicial
        'notas' => '',
    ];
    $reparaciones[] = $nueva_rep;
    write_json('reparaciones.json', $reparaciones);

    // Redirigir a detalles o al panel
    header('Location: abm_tareas.php?mensaje=ingreso_creado&id=' . $rep_id);
    exit;
}
?>
<!-- Formulario HTML (puedes integrar en tu header/footer) -->
<!doctype html>
<html><head><meta charset="utf-8"><title>Nuevo ingreso</title></head><body>
<h2>Registrar ingreso de dispositivo</h2>
<form method="post">
  <label>Tipo</label><br><input name="tipo_dispositivo" placeholder="Celular / Impresora"><br>
  <label>Marca</label><br><input name="marca"><br>
  <label>Modelo</label><br><input name="modelo"><br>
  <label>Nro. Serie</label><br><input name="nro_serie"><br>
  <label>Nombre cliente</label><br><input name="cliente_nombre"><br>
  <label>Teléfono cliente</label><br><input name="cliente_telefono"><br>
  <label>Título (ej: Parlante Sony)</label><br><input name="titulo"><br>
  <label>Observaciones</label><br><textarea name="observaciones"></textarea><br>
  <button type="submit">Registrar ingreso</button>
</form>
</body></html>
