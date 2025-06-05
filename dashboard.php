<?php
require 'db.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
  header('Location: login.php');
  exit;
}

$sector_id = $_SESSION['sector_id'];
$hoy = date('Y-m-d');

try {
  $stmt = $conn->prepare("SELECT t.*, u.nombre AS usuario_asignado, u.equipo AS equipo_usuario
                          FROM tareas t
                          JOIN usuarios u ON t.usuario_id = u.id
                          WHERE t.sector_id = ?
                          ORDER BY t.fecha_creacion DESC");
  $stmt->execute([$sector_id]);
  $tareas = $stmt->fetchAll();
} catch (PDOException $e) {
  error_log("Error al obtener tareas: " . $e->getMessage());
  $tareas = [];
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<h2 style="text-align:center;">Tablero Kanban</h2>

<!-- Botón para ocultar/mostrar columna Realizado -->
<div class="text-center mb-3">
  <button id="toggleRealizadas" class="btn btn-secondary">Ocultar realizadas</button>
</div>

<!-- Filtros -->
<div class="container mb-4">
  <div class="row g-3">
    <div class="col-md-4">
      <label>Filtrar por urgencia</label>
      <select id="filtro-urgencia" class="form-select">
        <option value="">Todas</option>
        <option value="alta">Alta</option>
        <option value="media">Media</option>
        <option value="baja">Baja</option>
      </select>
    </div>
    <div class="col-md-4">
      <label>Filtrar por usuario</label>
      <input type="text" id="filtro-usuario" class="form-control" placeholder="Nombre del usuario">
    </div>
    <div class="col-md-4">
      <label>Filtrar por título</label>
      <input type="text" id="filtro-titulo" class="form-control" placeholder="Buscar por título">
    </div>
  </div>
</div>

<div class="kanban">
  <?php
  $estados = ['pendiente' => 'Pendiente', 'proceso' => 'En Proceso', 'realizado' => 'Realizado'];
  foreach ($estados as $estado_key => $estado_label):
    // Agregamos un ID a la columna "realizado" para ocultarla
    $columna_id = $estado_key === 'realizado' ? 'id="columna-realizado"' : '';
  ?>
    <div class="columna columna-<?= $estado_key ?>" data-estado="<?= $estado_key ?>" <?= $columna_id ?>>
      <h3><?= $estado_label ?></h3>
      <div class="tareas">
        <?php foreach ($tareas as $t): if ($t['estado'] === $estado_key): ?>
          <div
            class="tarea <?= $estado_key === 'realizado' ? 'realizada' : '' ?> urgencia-<?= $t['urgencia'] ?>"
            draggable="true"
            data-id="<?= $t['id'] ?>"
            data-urgencia="<?= $t['urgencia'] ?>"
            data-usuario="<?= strtolower(htmlspecialchars($t['usuario_asignado'])) ?>"
            data-titulo="<?= htmlspecialchars($t['titulo']) ?>"
            data-descripcion="<?= htmlspecialchars($t['descripcion']) ?>"
            data-fecha_inicio="<?= $t['fecha_inicio'] ?>"
            data-fecha_fin="<?= $t['fecha_fin'] ?>"
            onclick="mostrarDescripcion(this)"
          >
            <strong><?= htmlspecialchars($t['titulo']) ?></strong><br>
            <small><?= htmlspecialchars($t['usuario_asignado']) ?></small>
          </div>
        <?php endif; endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<!-- Modal de Descripción -->
<div class="modal fade" id="modalDescripcion" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detalles de la Tarea</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body" id="contenido-descripcion">
        <!-- Se llena dinámicamente -->
      </div>
    </div>
  </div>
</div>

<script>
  window.rol = '<?= $_SESSION['rol'] ?>';
</script>
<script src="js/kanban.js"></script>

<script>
  // Filtros
  const filtroUrgencia = document.getElementById('filtro-urgencia');
  const filtroUsuario = document.getElementById('filtro-usuario');
  const filtroTitulo = document.getElementById('filtro-titulo');

  function aplicarFiltros() {
    const urgencia = filtroUrgencia.value;
    const usuario = filtroUsuario.value.toLowerCase();
    const titulo = filtroTitulo.value.toLowerCase();

    document.querySelectorAll('.tarea').forEach(tarea => {
      const tareaUrgencia = tarea.dataset.urgencia;
      const tareaUsuario = tarea.dataset.usuario;
      const tareaTitulo = tarea.dataset.titulo.toLowerCase();

      const coincideUrgencia = !urgencia || tareaUrgencia === urgencia;
      const coincideUsuario = !usuario || tareaUsuario.includes(usuario);
      const coincideTitulo = !titulo || tareaTitulo.includes(titulo);

      tarea.style.display = (coincideUrgencia && coincideUsuario && coincideTitulo) ? '' : 'none';
    });
  }

  filtroUrgencia.addEventListener('change', aplicarFiltros);
  filtroUsuario.addEventListener('input', aplicarFiltros);
  filtroTitulo.addEventListener('input', aplicarFiltros);

  // Mostrar detalles en modal al hacer clic en una tarea
  function mostrarDescripcion(tarea) {
    const titulo = tarea.dataset.titulo || 'Sin título';
    const desc = tarea.dataset.descripcion || 'Sin descripción';
    const inicio = tarea.dataset.fecha_inicio || '—';
    const fin = tarea.dataset.fecha_fin || '—';

    const contenido = `
      <h5 class="mb-2">${titulo}</h5>
      <p><strong>Descripción:</strong> ${desc}</p>
      <p><strong>Inicio:</strong> ${inicio}</p>
      <p><strong>Fin:</strong> ${fin}</p>
    `;
    document.getElementById('contenido-descripcion').innerHTML = contenido;
    new bootstrap.Modal(document.getElementById('modalDescripcion')).show();
  }

  // Toggle mostrar/ocultar columna de realizadas
  const btnToggle = document.getElementById('toggleRealizadas');
  const columnaRealizado = document.getElementById('columna-realizado');
  let visible = true;

  btnToggle.addEventListener('click', () => {
    visible = !visible;
    columnaRealizado.style.display = visible ? '' : 'none';
    btnToggle.textContent = visible ? 'Ocultar realizadas' : 'Mostrar realizadas';
  });
</script>

<!-- Bootstrap para el modal -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'includes/footer.php'; ?>
