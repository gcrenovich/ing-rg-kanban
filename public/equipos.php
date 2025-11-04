<?php
require_once __DIR__ . '/../includes/funciones.php';
require_login();
$dispositivos = leer_json('dispositivos.json');
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    if ($accion === 'agregar') {
        $nuevo = ['id'=>siguiente_id($dispositivos),'tipo'=>trim($_POST['tipo'] ?? '')];
        if ($nuevo['tipo'] === '') $mensaje='El tipo es obligatorio.';
        else {
            $dispositivos[]=$nuevo;
            escribir_json('dispositivos.json',$dispositivos);
            $mensaje='Equipo agregado correctamente.';
        }
    }
    if ($accion === 'editar') {
        foreach ($dispositivos as &$d)
            if ($d['id']==$_POST['id']){
                $d['tipo']=$_POST['tipo'];
                escribir_json('dispositivos.json',$dispositivos);
                $mensaje='Equipo actualizado correctamente.';
                break;
            }
    }
    if ($accion === 'eliminar'){
        $dispositivos=array_values(array_filter($dispositivos,fn($d)=>$d['id']!=$_POST['id']));
        escribir_json('dispositivos.json',$dispositivos);
        $mensaje='Equipo eliminado.';
    }
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Gestión de Equipos</title>
<link rel="stylesheet" href="css/style.css">
<style>
body{background:#f4f6f8;font-family:Arial,sans-serif;margin:0;}
header{background:#1e3a8a;color:#fff;display:flex;justify-content:space-between;align-items:center;padding:12px 20px;}
header h1{margin:0;font-size:20px;}
header nav a{background:#2563eb;color:#fff;padding:6px 12px;border-radius:8px;margin-left:10px;text-decoration:none;}
header nav a:hover{background:#1d4ed8;}
.container{max-width:700px;margin:20px auto;background:#fff;padding:20px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.1);}
table{width:100%;border-collapse:collapse;margin-top:15px;}
th,td{padding:8px;border-bottom:1px solid #ddd;text-align:left;}
th{background:#2563eb;color:#fff;}
input{padding:6px;margin:3px;width:100%;border:1px solid #ccc;border-radius:6px;}
button{background:#2563eb;color:#fff;border:none;border-radius:6px;padding:8px 14px;cursor:pointer;}
button:hover{background:#1d4ed8;}
.ok{background:#dcfce7;color:#166534;padding:10px;border-radius:6px;}
.error{background:#fee2e2;color:#b91c1c;padding:10px;border-radius:6px;}
</style>
</head>
<body>
<header>
  <h1>Gestión de Equipos</h1>
  <nav>
    <a href="index.php">🏠 Inicio</a>
    <a href="registrar.php">➕ Nuevo Ingreso</a>
  </nav>
</header>

<div class="container">
<?php if($mensaje): ?><p class="<?=str_contains($mensaje,'correct')?'ok':'error'?>"><?=htmlspecialchars($mensaje)?></p><?php endif; ?>

<h2>Agregar nuevo equipo</h2>
<form method="post">
  <input type="hidden" name="accion" value="agregar">
  <input name="tipo" placeholder="Ej: Notebook, Smartphone, TV..." required>
  <button>Guardar</button>
</form>

<h2>Listado de equipos</h2>
<table>
<tr><th>ID</th><th>Tipo</th><th>Acciones</th></tr>
<?php foreach($dispositivos as $d): ?>
<tr>
  <td><?=$d['id']?></td>
  <td><?=$d['tipo']?></td>
  <td>
    <form method="post" style="display:inline">
      <input type="hidden" name="accion" value="eliminar">
      <input type="hidden" name="id" value="<?=$d['id']?>">
      <button onclick="return confirm('¿Eliminar equipo?')">🗑</button>
    </form>
    <button onclick="editarEquipo(<?=htmlspecialchars(json_encode($d))?>)">✏️</button>
  </td>
</tr>
<?php endforeach; ?>
</table>
</div>

<script>
function editarEquipo(d){
  const form = document.createElement('form');
  form.method='post';
  form.innerHTML = `
  <input type="hidden" name="accion" value="editar">
  <input type="hidden" name="id" value="${d.id}">
  <h3>Editar equipo #${d.id}</h3>
  <input name="tipo" value="${d.tipo}" required>
  <button>Guardar cambios</button>`;
  document.body.innerHTML = '';
  document.body.appendChild(form);
}
</script>
</body>
</html>
