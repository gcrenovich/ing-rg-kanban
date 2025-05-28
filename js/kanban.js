// js/kanban.js

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.tarea').forEach(t => {
    t.addEventListener('dragstart', e => {
      e.dataTransfer.setData('text/plain', t.dataset.id);
    });
  });

  document.querySelectorAll('.columna .tareas').forEach(col => {
    col.addEventListener('dragover', e => e.preventDefault());

    col.addEventListener('drop', e => {
      e.preventDefault();
      const id = e.dataTransfer.getData('text/plain');
      const tarea = document.querySelector(`.tarea[data-id='${id}']`);
      const estado = col.closest(".columna").dataset.estado;
      col.appendChild(tarea);

      fetch('actualizar_estado.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${id}&estado=${estado}`
      });

      if (estado === 'realizado') tarea.classList.add('realizada');
    });
  });
});
