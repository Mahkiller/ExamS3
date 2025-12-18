document.addEventListener('DOMContentLoaded', () => {
  const tbody = document.querySelector('#coursesTable tbody');
  const statusLine = document.getElementById('statusLine');

  async function loadCourses(){
    tbody.innerHTML = '<tr><td colspan="9">Chargement...</td></tr>';
    statusLine.textContent = '';
    try {
      const res = await fetch('/courses', { method: 'GET', credentials: 'same-origin' });
      const j = await res.json().catch(()=>null);
      if (!res.ok) {
        const msg = (j && j.message) ? j.message : res.status + ' ' + res.statusText;
        tbody.innerHTML = `<tr><td colspan="9">Erreur: ${escapeHtml(msg)}</td></tr>`;
        statusLine.textContent = 'Erreur récupération courses: ' + msg;
        return;
      }
      const data = Array.isArray(j.data) ? j.data : [];
      if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9">Aucune course</td></tr>';
        return;
      }
      tbody.innerHTML = '';
      data.forEach(c => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${escapeHtml(c.date_course||'')}</td>
                        <td>${escapeHtml((c.heure_debut||'') + (c.heure_fin? ' — '+c.heure_fin:''))}</td>
                        <td>${escapeHtml(c.conducteur_nom||c.conducteur||'')}</td>
                        <td>${escapeHtml(c.moto_immat||c.moto||'')}</td>
                        <td>${escapeHtml(c.client_nom||c.client||'')}</td>
                        <td>${Number(c.km||0).toFixed(2)}</td>
                        <td><strong>${Number(c.montant||0).toFixed(2)}</strong></td>
                        <td>${c.valide==1? 'Oui':'Non'}</td>
                        <td>
                          ${c.valide==1 ? '' : `<button class="action-btn validate" onclick="validate(${c.id})">Valider</button><button class="action-btn danger" onclick="cancel(${c.id})">Annuler</button>`}
                          <a class="action-btn view" href="/ui/course/${c.id}">Modifier</a>
                          <a class="action-btn" href="/ui/course-price/${c.id}" style="background:#ff9f1c">Prix essence</a>
                        </td>`;
        tbody.appendChild(tr);
      });
    } catch (err) {
      tbody.innerHTML = `<tr><td colspan="9">Erreur: ${escapeHtml(String(err))}</td></tr>`;
      statusLine.textContent = 'Erreur réseau: ' + err.message;
    }
  }

  document.getElementById('createForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const msgEl = document.getElementById('createMsg');
    msgEl.textContent = 'Envoi...';
    const formData = new URLSearchParams(new FormData(this));
    try {
      const res = await fetch('/courses', { method: 'POST', body: formData });
      const j = await res.json().catch(()=>null);
      if (!res.ok) throw new Error((j && j.message) ? j.message : res.status + ' ' + res.statusText);
      msgEl.textContent = 'Créé.';
      this.reset();
      await loadCourses();
    } catch (err) {
      msgEl.textContent = 'Erreur: ' + err.message;
    }
  });

  document.getElementById('clearBtn').addEventListener('click', function(){
    document.getElementById('createForm').reset();
    document.getElementById('createMsg').textContent = 'Annulé.';
  });

  window.validate = async function(id){
    if(!confirm('Valider la course ? (une fois validée, non modifiable)')) return;
    try {
      const res = await fetch(`/courses/validate/${id}`, { method: 'POST' });
      if (!res.ok) throw new Error('Erreur serveur');
      await loadCourses();
    } catch (err) { alert('Erreur: '+err.message); console.error(err); }
  };
  window.cancel = async function(id){
    if(!confirm('Annuler (supprimer) cette course ?')) return;
    try {
      const res = await fetch(`/courses/delete/${id}`, { method: 'POST' });
      if (!res.ok) throw new Error('Erreur serveur');
      await loadCourses();
    } catch (err) { alert('Erreur: '+err.message); console.error(err); }
  };

  function escapeHtml(s){ return String(s).replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;'); }

  loadCourses();
});