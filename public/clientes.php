<?php
require_once __DIR__ . '/../includes/funciones.php';
require_login();

$clientes = leer_json('clientes.json');
$mensaje = '';

/* ============================================================
   PROCESO POST (AGREGAR / EDITAR / ELIMINAR)
   ============================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    /* ---------- AGREGAR ---------- */
    if ($accion === 'agregar') {

        $nuevo = [
            'id'        => siguiente_id($clientes),
            'nombre'    => trim($_POST['nombre'] ?? ''),
            'dni'       => trim($_POST['dni'] ?? ''),
            'telefono'  => trim($_POST['telefono'] ?? ''),
            'email'     => trim($_POST['email'] ?? ''),
            'direccion' => trim($_POST['direccion'] ?? '')
        ];

        if ($nuevo['nombre'] === '') {
            $mensaje = 'El nombre es obligatorio.';
        } elseif ($nuevo['dni'] === '') {
            $mensaje = 'El DNI es obligatorio.';
        } else {
            $clientes[] = $nuevo;
            escribir_json('clientes.json', $clientes);
            $mensaje = 'Cliente agregado correctamente.';
        }
    }

    /* ---------- EDITAR ---------- */
    if ($accion === 'editar') {
        foreach ($clientes as &$c) {
            if ($c['id'] == $_POST['id']) {
                $c['nombre']    = trim($_POST['nombre']);
                $c['dni']       = trim($_POST['dni']);
                $c['telefono']  = trim($_POST['telefono']);
                $c['email']     = trim($_POST['email']);
                $c['direccion'] = trim($_POST['direccion']);
                escribir_json('clientes.json', $clientes);
                $mensaje = 'Cliente actualizado correctamente.';
                break;
            }
        }
    }

    /* ---------- ELIMINAR ---------- */
    if ($accion === 'eliminar') {
        $clientes = array_values(array_filter($clientes, fn($c) => $c['id'] != $_POST['id']));
        escribir_json('clientes.json', $clientes);
        $mensaje = 'Cliente eliminado.';
    }
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Gestión de Clientes</title>
<link rel="stylesheet" href="css/style.css">

<style>
body { background:#f4f6f8; font-family:Arial,sans-serif; margin:0; }
header { background:#1e3a8a; color:#fff; display:flex; justify-content:space-between; align-items:center; padding:12px 20px; }
header h1{margin:0;font-size:20px;}
header nav a{background:#2563eb;color:#fff;padding:6px 12px;border-radius:8px;margin-left:10px;text-decoration:none;}
header nav a:hover{background:#1d4ed8;}
.container{max-width:900px;margin:20px auto;background:#fff;padding:20px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.1);}
table{width:100%;border-collapse:collapse;margin-top:15px;}
th,td{padding:8px;border-bottom:1px solid #ddd;text-align:left;}
th{background:#2563eb;color:#fff;}
form input{padding:6px;margin:3px;width:100%;border:1px solid #ccc;border-radius:6px;}
button{background:#2563eb;color:#fff;border:none;border-radius:6px;padding:8px 14px;cursor:pointer;}
button:hover{background:#1d4ed8;}
.error{background:#fee2e2;color:#b91c1c;padding:10px;border-radius:6px;}
.ok{background:#dcfce7;color:#166534;padding:10px;border-radius:6px;}
</style>
</head>

<body>
<header>
  <h1>Gestión de Clientes</h1>
  <nav>
    <a href="index.php">🏠 Inicio</a>
    <a href="registrar.php">➕ Nuevo Ingreso</a>
  </nav>
</header>

<div class="container">

<?php if($mensaje): ?>
  <p class="<?=str_contains($mensaje,'correct')?'ok':'error'?>"><?=htmlspecialchars($mensaje)?></p>
<?php endif; ?>

<h2>Agregar nuevo cliente</h2>

<form method="post">
  <input type="hidden" name="accion" value="agregar">

  <input name="nombre" placeholder="Nombre" required>
  <input name="dni" placeholder="DNI" required>
  <input name="telefono" placeholder="Teléfono">
  <input name="email" placeholder="Email">
  <input name="direccion" placeholder="Dirección">

  <button>Guardar</button>
</form>

<h2>Listado de clientes</h2>

<table>
  <tr>
    <th>ID</th>
    <th>Nombre</th>
    <th>DNI</th>
    <th>Teléfono</th>
    <th>Email</th>
    <th>Dirección</th>
    <th>Acciones</th>
  </tr>

  <?php foreach($clientes as $c): ?>
  <tr>
    <td><?=$c['id']?></td>
    <td><?=htmlspecialchars($c['nombre'])?></td>
    <td><?=htmlspecialchars($c['dni'] ?? '')?></td>
    <td><?=htmlspecialchars($c['telefono'])?></td>
    <td><?=htmlspecialchars($c['email'])?></td>
    <td><?=htmlspecialchars($c['direccion'])?></td>

    <td>
      <!-- ELIMINAR -->
      <form method="post" style="display:inline">
        <input type="hidden" name="accion" value="eliminar">
        <input type="hidden" name="id" value="<?=$c['id']?>">
        <button onclick="return confirm('¿Eliminar cliente?')">🗑</button>
      </form>

      <!-- EDITAR -->
      <button onclick='editarCliente(<?=json_encode($c, JSON_UNESCAPED_UNICODE)?>)'>✏️</button>
    </td>
  </tr>
  <?php endforeach; ?>

</table>
</div>

<script>
function editarCliente(c) {
  const form = document.createElement('form');
  form.method = 'post';
  form.innerHTML = `
    <input type="hidden" name="accion" value="editar">
    <input type="hidden" name="id" value="${c.id}">
    <h3>Editar cliente #${c.id}</h3>

    <input name="nombre" value="${c.nombre}" placeholder="Nombre" required>
    <input name="dni" value="${c.dni ?? ''}" placeholder="DNI" required>
    <input name="telefono" value="${c.telefono}" placeholder="Teléfono">
    <input name="email" value="${c.email}" placeholder="Email">
    <input name="direccion" value="${c.direccion}" placeholder="Dirección">

    <button>Guardar cambios</button>
  `;

  document.body.innerHTML = '';
  document.body.appendChild(form);
}
</script>

</body>
</html>
