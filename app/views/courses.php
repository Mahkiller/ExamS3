<?php
// Vue UI pour lister et créer des courses (client-side calls vers les endpoints /courses)
if (!class_exists('Flight')) { echo 'Flight non disponible.'; exit; }
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Courses — Gestion</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body{font-family:Arial,Helvetica,sans-serif;padding:18px;background:#f6f8fb}
    .wrap{max-width:1100px;margin:0 auto}
    h1{margin:0 0 12px}
    .card{background:#fff;padding:16px;border-radius:8px;box-shadow:0 6px 18px rgba(2,6,23,0.06);margin-bottom:12px}
    table{width:100%;border-collapse:collapse}
    th,td{padding:10px;border-bottom:1px solid #eee;text-align:left}
    thead th{background:#0f172a;color:#fff}
    .btn{padding:6px 10px;border-radius:6px;border:0;color:#fff;cursor:pointer}
    .btn.primary{background:#0f62fe}
    .btn.warn{background:#f59e0b}
    .btn.danger{background:#dc2626}
    form .row{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:8px}
    input,select{padding:8px;border:1px solid #ddd;border-radius:6px}
    .small{font-size:13px;color:#666}
    @media(max-width:720px){ .row{flex-direction:column} }
  </style>
</head>
<body>
  <div class="wrap">
    <h1>Gestion des courses</h1>

    <div class="card" id="createCard">
      <h3>Créer une course</h3>
      <form id="createForm">
        <div class="row">
          <input name="date_course" type="date" required />
          <input name="heure_debut" type="time" />
          <input name="heure_fin" type="time" />
          <input name="km" type="number" step="0.01" placeholder="km" required />
          <input name="montant" type="number" step="0.01" placeholder="montant" required />
        </div>
        <div class="row">
          <input name="depart" placeholder="Départ" />
          <input name="arrivee" placeholder="Arrivée" />
        </div>
        <div class="row">
          <input name="conducteur_id" type="number" placeholder="ID conducteur" required />
          <input name="moto_id" type="number" placeholder="ID moto" required />
          <input name="client_id" type="number" placeholder="ID client (optionnel)" />
        </div>
        <div class="row">
          <button class="btn primary" type="submit">Créer</button>
          <span id="createMsg" class="small"></span>
        </div>
      </form>
    </div>

    <div class="card">
      <h3>Liste des courses</h3>
      <table id="coursesTable" aria-live="polite">
        <thead>
          <tr><th>Date</th><th>Conducteur</th><th>Moto</th><th>KM</th><th>Montant</th><th>Départ → Arrivée</th><th>Validée</th><th>Actions</th></tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

  <script>
    async function loadCourses(){
      const t = document.querySelector('#coursesTable tbody');
      t.innerHTML = '<tr><td colspan="8">Chargement...</td></tr>';
      try {
        const res = await fetch('/courses');
        if (!res.ok) throw new Error(await res.text());
        const data = await res.json();
        if (!data.length) { t.innerHTML = '<tr><td colspan="8">Aucune course</td></tr>'; return; }
        t.innerHTML = '';
        data.forEach(c => {
          const tr = document.createElement('tr');
          tr.innerHTML = `<td>${escapeHtml(c.date_course||c.date||'')}</td>
                          <td>${escapeHtml(c.conducteur||'')}</td>
                          <td>${escapeHtml(c.moto||'')}</td>
                          <td>${Number(c.km||0).toFixed(2)}</td>
                          <td>${Number(c.montant||0).toFixed(2)}</td>
                          <td>${escapeHtml(c.depart||'')} → ${escapeHtml(c.arrivee||'')}</td>
                          <td>${c.valide==1? 'Oui':'Non'}</td>
                          <td>
                            <a class="btn" style="background:#0369a1;color:#fff;padding:6px 8px;border-radius:6px;text-decoration:none" href="/ui/course/${c.id}">Modifier</a>
                            <button class="btn warn" onclick="validate(${c.id})">Valider</button>
                            <button class="btn danger" onclick="del(${c.id})">Suppr</button>
                          </td>`;
          t.appendChild(tr);
        });
      } catch (err) {
        t.innerHTML = `<tr><td colspan="8">Erreur: ${escapeHtml(String(err))}</td></tr>`;
      }
    }

    document.getElementById('createForm').addEventListener('submit', async function(e){
      e.preventDefault();
      const f = new FormData(this);
      const obj = Object.fromEntries(f.entries());
      const btnMsg = document.getElementById('createMsg');
      btnMsg.textContent = 'Envoi...';
      try {
        const res = await fetch('/courses/create', { method: 'POST', body: new URLSearchParams(obj) });
        const j = await res.json();
        if (!res.ok) throw new Error(j.message || 'Erreur création');
        btnMsg.textContent = 'Créé.';
        this.reset();
        loadCourses();
      } catch (err) {
        btnMsg.textContent = 'Erreur: ' + err.message;
      }
    });

    async function validate(id){
      if(!confirm('Valider la course ? (une fois validée, non modifiable)')) return;
      await fetch(`/courses/validate/${id}`, { method: 'POST' });
      loadCourses();
    }
    async function del(id){
      if(!confirm('Supprimer la course ?')) return;
      await fetch(`/courses/delete/${id}`, { method: 'DELETE' });
      loadCourses();
    }
    function escapeHtml(s){ return String(s).replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;'); }

    loadCourses();
  </script>
</body>
</html>