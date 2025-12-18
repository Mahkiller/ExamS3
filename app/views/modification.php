<?php
// Vue pour modifier une course
if (!class_exists('Flight')) { 
    echo 'Flight non disponible.'; 
    exit; 
}

$id = isset($id) ? (int)$id : 0;
function e($v){ 
    return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); 
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Modifier la course #<?= e($id) ?></title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="/assets/welcome.css">
</head>
<body>
  <div class="container">
    <div class="header">
      <div>
        <div class="title">Modifier la course #<?= e($id) ?></div>
        <div class="small">Modifiez les informations de la course</div>
      </div>
      <div class="links">
        <a href="/ui/courses">← Retour à la liste</a>
      </div>
    </div>
    
    <div class="card">
      <div id="status" class="small" style="margin-bottom: 15px;"></div>
      <form id="editForm">
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
        </div>
        
        <div style="display: flex; gap: 15px; margin-bottom: 15px; flex-wrap: wrap;">
          <div>
            <label style="display: block; margin-bottom: 5px;">KM</label>
            <input name="km" type="number" step="0.01" required style="padding: 8px; width: 100px;" />
          </div>
          <div>
            <label style="display: block; margin-bottom: 5px;">Montant (Ar)</label>
            <input name="montant" type="number" step="0.01" required style="padding: 8px; width: 150px;" />
          </div>
        </div>
        
        <div style="margin-bottom: 15px;">
          <label style="display: block; margin-bottom: 5px;">Départ</label>
          <input name="depart" style="padding: 8px; width: 100%;" />
        </div>
        
        <div style="margin-bottom: 15px;">
          <label style="display: block; margin-bottom: 5px;">Arrivée</label>
          <input name="arrivee" style="padding: 8px; width: 100%;" />
        </div>
        
        <div style="display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap;">
          <div>
            <label style="display: block; margin-bottom: 5px;">Conducteur ID</label>
            <input name="conducteur_id" type="number" required style="padding: 8px; width: 120px;" />
          </div>
          <div>
            <label style="display: block; margin-bottom: 5px;">Moto ID</label>
            <input name="moto_id" type="number" required style="padding: 8px; width: 120px;" />
          </div>
          <div>
            <label style="display: block; margin-bottom: 5px;">Client ID (optionnel)</label>
            <input name="client_id" type="number" style="padding: 8px; width: 120px;" />
          </div>
        </div>
        
        <div style="display: flex; gap: 10px;">
          <button class="action-btn" type="submit" style="background: #0f62fe;">Enregistrer</button>
          <a class="action-btn" href="/ui/courses" style="background: #6b7280; text-decoration: none;">Annuler</a>
        </div>
      </form>
    </div>
  </div>

  <script>
    const id = <?= json_encode($id) ?>;
    const status = document.getElementById('status');
    const form = document.getElementById('editForm');

    async function load() {
      try {
        status.textContent = 'Chargement...';
        status.style.color = '#666';

        const res = await fetch(`/courses/${id}`);
        const j = await res.json().catch(()=>null);

        if (!res.ok) {
          const msg = (j && (j.message || j.error)) ? (j.message || j.error) : `${res.status} ${res.statusText}`;
          throw new Error(msg || 'Erreur de chargement');
        }

        if (!j || j.success !== true || !j.data) {
          const msg = j && (j.message || j.error) ? (j.message || j.error) : 'Données introuvables';
          throw new Error(msg);
        }

        const c = j.data; // <-- use the payload returned by the API

        // Remplir le formulaire
        form.elements['date_course'].value = c.date_course || '';
        form.elements['heure_debut'].value = c.heure_debut || '';
        form.elements['heure_fin'].value = c.heure_fin || '';
        form.elements['km'].value = c.km ?? '';
        form.elements['montant'].value = c.montant ?? '';
        form.elements['depart'].value = c.depart || '';
        form.elements['arrivee'].value = c.arrivee || '';
        form.elements['conducteur_id'].value = c.conducteur_id ?? '';
        form.elements['moto_id'].value = c.moto_id ?? '';
        form.elements['client_id'].value = c.client_id ?? '';

        if (Number(c.valide) === 1) {
          status.textContent = '⚠️ Cette course est déjà validée — modification interdite.';
          status.style.color = '#dc2626';
          form.style.display = 'none';
        } else {
          status.textContent = '';
          form.style.display = 'block';
        }
      } catch (err) {
        status.textContent = '❌ Erreur: ' + err.message;
        status.style.color = '#dc2626';
        form.style.display = 'none';
        console.error(err);
      }
    }

    form.addEventListener('submit', async function(e) {
      e.preventDefault();

      const formData = new FormData(this);
      const data = Object.fromEntries(formData.entries());

      // Nettoyer les valeurs
      Object.keys(data).forEach(key => {
        if (data[key] === '') data[key] = null;
      });

      try {
        const res = await fetch(`/courses/update/${id}`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: new URLSearchParams(data)
        });
        const result = await res.json().catch(()=>null);

        if (!res.ok || !result || result.success !== true) {
          const msg = result && (result.message || result.error) ? (result.message || result.error) : `${res.status} ${res.statusText}`;
          throw new Error(msg || 'Erreur de mise à jour');
        }

        alert('✅ Course modifiée avec succès !');
        window.location.href = '/ui/courses';
      } catch (err) {
        alert('❌ Erreur: ' + err.message);
        console.error(err);
      }
    });

    // Charger les données au démarrage
    load();
  </script>
</body>
</html>