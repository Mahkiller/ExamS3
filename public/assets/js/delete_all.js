document.addEventListener('DOMContentLoaded', () => {
  const delBtn = document.getElementById('delBtn');
  const cancelBtn = document.getElementById('cancelBtn');
  const msg = document.getElementById('msg');
  cancelBtn.addEventListener('click', ()=> { window.location.href = '/'; });
  delBtn.addEventListener('click', async function(){
    const code = document.getElementById('code').value.trim();
    if (!code) { msg.textContent = 'Entrez le code de confirmation.'; return; }
    if (!confirm('Confirmez-vous la suppression de toutes les courses ? Cette action est irréversible.')) return;
    msg.textContent = 'Suppression en cours...';
    try {
      const res = await fetch('/courses/delete-all', {
        method: 'POST',
        body: new URLSearchParams({ code })
      });
      const j = await res.json().catch(()=>null);
      if (!res.ok) throw new Error(j && j.message ? j.message : (res.status + ' ' + res.statusText));
      msg.textContent = 'OK : ' + (j.message || 'Suppression effectuée') + ' — ' + (j.deleted ?? 0) + ' lignes supprimées.';
      setTimeout(()=>{ window.location.href = '/dashboard'; }, 900);
    } catch (err) {
      msg.textContent = 'Erreur : ' + err.message;
    }
  });
});