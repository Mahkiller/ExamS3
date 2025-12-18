<?php
if (!class_exists('Flight')) { echo 'Flight non disponible.'; exit; }
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
$prix_global = $prix_global ?? null;
$title = 'Prix global de l\'essence';
$subtitle = 'Définir le prix d’essence global';
require __DIR__ . '/partials/header.php';
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Prix global de l'essence</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="/assets/welcome.css">
  <link rel="stylesheet" href="/assets/course_price.css">
</head>
<body>
  <div class="container">
    <div class="card">
      <form id="globalPrixForm">
        <div class="form-row">
          <label for="prix_global">Prix global (Ar)</label>
          <input id="prix_global" name="prix" type="number" step="0.01" min="0" value="<?= $prix_global !== null ? e($prix_global) : '' ?>" required />
        </div>
        <div class="actions" style="margin-top:12px">
          <button class="action-btn validate" type="submit">Enregistrer</button>
          <span id="msg" class="small"></span>
        </div>
      </form>
    </div>
  </div>

<script src="/assets/js/course_price.js"></script>
</body>
</html>