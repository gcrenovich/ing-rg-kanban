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

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // actualizar valores desde POST
  $t['cliente_id'] = $_POST['cliente_id'] ?? $t['cliente_id'];
  $t['dispositivo_id'] = $_POST['dispositivo_id'] ?? $t['dispositivo_id'];
  $t['marca'] = trim($_POST['marca'] ?? $t['marca']);
  $t['modelo'] = trim($_POST['modelo'] ?? $t['modelo']);
  $t['problema'] = trim($_POST['problema'] ?? $t['problema']);
  $t['estado'] = trim($_POST['estado'] ?? $t['estado']);
  $t['tecnico'] = trim($_POST['tecnico'] ?? $t['tecnico']);
  $t['fecha_modificacion'] = date('Y-m-d H:i:s');

  // grabar en array trabajos
  foreach ($trabajos as &$item) {
    if ((string)$item['id'] === (string)$t['id']) {
      $item = $t;
      break;
    }
  }
  // escribir JSON (usa la función que ya tienes)
  escribir_json('trabajos.json', $trabajos);

  // redirigir al detalle
  header("Location: detalles.php?id=" . urlencode($t['id']));
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
/* Estilos coherentes con index.php / registrar.php */
body { font-family: Arial, sans-serif; background: #f4f6f8; margin: 0; color:#333; }
header.topbar { background: #1e3a8a; color: #fff; display:flex; justify-content:space-between; align-items:center; padding:12px 20px; }
header .logo { font-weight:bold; font-size:1.1em; }
header .top-actions a { margin-left:8px; text-decoration:none; padding:8px 10px; border-radius:6px; background:#2563eb; color:#fff; }
header .top-actions a.logout { background:#e63946; }
.main-wrap { max-width:920px; margin:20px auto; padding:16px; }
.card { background:#fff; border-radius:10px; padding:18px; box-shadow:0 2px 8px rgba(0,0,0,0.08); }
h2 { color:#1e3a8a; margin:0 0 12px 0; }
.form-row { display:flex; gap:12px; }
.form-col { flex:1; }
label { display:block; margin-top:10px; font-weight:600; color:#334155; }
input, select, textarea { width:100%; padding:10px; margin-top:6px; border:1px solid #d1d5db; border-radius:8px; box-sizing:border-box; }
textarea { min-height:110px; resize:vertical; }
.actions { margin-top:14px; display:flex; gap:8px; flex-wrap:wrap; }
.btn { background:#2563eb; color:#fff; padding:10px 14px; border-radius:8px; border:none; cursor:pointer; text-decoration:none; display:inline-block; }
.btn.secondary { background:#6b7280; }
.btn.danger { background:#dc2626; }
.small-note { font-size:13px; color:#6b7280; margin-top:8px; }
@media (max-width:720px){
  .form-row{flex-direction:column;}
}
</style>
</head>
<body>

<header class="topbar">
  <div class="logo">Editar Trabajo</div>
  <div class="top-actions">
    <a href="index.php" class="btn">🏠 Inicio</a>
    <a href="registrar.php" class="btn">➕ Nuevo ingreso</a>
    <a href="logout.php" class="btn logout">Salir</a>
  </div>
</header>

<main class="main-wrap">
  <div class="card">
    <h2>Editar trabajo — <?= htmlspecialchars($t['comprobante_id'] ?? ('ID ' . $t['id'])) ?></h2>

    <?php if ($mensaje): ?>
      <div class="small-note"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="form-row">
        <div class="form-col">
          <label for="cliente_id">Cliente</label>
          <select name="cliente_id" id="cliente_id" required>
            <option value="">-- Seleccionar cliente --</option>
            <?php foreach ($clientes as $cli): ?>
              <option value="<?= htmlspecialchars($cli['id']) ?>" <?= ((string)$cli['id'] === (string)$t['cliente_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cli['nombre'] . ' - ' . ($cli['telefono'] ?? '')) ?>
              </option>
            <?php endforeach; ?>
          </select>

          <label for="dispositivo_id">Tipo de equipo</label>
          <select name="dispositivo_id" id="dispositivo_id" required>
            <option value="">-- Seleccionar equipo --</option>
            <?php foreach ($dispositivos as $d): ?>
              <option value="<?= htmlspecialchars($d['id']) ?>" <?= ((string)$d['id'] === (string)$t['dispositivo_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($d['tipo']) ?>
              </option>
            <?php endforeach; ?>
          </select>

          <label for="marca">Marca</label>
          <input type="text" name="marca" id="marca" value="<?= htmlspecialchars($t['marca'] ?? '') ?>">

          <label for="modelo">Modelo</label>
          <input type="text" name="modelo" id="modelo" value="<?= htmlspecialchars($t['modelo'] ?? '') ?>">
        </div>

        <div class="form-col">
          <label for="problema">Problema / Observaciones</label>
          <textarea name="problema" id="problema"><?= htmlspecialchars($t['problema'] ?? '') ?></textarea>

          <label for="tecnico">Técnico</label>
          <input type="text" name="tecnico" id="tecnico" value="<?= htmlspecialchars($t['tecnico'] ?? '') ?>">

          <label for="estado">Estado</label>
          <select name="estado" id="estado" required>
            <?php
            $estados = ['Pendiente', 'En proceso', 'Finalizado', 'Entregado', 'Guardado', 'Cancelado'];
            foreach ($estados as $e): ?>
              <option value="<?= $e ?>" <?= ((string)$t['estado'] === (string)$e) ? 'selected' : '' ?>><?= $e ?></option>
            <?php endforeach; ?>
          </select>

          <div class="small-note">Fecha ingreso: <?= htmlspecialchars($t['fecha_ingreso'] ?? '') ?></div>
        </div>
      </div>

      <div class="actions">
        <button type="submit" class="btn">💾 Guardar cambios</button>
        <a href="detalles.php?id=<?= urlencode($t['id']) ?>" class="btn secondary">Cancelar</a>
        <a href="acciones.php?action=eliminar&id=<?= urlencode($t['id']) ?>" class="btn danger" onclick="return confirm('¿Eliminar este trabajo?')">🗑 Eliminar</a>
      </div>
    </form>
  </div>
</main>

</body>
</html>
