document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('prixForm');
  if (!form) return;
  const id = String(form.dataset.id || '');
  const msg = document.getElementById('msg');

  form.addEventListener('submit', async function(e){
    e.preventDefault();
    const prix = document.getElementById('prix').value;
    msg.textContent = 'Enregistrement...';
    try {
      const res = await fetch(`/courses/price/${encodeURIComponent(id)}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ prix })
      });
      const j = await res.json().catch(()=>null);
      if (!res.ok) throw new Error((j && j.message) ? j.message : `${res.status} ${res.statusText}`);
      msg.textContent = 'Prix enregistré.';
      setTimeout(()=>{ window.location.href = '/dashboard'; }, 700);
    } catch (err) {
      msg.textContent = 'Erreur: ' + err.message;
    }
  });

  const resetBtn = document.getElementById('resetBtn');
  if (resetBtn) {
    resetBtn.addEventListener('click', async function(){
      if (!confirm('Supprimer le prix local pour cette course ? Le prix global sera utilisé.')) return;
      msg.textContent = 'Suppression...';
      try {
        // use POST compatibility endpoint
        const res = await fetch(`/courses/price/delete/${encodeURIComponent(id)}`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        });
        const j = await res.json().catch(()=>null);
        if (!res.ok) throw new Error((j && j.message) ? j.message : `${res.status} ${res.statusText}`);
        msg.textContent = 'Prix local supprimé.';
        setTimeout(()=>{ window.location.href = '/dashboard'; }, 600);
      } catch (err) {
        msg.textContent = 'Erreur: ' + err.message;
      }
    });
  }
});