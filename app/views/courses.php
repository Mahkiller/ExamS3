<?php
if (!class_exists('Flight')) { echo 'Flight non disponible.'; exit; }
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Nouvelle course +</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="/assets/welcome.css">
  <link rel="stylesheet" href="/assets/courses.css">
  <style>
    #statusLine{margin-top:10px;color:#a00;font-weight:600}
    #coursesTable td .small-cell{display:block;color:#6b7280;font-size:13px}
    /* quick access floating buttons */
    #floating-actions{position:fixed;right:18px;bottom:18px;z-index:9999}#floating-actions a{display:block;margin-bottom:8px}
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div>
        <h1 class="title">Nouvelle course +</h1>
        <div class="small">Créer une course et voir la liste.</div>
      </div>

      <div class="nav-links">
        <a href="/" class="links">Accueil</a>
        <a href="/dashboard" class="links">Tableau</a>
        <a href="/ui/prix-essence" class="links" style="background:#ff9f1c">Prix essence</a>
        <a href="/ui/delete-all" class="action-btn danger">Supprimer toutes</a>
      </div>
    </div>

    <div class="card" id="createCard" style="margin-bottom:18px">
      <h3>Créer une course</h3>
      <form id="createForm" autocomplete="off">
        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:10px">
          <input name="date_course" type="date" required />
          <input name="heure_debut" type="time" />
          <input name="heure_fin" type="time" />
          <input name="km" type="number" step="0.01" placeholder="km" required style="min-width:120px"/>
          <input name="montant" type="number" step="0.01" placeholder="montant" required style="min-width:120px"/>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:10px">
          <input name="depart" placeholder="Départ" style="flex:1" />
          <input name="arrivee" placeholder="Arrivée" style="flex:1" />
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:10px">
          <input name="conducteur_id" type="number" placeholder="ID conducteur" required style="min-width:140px" />
          <input name="moto_id" type="number" placeholder="ID moto" required style="min-width:140px" />
          <input name="client_id" type="number" placeholder="ID client (optionnel)" style="min-width:140px" />
        </div>
        <div style="display:flex;gap:10px;align-items:center">
          <button class="action-btn validate" type="submit">Créer</button>
          <button id="clearBtn" type="button" class="action-btn danger">Annuler</button>
          <span id="createMsg" class="small" style="margin-left:8px"></span>
        </div>
      </form>
      <div id="statusLine" aria-live="polite"></div>
    </div>

    <div class="card">
      <h3>Liste des courses</h3>
      <table id="coursesTable" class="table" aria-live="polite">
        <thead>
          <tr><th>Date</th><th>Heure</th><th>Conducteur</th><th>Moto</th><th>Client</th><th>KM</th><th>Montant</th><th>Validée</th><th>Actions</th></tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

  <!-- quick access floating buttons -->
  <div id="floating-actions">
    <a href="/ui/delete-all" class="action-btn danger">Supprimer toutes les courses</a>
    <a href="/ui/prix-essence" class="action-btn" style="background:#ff9f1c">Prix essence</a>
    <a href="/" class="action-btn view">Accueil</a>
  </div>

  <script>
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
            console.error('GET /courses →', res.status, j);
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
          console.error(err);
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
  </script>
</body>
</html>