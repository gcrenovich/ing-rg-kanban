<?php
// public/registrar.php
require_once __DIR__ . '/../includes/funciones.php';
require_login();
$clientes = leer_json('clientes.json');
$dispositivos = leer_json('dispositivos.json');

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Si viene cliente_nuevo, crear cliente primero
    $cliente_id = $_POST['cliente_id'] ?? '';
    if ($cliente_id === 'nuevo') {
        // validar nombre
        if (empty($_POST['cliente_nombre'])) $mensaje = 'Nombre cliente requerido.';
        else {
            $cl = leer_json('clientes.json');
            $nuevo = ['id'=>siguiente_id($cl),'nombre'=>$_POST['cliente_nombre'],'telefono'=>$_POST['cliente_telefono'] ?? '','email'=>$_POST['cliente_email'] ?? '','direccion'=>$_POST['cliente_direccion'] ?? ''];
            $cl[] = $nuevo;
            escribir_json('clientes.json', $cl);
            $cliente_id = $nuevo['id'];
        }
    }

    if (!$mensaje) {
        // crear trabajo por POST a api/trabajos.php
        $fd = new \stdClass(); // no se usa, hacemos fetch server-side simple
        // Reutilizo la lógica de api: manipulamos el JSON directamente
        $trabajos = leer_json('trabajos.json');
        $nuevo = [
            'id' => siguiente_id($trabajos),
            'cliente_id' => (int)$cliente_id,
            'dispositivo_id' => (int)($_POST['dispositivo_id'] ?? 0),
            'marca' => $_POST['marca'] ?? '',
            'modelo' => $_POST['modelo'] ?? '',
            'problema' => $_POST['problema'] ?? '',
            'estado' => 'Pendiente',
            'tecnico' => $_POST['tecnico'] ?? '',
            'fecha_ingreso' => date('Y-m-d'),
            'fecha_entrega' => null,
            'comentarios' => [],
            'comprobante_id' => ''
        ];
        $trabajos[] = $nuevo;
        escribir_json('trabajos.json', $trabajos);
        // asignar comprobante_id luego de guardar
        $trabajos = leer_json('trabajos.json');
        // encontrar por id
        foreach ($trabajos as $i=>$t) if ($t['id'] == $nuevo['id']) {
            $trabajos[$i]['comprobante_id'] = generar_comprobante_id($trabajos);
            $comp = $trabajos[$i]['comprobante_id'];
            escribir_json('trabajos.json', $trabajos);
            // abrir comprobante en nueva pestaña (redirigir a comprobante.php con id)
            header('Location: comprobante.php?id=' . $nuevo['id']);
            exit;
        }
    }
}

?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Registrar ingreso</title>
<link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
<header class="topbar"><div class="logo">Registrar Ingreso</div><div class="top-actions"><a class="btn" href="dashboard.php">Volver</a></div></header>
<main style="padding:12px;">
<?php if($mensaje): ?><p class="error"><?=htmlspecialchars($mensaje)?></p><?php endif; ?>
<form method="post">
  <h3>Cliente</h3>
  <select name="cliente_id" id="cliente_id" onchange="toggleNuevoCliente()">
    <option value="">-- Seleccionar cliente --</option>
    <?php foreach($clientes as $c): ?>
      <option value="<?=$c['id']?>"><?=htmlspecialchars($c['nombre'].' - '.$c['telefono'])?></option>
    <?php endforeach; ?>
    <option value="nuevo">-- Nuevo cliente --</option>
  </select>
  <div id="nuevoCliente" style="display:none;margin-top:8px;">
    <input name="cliente_nombre" placeholder="Nombre">
    <input name="cliente_telefono" placeholder="Teléfono">
    <input name="cliente_email" placeholder="Email">
    <input name="cliente_direccion" placeholder="Dirección">
  </div>

  <h3>Equipo</h3>
  <select name="dispositivo_id" required>
    <?php foreach($dispositivos as $d): ?>
      <option value="<?=$d['id']?>"><?=htmlspecialchars($d['tipo'])?></option>
    <?php endforeach; ?>
  </select>
  <input name="marca" placeholder="Marca">
  <input name="modelo" placeholder="Modelo">
  <textarea name="problema" placeholder="Descripción del problema" required></textarea>
  <label>Técnico (opc)</label><input name="tecnico" placeholder="Nombre técnico">
  <button class="btn" type="submit">Registrar y generar comprobante</button>
</form>
</main>
<script>
function toggleNuevoCliente(){
  var sel = document.getElementById('cliente_id');
  var div = document.getElementById('nuevoCliente');
  if(sel.value === 'nuevo') div.style.display = 'block'; else div.style.display = 'none';
}
</script>
</body>
</html>
