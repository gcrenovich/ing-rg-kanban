<?php
// public/editar_trabajo.php
require_once __DIR__ . '/../includes/funciones.php';
require_login();

$id = $_GET['id'] ?? null;
if (!$id) {
  echo 'ID requerido';
  exit;
}

$trabajos = leer_json('trabajos.json');
$t = find_by_id($trabajos, $id);
if (!$t) {
  echo 'Trabajo no encontrado';
  exit;
}

$clientes = leer_json('clientes.json');
$dispositivos = leer_json('dispositivos.json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $t['cliente_id'] = $_POST['cliente_id'];
  $t['dispositivo_id'] = $_POST['dispositivo_id'];
  $t['marca'] = trim($_POST['marca']);
  $t['modelo'] = trim($_POST['modelo']);
  $t['problema'] = trim($_POST['problema']);
  $t['estado'] = trim($_POST['estado']);
  $t['tecnico'] = trim($_POST['tecnico']);
  $t['fecha_modificacion'] = date('Y-m-d H:i:s');

  // Guardar cambios
  foreach ($trabajos as &$item) {
    if ($item['id'] == $t['id']) {
      $item = $t;
      break;
    }
  }
  //guardar_json('trabajos.json', $trabajos);
    escribir_json('trabajos.json', $trabajos);
  header("Location: detalles.php?id=" . $t['id']);
  exit;
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Editar trabajo #<?= htmlspecialchars($t['id']) ?></title>
<link rel="stylesheet" href="../assets/css/estilos.css">
<style>
.form-box {
  max-width: 600px;
  margin: 20px auto;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.15);
  padding: 20px;
}
label {
  display: block;
  margin-top: 10px;
  font-weight: 600;
}
input, select, textarea {
  width: 100%;
  padding: 8px;
  margin-top: 4px;
  border-radius: 6px;
  border: 1px solid #ccc;
}
button {
  margin-top: 16px;
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  background: #1d3557;
  color: #fff;
  font-weight: 600;
  cursor: pointer;
}
button:hover { background: #2a9d8f; }
</style>
</head>
<body>

<header class="topbar">
  <div class="logo">Editar Trabajo</div>
  <div class="top-actions">
    <a class="btn" href="detalles.php?id=<?= htmlspecialchars($t['id']) ?>">Volver</a>
  </div>
</header>

<main>
  <div class="form-box">
    <h2>Editar trabajo #<?= htmlspecialchars($t['id']) ?></h2>
    <form method="post">
      <label>Cliente</label>
      <select name="cliente_id" required>
        <option value="">Seleccionar cliente...</option>
        <?php foreach ($clientes as $cli): ?>
          <option value="<?= htmlspecialchars($cli['id']) ?>" <?= ($cli['id'] == $t['cliente_id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($cli['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label>Equipo</label>
      <select name="dispositivo_id" required>
        <option value="">Seleccionar equipo...</option>
        <?php foreach ($dispositivos as $d): ?>
          <option value="<?= htmlspecialchars($d['id']) ?>" <?= ($d['id'] == $t['dispositivo_id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($d['tipo']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label>Marca</label>
      <input type="text" name="marca" value="<?= htmlspecialchars($t['marca']) ?>" required>

      <label>Modelo</label>
      <input type="text" name="modelo" value="<?= htmlspecialchars($t['modelo']) ?>" required>

      <label>Problema</label>
      <textarea name="problema" required><?= htmlspecialchars($t['problema']) ?></textarea>

      <label>Estado</label>
      <select name="estado" required>
        <?php
        $estados = ['Pendiente', 'En proceso', 'Finalizado', 'Entregado', 'Guardado', 'Cancelado'];
        foreach ($estados as $e):
        ?>
          <option value="<?= $e ?>" <?= ($t['estado'] == $e) ? 'selected' : '' ?>><?= $e ?></option>
        <?php endforeach; ?>
      </select>

      <label>Técnico</label>
      <input type="text" name="tecnico" value="<?= htmlspecialchars($t['tecnico'] ?? '') ?>">

      <button type="submit">Guardar cambios</button>
    </form>
  </div>
</main>

</body>
</html>
