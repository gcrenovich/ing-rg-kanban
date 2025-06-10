// js/kanban.js

document.addEventListener('DOMContentLoaded', () => {
  // Habilitar drag para cada tarea
  document.querySelectorAll('.tarea').forEach(t => {
    t.setAttribute('draggable', 'true');
    t.addEventListener('dragstart', e => {
      e.dataTransfer.setData('text/plain', t.dataset.id);
    });
  });

  // Habilitar drop sobre cada contenedor de tareas
  document.querySelectorAll('.columna .tareas').forEach(col => {
    col.addEventListener('dragover', e => {
      e.preventDefault(); // importante
      col.classList.add('zona-activa'); // opcional: para feedback visual
    });

    col.addEventListener('dragleave', () => {
      col.classList.remove('zona-activa');
    });

    col.addEventListener('drop', e => {
      e.preventDefault();
      col.classList.remove('zona-activa');
      const id = e.dataTransfer.getData('text/plain');
      const tarea = document.querySelector(`.tarea[data-id='${id}']`);
      const estado = col.closest('.columna').dataset.estado;


     if (tarea) {
  const estadoActual = tarea.parentElement.closest('.columna').dataset.estado;

  // Si es usuario, solo puede avanzar (pendiente > proceso > realizado)
  if (window.rol === 'usuario') {
    const orden = ['pendiente', 'proceso', 'realizado'];
    const idxActual = orden.indexOf(estadoActual);
    const idxNuevo = orden.indexOf(estado);

    if (idxNuevo - idxActual !== 1) {
      alert("No tenés permisos para hacer ese movimiento.");
      return;
    }
  }

  col.appendChild(tarea);

  if (estado === 'realizado') {
    tarea.classList.add('realizada');
  } else {
    tarea.classList.remove('realizada');
  }

  fetch('actualizar_estado.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `id=${id}&estado=${estado}`
  }).then(res => res.text()).then(console.log);
}

    });
  });
});
