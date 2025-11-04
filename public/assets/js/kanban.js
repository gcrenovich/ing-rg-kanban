// assets/js/kanban.js
document.addEventListener('DOMContentLoaded', ()=>{
  let dragging = null;

  document.querySelectorAll('.kanban-card').forEach(card=>{
    card.addEventListener('dragstart', ()=>{ dragging = card; card.classList.add('dragging'); });
    card.addEventListener('dragend', ()=>{ if(card) card.classList.remove('dragging'); dragging = null; });
  });

  document.querySelectorAll('.kanban-column').forEach(col=>{
    col.addEventListener('dragover', e=>{ e.preventDefault(); });
    col.addEventListener('drop', async e=>{
      e.preventDefault();
      if (!dragging) return;
      col.querySelector('.kanban-cards').appendChild(dragging);
      const id = dragging.dataset.id;
      const nuevoEstado = col.dataset.estado;
      // POST a api/trabajos.php para actualizar estado
      try {
        const fd = new FormData();
        fd.append('action','update_estado');
        fd.append('id', id);
        fd.append('estado', nuevoEstado);
        const res = await fetch('../api/trabajos.php', { method:'POST', body: fd });
        const j = await res.json();
        if (!j.ok) alert('Error al actualizar estado');
      } catch (err) {
        console.error(err); alert('Error comunicando con el servidor');
      }
    });
  });
});
