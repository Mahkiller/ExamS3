<?php
if (!class_exists('Flight')) {
    echo 'Flight non disponible.'; 
    exit; 
}

$id = isset($id) ? (int)$id : 0;
function e($v){ 
    return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); 
}

$title = 'Modifier la course #' . htmlspecialchars($id, ENT_QUOTES);
$subtitle = 'Modifiez les informations de la course';
require __DIR__ . '/partials/header.php';
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Modifier la course #<?= e($id) ?></title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="/assets/welcome.css">
  <link rel="stylesheet" href="/assets/modification.css">
</head>
<body>
  <div class="container">
    
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

  <!-- quick access floating buttons -->
<style>#floating-actions{position:fixed;right:18px;bottom:18px;z-index:9999}#floating-actions a{display:block;margin-bottom:8px}</style>
<div id="floating-actions">
  <a href="/ui/delete-all" class="action-btn danger">Supprimer toutes les courses</a>
  <a href="/ui/prix-essence" class="action-btn" style="background:#ff9f1c">Prix essence</a>
  <a href="/" class="action-btn view">Accueil</a>
</div>

  <script src="/assets/js/modification.js"></script>
  <script>
    modification.load(<?= json_encode($id) ?>, document.getElementById('editForm'), document.getElementById('status'));
    modification.bindSubmit(<?= json_encode($id) ?>, document.getElementById('editForm'));
  </script>
</body>
</html>