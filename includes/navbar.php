<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold" href="dashboard.php">
      <i class="bi bi-kanban"></i> Kanban
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
     <ul class="navbar-nav">
    <li class="nav-item"><a class="nav-link" href="dashboard.php">Inicio</a></li>
    <li class="nav-item"><a class="nav-link" href="abm_usuarios.php">Usuarios</a></li>
    <li class="nav-item"><a class="nav-link" href="abm_sectores.php">Sectores</a></li>
    <li class="nav-item"><a class="nav-link" href="abm_tareas.php">Tareas</a></li>
    <li class="nav-item"><a class="nav-link" href="reportes.php">Reportes</a></li>

    <?php
    $SECTOR_IT = 2; // Cambiar al ID correcto
    if (isset($_SESSION['sector_id']) && $_SESSION['sector_id'] == $SECTOR_IT):
    ?>
    <li class="nav-item"><a class="nav-link" href="inventario.php">Inventario IT</a></li>
    <?php endif; ?>
º
    <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Salir</a></li>
</ul>

    </div>
  </div>

  
</nav>

