<?php
// Vue pour modifier une course (reçoit $id passé par le routeur)
if (!class_exists('Flight')) { echo 'Flight non disponible.'; exit; }
$id = isset($id) ? (int)$id : 0;
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
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
  <div class="wrap">
    <h1>Modifier la course #<?= e($id) ?></h1>
    <div class="card" id="card">
      <div id="status" class="small" style="display:none"></div>
      <form id="editForm" style="display:block">
        <label>Date <input name="date_course" type="date" required /></label>
        <div class="row">
          <label>Heure début <input name="heure_debut" type="time" /></label>
          <label>Heure fin <input name="heure_fin" type="time" /></label>
        </div>
        <div class="row">
          <label>KM <input name="km" type="number" step="0.01" required /></label>
          <label>Montant <input name="montant" type="number" step="0.01" required /></label>
        </div>
        <label>Départ <input name="depart" /></label>
        <label>Arrivée <input name="arrivee" /></label>
        <div class="row">
          <label>Conducteur ID <input name="conducteur_id" type="number" required /></label>
          <label>Moto ID <input name="moto_id" type="number" required /></label>
        </div>
        <label>Client ID <input name="client_id" type="number" /></label>
        <div style="margin-top:12px">
          <button class="btn primary" type="submit">Enregistrer</button>
          <a class="btn ghost" href="/ui/courses" style="text-decoration:none;margin-left:8px">Retour</a>
        </div>
      </form>
    </div>
  </div>

  <script>
    const id = <?= json_encode($id) ?>;
    const status = document.getElementById('status');
    const form = document.getElementById('editForm');

    async function load(){
      try{
        // show minimal busy indicator on form controls instead of a persistent "Chargement..."
        form.style.opacity = '0.6';
        const res = await fetch(`/courses/${id}`);
        if (!res.ok) throw new Error(await res.text());
        const c = await res.json();
        form.elements['date_course'].value = c.date_course || c.date || '';
        form.elements['heure_debut'].value = c.heure_debut || '';
        form.elements['heure_fin'].value = c.heure_fin || '';
        form.elements['km'].value = c.km || '';
        form.elements['montant'].value = c.montant || '';
        form.elements['depart'].value = c.depart || '';
        form.elements['arrivee'].value = c.arrivee || '';
        form.elements['conducteur_id'].value = c.conducteur_id || '';
        form.elements['moto_id'].value = c.moto_id || '';
        form.elements['client_id'].value = c.client_id || '';
        if (c.valide == 1) {
          status.style.display = 'block';
          status.textContent = 'Course validée — modification interdite.';
          form.style.display = 'none';
        } else {
          status.style.display = 'none';
          form.style.display = 'block';
        }
      }catch(err){
        status.style.display = 'block';
        status.textContent = 'Erreur chargement: ' + String(err);
        form.style.display = 'none';
      } finally {
        form.style.opacity = '1';
      }
    }

    form.addEventListener('submit', async function(e){
      e.preventDefault();
      const data = new URLSearchParams(new FormData(this));
      try {
        const res = await fetch(`/courses/update/${id}`, { method: 'POST', body: data });
        const j = await res.json();
        if (!res.ok) throw new Error(j.message || 'Erreur');
        alert('Modifié.');
        window.location.href = '/ui/courses';
      } catch (err) {
        alert('Erreur: ' + err.message);
      }
    });

    load();
  </script>
</body>
</html>