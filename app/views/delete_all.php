<?php
if (!class_exists('Flight')) { echo 'Flight non disponible.'; exit; }
if (!function_exists('e')) { function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); } }
$title = 'Supprimer toutes les courses';
$subtitle = 'Cette opération est irréversible.';
require __DIR__ . '/partials/header.php';
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Supprimer toutes les courses</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="/assets/welcome.css">
  <style>
    .center-box{max-width:720px;margin:36px auto}
    .note{color:#666;margin-top:8px}
    .danger{background:#dc2626}
    input[type="text"]{padding:10px;border-radius:8px;border:1px solid #e6eefc;min-width:160px}
    .actions{margin-top:14px;display:flex;gap:10px;align-items:center;flex-wrap:wrap}
  </style>
</head>
<body>
  <div class="container center-box">
    <div class="card">
      <div>
        <label for="code">Code de confirmation :</label>
        <div style="margin-top:8px;display:flex;gap:8px;align-items:center">
          <input id="code" name="code" type="text" placeholder="Entrez le code" />
          <div class="note">Le code est "1234"</div>
        </div>

        <div class="actions">
          <button id="delBtn" class="action-btn danger">Supprimer toutes les courses</button>
          <button id="cancelBtn" class="action-btn view">Annuler</button>
          <span id="msg" class="small"></span>
        </div>
      </div>
    </div>
  </div>

<script src="/assets/js/delete_all.js"></script>
</body>
</html>