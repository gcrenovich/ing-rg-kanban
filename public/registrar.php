<?php 
require_once __DIR__ . '/../includes/funciones.php';
require_login();

$clientes = leer_json('clientes.json');
$dispositivos = leer_json('dispositivos.json');

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $cliente_id = $_POST['cliente_id'] ?? '';

    // =====================================================
    // 1) SI ES CLIENTE NUEVO → VALIDAR Y CREAR
    // =====================================================
    if ($cliente_id === 'nuevo') {

        if (empty($_POST['cliente_nombre'])) {
            $mensaje = 'Nombre del cliente requerido.';
        }
        elseif (empty($_POST['cliente_dni'])) {
            $mensaje = 'DNI requerido.';
        }
        elseif (!is_numeric($_POST['cliente_dni'])) {
            $mensaje = 'El DNI debe ser numérico.';
        }
        else {
            // CARGAMOS JSON DE CLIENTES
            $cl = leer_json('clientes.json');

            // Verificar si ya existe un DNI
            foreach ($cl as $c) {
                if (isset($c['dni']) && $c['dni'] == $_POST['cliente_dni']) {
                    $mensaje = 'Ya existe un cliente con ese DNI.';
                    break;
                }
            }

            // SI NO HAY ERRORES → CREAR CLIENTE
            if (!$mensaje) {

                $nuevo = [
                    'id'        => siguiente_id($cl),
                    'nombre'    => $_POST['cliente_nombre'],
                    'dni'       => $_POST['cliente_dni'],  // ✅ AGREGADO
                    'telefono'  => $_POST['cliente_telefono'] ?? '',
                    'email'     => $_POST['cliente_email'] ?? '',
                    'direccion' => $_POST['cliente_direccion'] ?? '',
                    'fecha_registro' => date('Y-m-d')
                ];

                $cl[] = $nuevo;
                escribir_json('clientes.json', $cl);

                $cliente_id = $nuevo['id']; // usar el ID recién creado
            }
        }
    }

    // =====================================================
    // 2) SI NO HUBO ERRORES → REGISTRAR EL TRABAJO
    // =====================================================
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

        // Generar nº de comprobante
        foreach ($trabajos as $i => $t) {
            if ($t['id'] == $nuevo['id']) {
                $trabajos[$i]['comprobante_id'] = generar_comprobante_id($trabajos);
                escribir_json('trabajos.json', $trabajos);

                header('Location: comprobante.php?id=' . $nuevo['id']);
                exit;
            }
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
body { font-family: Arial, sans-serif; background: #f4f6f8; margin: 0; }
header {
  background: #1e3a8a; color: #fff; display: flex;
  justify-content: space-between; align-items: center;
  padding: 12px 20px;
}
header nav a {
  background: #2563eb; color: #fff; padding: 6px 12px;
  border-radius: 8px; margin-left: 10px; text-decoration: none;
}
.container {
  max-width: 800px; margin: 20px auto; background: #fff;
  border-radius: 10px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
label { font-weight: bold; margin-top: 10px; display: block; }
input, select, textarea {
  width: 100%; padding: 8px; margin-top: 5px;
  border: 1px solid #ccc; border-radius: 8px;
}
.error {
  background: #fee2e2; color: #b91c1c; padding: 10px;
  border-radius: 6px; margin-bottom: 10px;
}
fieldset {
  border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px;
}
legend { font-weight: bold; color: #2563eb; }
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

<?php if($mensaje): ?>
<p class="error"><?= htmlspecialchars($mensaje) ?></p>
<?php endif; ?>

<form method="post">

  <fieldset>
    <legend>Cliente</legend>

    <label for="cliente_id">Seleccionar cliente</label>
    <select name="cliente_id" id="cliente_id" onchange="toggleNuevoCliente()" required>
        <option value="">-- Seleccionar cliente --</option>

        <?php foreach($clientes as $c): ?>
            <option value="<?=$c['id']?>">
                <?=htmlspecialchars($c['nombre'].' - '.$c['telefono'])?>
            </option>
        <?php endforeach; ?>

        <option value="nuevo">➕ Nuevo cliente</option>
    </select>

    <div id="nuevoCliente" style="display:none;">
      <label>DNI</label>
      <input name="cliente_dni" placeholder="DNI sin puntos">

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
    <input name="marca" placeholder="Marca">

    <label>Modelo</label>
    <input name="modelo" placeholder="Modelo">

    <label>Problema</label>
    <textarea name="problema" required></textarea>

    <label>Técnico (opcional)</label>
    <input name="tecnico" placeholder="Nombre del técnico">
  </fieldset>

  <button type="submit">💾 Registrar y generar comprobante</button>

</form>
</div>

<script>
function toggleNuevoCliente(){
    document.getElementById('nuevoCliente').style.display =
        (document.getElementById('cliente_id').value === 'nuevo') ? 'block' : 'none';
}
</script>

</body>
</html>
