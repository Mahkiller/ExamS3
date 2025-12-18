document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.collapsible').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-target');
      const tbl = document.getElementById(id);
      if (!tbl) return;
      if (tbl.style.display === 'none' || getComputedStyle(tbl).display === 'none') {
        tbl.style.display = 'block';
      } else {
        tbl.style.display = 'none';
      }
    });
  });

  window.validateCourse = async function(id) {
    if (!confirm('Valider la course ? (une fois validée, elle ne sera plus modifiable)')) return;
    try {
      const res = await fetch(`/courses/validate/${id}`, { method: 'POST' });
      if (!res.ok) {
        const err = await res.json().catch(()=>({message:'Erreur serveur'}));
        throw new Error(err.message || 'Erreur serveur');
      }
      const result = await res.json().catch(()=>null);
      if (result && result.success) {
        location.reload();
      } else {
        throw new Error((result && result.message) ? result.message : 'Échec');
      }
    } catch (e) {
      alert('Erreur: ' + e.message);
    }
  };

  window.cancelCourse = async function(id) {
    if (!confirm('Annuler (supprimer) cette course ?')) return;
    try {
      const res = await fetch(`/courses/delete/${id}`, { method: 'POST' });
      if (!res.ok) {
        const err = await res.json().catch(()=>({message:'Erreur serveur'}));
        throw new Error(err.message || 'Erreur serveur');
      }
      const j = await res.json().catch(()=>null);
      if (j && j.success) {
        location.reload();
      } else {
        throw new Error((j && j.message) ? j.message : 'Échec suppression');
      }
    } catch (e) {
      alert('Erreur: ' + e.message);
    }
  };
});