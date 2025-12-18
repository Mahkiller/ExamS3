<?php
if (!class_exists('Flight')) { echo 'Flight non disponible.'; exit; }
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
$title = 'Nouvelle course +';
$subtitle = 'Créer une course et voir la liste.';
require __DIR__ . '/partials/header.php';
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
  </style>
</head>
<body>

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

  <script src="/assets/js/courses.js"></script>
</body>
</html>