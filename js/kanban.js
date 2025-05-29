// js/kanban.js

document.addEventListener('DOMContentLoaded', () => {
  function configurarTareasDrag() {
    document.querySelectorAll('.tarea').forEach(tarea => {
      tarea.setAttribute('draggable', 'true');
      tarea.addEventListener('dragstart', e => {
        e.dataTransfer.setData('text/plain', tarea.dataset.id);
      });
    });
  }

  configurarTareasDrag(); // Inicial

  document.querySelectorAll('.columna .tareas').forEach(col => {
    col.addEventListener('dragover', e => e.preventDefault());

    col.addEventListener('drop', e => {
      e.preventDefault();
      const id = e.dataTransfer.getData('text/plain');
      const tarea = document.querySelector(`.tarea[data-id='${id}']`);
      const estado = col.closest(".columna").dataset.estado;

      if (tarea) {
        col.appendChild(tarea);

        if (estado === 'realizado') {
          tarea.classList.add('realizada');
          tarea.setAttribute('draggable', 'false');
        } else {
          tarea.classList.remove('realizada');
          tarea.setAttribute('draggable', 'true');
        }

        fetch('actualizar_estado.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `id=${id}&estado=${estado}`
        });
      }
    });
  });
});
