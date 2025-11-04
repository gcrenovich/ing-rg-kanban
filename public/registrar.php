<?php
require_once __DIR__ . '/../includes/funciones.php';
require_login();
$clientes = leer_json('clientes.json');
$dispositivos = leer_json('dispositivos.json');

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = $_POST['cliente_id'] ?? '';
    if ($cliente_id === 'nuevo') {
        if (empty($_POST['cliente_nombre'])) $mensaje = 'Nombre cliente requerido.';
        else {
            $cl = leer_json('clientes.json');
            $nuevo = [
                'id'=>siguiente_id($cl),
                'nombre'=>$_POST['cliente_nombre'],
                'telefono'=>$_POST['cliente_telefono'] ?? '',
                'email'=>$_POST['cliente_email'] ?? '',
                'direccion'=>$_POST['cliente_direccion'] ?? ''
            ];
            $cl[] = $nuevo;
            escribir_json('clientes.json', $cl);
            $cliente_id = $nuevo['id'];
        }
    }

    if (!$mensaje) {
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
        foreach ($trabajos as $i=>$t)
            if ($t['id'] == $nuevo['id']) {
                $trabajos[$i]['comprobante_id'] = generar_comprobante_id($trabajos);
                escribir_json('trabajos.json', $trabajos);
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
<title>Registrar Ingreso</title>
<link rel="stylesheet" href="css/style.css">
<style>
body {
  font-family: Arial, sans-serif;
  background: #f4f6f8;
  margin: 0;
}
header {
  background: #1e3a8a;
  color: #fff;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 20px;
}
header h1 { margin: 0; font-size: 20px; }
header nav a {
  background: #2563eb;
  color: #fff;
  padding: 6px 12px;
  border-radius: 8px;
  margin-left: 10px;
  text-decoration: none;
  font-size: 14px;
}
header nav a:hover { background: #1d4ed8; }

.container {
  max-width: 800px;
  margin: 20px auto;
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  padding: 20px;
}
h2 { color: #1e3a8a; border-bottom: 2px solid #2563eb; padding-bottom: 5px; }
label { display:block; margin-top:10px; font-weight:bold; color:#333; }
input, select, textarea {
  width: 100%;
  padding: 8px;
  margin-top: 5px;
  border: 1px solid #ccc;
  border-radius: 8px;
}
textarea { resize: vertical; min-height: 70px; }
button {
  margin-top: 15px;
  background: #2563eb;
  color: #fff;
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 15px;
}
button:hover { background: #1d4ed8; }
.error {
  background: #fee2e2;
  color: #b91c1c;
  padding: 10px;
  border-radius: 6px;
  margin-bottom: 10px;
}
fieldset {
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  margin-top: 15px;
  padding: 15px;
}
legend {
  font-weight: bold;
  color: #2563eb;
  padding: 0 8px;
}
</style>
</head>
<body>
<header>
  <h1>Registrar Ingreso de Equipo</h1>
  <nav>
    <a href="index.php">🏠 Inicio</a>
    <a href="clientes.php">👥 Clientes</a>
    <a href="equipos.php">💻 Equipos</a>
  </nav>
</header>

<div class="container">
<?php if($mensaje): ?><p class="error"><?= htmlspecialchars($mensaje) ?></p><?php endif; ?>

<form method="post">
  <fieldset>
    <legend>Cliente</legend>
    <label for="cliente_id">Seleccionar cliente</label>
    <select name="cliente_id" id="cliente_id" onchange="toggleNuevoCliente()" required>
      <option value="">-- Seleccionar cliente --</option>
      <?php foreach($clientes as $c): ?>
        <option value="<?=$c['id']?>"><?=htmlspecialchars($c['nombre'].' - '.$c['telefono'])?></option>
      <?php endforeach; ?>
      <option value="nuevo">➕ Nuevo cliente</option>
    </select>

    <div id="nuevoCliente" style="display:none;">
      <label>Nombre</label>
      <input name="cliente_nombre" placeholder="Nombre completo">
      <label>Teléfono</label>
      <input name="cliente_telefono" placeholder="Teléfono">
      <label>Email</label>
      <input name="cliente_email" placeholder="Correo electrónico">
      <label>Dirección</label>
      <input name="cliente_direccion" placeholder="Dirección">
    </div>
  </fieldset>

  <fieldset>
    <legend>Equipo</legend>
    <label for="dispositivo_id">Tipo de equipo</label>
    <select name="dispositivo_id" required>
      <option value="">-- Seleccionar equipo --</option>
      <?php foreach($dispositivos as $d): ?>
        <option value="<?=$d['id']?>"><?=htmlspecialchars($d['tipo'])?></option>
      <?php endforeach; ?>
    </select>
    <label>Marca</label>
    <input name="marca" placeholder="Marca del equipo">
    <label>Modelo</label>
    <input name="modelo" placeholder="Modelo o referencia">
    <label>Problema</label>
    <textarea name="problema" placeholder="Describa el problema del equipo..." required></textarea>
    <label>Técnico (opcional)</label>
    <input name="tecnico" placeholder="Nombre del técnico">
  </fieldset>

  <button type="submit">💾 Registrar y generar comprobante</button>
</form>
</div>

<script>
function toggleNuevoCliente(){
  var sel = document.getElementById('cliente_id');
  var div = document.getElementById('nuevoCliente');
  div.style.display = (sel.value === 'nuevo') ? 'block' : 'none';
}
</script>
</body>
</html>
