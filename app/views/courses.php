<?php
// Vue UI pour lister et créer des courses
if (!class_exists('Flight')) { 
    echo 'Flight non disponible.'; 
    exit; 
}
function e($v){ 
    return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); 
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Courses — Gestion</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="/assets/welcome.css">
</head>
<body>
  <div class="container">
    <div class="header">
      <div>
        <div class="title">Gestion des courses</div>
        <div class="small">Créer, modifier et supprimer des courses</div>
      </div>
      <div class="links">
        <a href="/">Retour au tableau</a>
      </div>
    </div>

    <div class="card" id="createCard">
      <h3>Créer une course</h3>
      <form id="createForm">
        <div style="margin-bottom: 15px;">
          <label style="display: block; margin-bottom: 5px; font-weight: bold;">Date</label>
          <input name="date_course" type="date" required style="padding: 8px; width: 200px;" />
        </div>
        
        <div style="display: flex; gap: 15px; margin-bottom: 15px; flex-wrap: wrap;">
          <div>
            <label style="display: block; margin-bottom: 5px;">Heure début</label>
            <input name="heure_debut" type="time" style="padding: 8px;" />
          </div>
          <div>
            <label style="display: block; margin-bottom: 5px;">Heure fin</label>
            <input name="heure_fin" type="time" style="padding: 8px;" />
          </div>
          <div>
            <label style="display: block; margin-bottom: 5px;">KM</label>
            <input name="km" type="number" step="0.01" placeholder="km" required style="padding: 8px; width: 100px;" />
          </div>
          <div>
            <label style="display: block; margin-bottom: 5px;">Montant (Ar)</label>
            <input name="montant" type="number" step="0.01" placeholder="montant" required style="padding: 8px; width: 150px;" />
          </div>
        </div>
        
        <div style="display: flex; gap: 15px; margin-bottom: 15px; flex-wrap: wrap;">
          <div style="flex: 1;">
            <label style="display: block; margin-bottom: 5px;">Départ</label>
            <input name="depart" placeholder="Lieu de départ" style="padding: 8px; width: 100%;" />
          </div>
          <div style="flex: 1;">
            <label style="display: block; margin-bottom: 5px;">Arrivée</label>
            <input name="arrivee" placeholder="Lieu d'arrivée" style="padding: 8px; width: 100%;" />
          </div>
        </div>
        
        <div style="display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap;">
          <div>
            <label style="display: block; margin-bottom: 5px;">Conducteur ID</label>
            <input name="conducteur_id" type="number" placeholder="ID conducteur" required style="padding: 8px; width: 120px;" />
          </div>
          <div>
            <label style="display: block; margin-bottom: 5px;">Moto ID</label>
            <input name="moto_id" type="number" placeholder="ID moto" required style="padding: 8px; width: 120px;" />
          </div>
          <div>
            <label style="display: block; margin-bottom: 5px;">Client ID (optionnel)</label>
            <input name="client_id" type="number" placeholder="ID client" style="padding: 8px; width: 120px;" />
          </div>
        </div>
        
        <div style="display: flex; gap: 10px; align-items: center;">
          <button class="action-btn" type="submit" style="background: #0f62fe;">Créer la course</button>
          <span id="createMsg" class="small"></span>
        </div>
      </form>
    </div>

    <div class="card">
      <h3>Liste des courses</h3>
      <table class="table" id="coursesTable" aria-live="polite">
        <thead>
          <tr>
            <th>Date</th>
            <th>Conducteur</th>
            <th>Moto</th>
            <th>KM</th>
            <th>Montant</th>
            <th>Départ → Arrivée</th>
            <th>Validée</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

  <script>
    async function loadCourses() {
      const tbody = document.querySelector('#coursesTable tbody');
      tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 20px;">Chargement...</td></tr>';
      
      try {
        const res = await fetch('/courses');
        if (!res.ok) {
          const error = await res.json();
          throw new Error(error.error || 'Erreur de chargement');
        }
        
        const data = await res.json();
        
        if (!data.length) {
          tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 20px;">Aucune course</td></tr>';
          return;
        }
        
        tbody.innerHTML = '';
        data.forEach(c => {
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${escapeHtml(c.date_course || '')}</td>
            <td>${escapeHtml(c.conducteur_nom || 'ID: ' + c.conducteur_id)}</td>
            <td>${escapeHtml(c.moto_immat || 'ID: ' + c.moto_id)}</td>
            <td>${Number(c.km || 0).toFixed(2)}</td>
            <td>${Number(c.montant || 0).toFixed(2)} Ar</td>
            <td>${escapeHtml(c.depart || '')} → ${escapeHtml(c.arrivee || '')}</td>
            <td>${c.valide == 1 ? '✅ Oui' : '❌ Non'}</td>
            <td style="white-space: nowrap;">
              <a class="action-btn view" href="/ui/course/${c.id}" style="margin-right: 5px;">Modifier</a>
              ${c.valide == 0 ? `<button class="action-btn validate" onclick="validate(${c.id})" style="margin-right: 5px;">Valider</button>` : ''}
              ${c.valide == 0 ? `<button class="action-btn danger" onclick="del(${c.id})">Supprimer</button>` : ''}
            </td>
          `;
          tbody.appendChild(tr);
        });
      } catch (err) {
        tbody.innerHTML = `<tr><td colspan="8" style="color: #dc2626; text-align: center; padding: 20px;">Erreur: ${escapeHtml(String(err.message))}</td></tr>`;
      }
    }

    document.getElementById('createForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      const data = Object.fromEntries(formData.entries());
      
      // Nettoyer les valeurs
      Object.keys(data).forEach(key => {
        if (data[key] === '') data[key] = null;
      });
      
      const btnMsg = document.getElementById('createMsg');
      btnMsg.textContent = 'Envoi...';
      btnMsg.style.color = '#666';
      
      try {
        const res = await fetch('/courses/create', { 
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: new URLSearchParams(data)
        });
        
        const result = await res.json();
        
        if (!res.ok) {
          throw new Error(result.error || 'Erreur création');
        }
        
        btnMsg.textContent = '✓ Course créée avec succès !';
        btnMsg.style.color = '#16a34a';
        this.reset();
        loadCourses();
        
        // Réinitialiser le message après 3 secondes
        setTimeout(() => {
          btnMsg.textContent = '';
        }, 3000);
      } catch (err) {
        btnMsg.textContent = '✗ Erreur: ' + err.message;
        btnMsg.style.color = '#dc2626';
      }
    });

    async function validate(id) {
      if (!confirm('Valider la course ? (une fois validée, elle ne sera plus modifiable)')) return;
      
      try {
        const res = await fetch(`/courses/validate/${id}`, { 
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          }
        });
        
        if (!res.ok) {
          const error = await res.json();
          throw new Error(error.error || 'Erreur de validation');
        }
        
        const result = await res.json();
        if (result.success) {
          alert('Course validée avec succès !');
          loadCourses();
        }
      } catch (err) {
        alert('Erreur: ' + err.message);
      }
    }

    async function del(id) {
      if (!confirm('Supprimer la course ? Cette action est irréversible.')) return;
      
      try {
        const res = await fetch(`/courses/delete/${id}`, { 
          method: 'DELETE'
        });
        
        if (!res.ok) {
          const error = await res.json();
          throw new Error(error.error || 'Erreur de suppression');
        }
        
        const result = await res.json();
        if (result.success) {
          alert('Course supprimée avec succès !');
          loadCourses();
        }
      } catch (err) {
        alert('Erreur: ' + err.message);
      }
    }

    function escapeHtml(s) { 
      return String(s).replace(/[&<>"']/g, function(m) {
        return {
          '&': '&amp;',
          '<': '&lt;',
          '>': '&gt;',
          '"': '&quot;',
          "'": '&#39;'
        }[m];
      });
    }

    // Charger les courses au démarrage
    loadCourses();
  </script>
</body>
</html>