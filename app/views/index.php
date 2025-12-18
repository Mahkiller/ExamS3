<?php

if (!class_exists('Flight')) { echo 'Flight non disponible.'; exit; }
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Accueil — Coopérative Moto</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="/assets/welcome.css">
  <style>
    .center{max-width:900px;margin:80px auto;text-align:center}
    .big-btn{display:inline-block;padding:18px 28px;border-radius:12px;margin:12px;font-size:18px;text-decoration:none;color:#fff;font-weight:700;min-width:220px}
    .btn-primary{background:#0f62fe}
    .btn-create{background:#06b6d4}
    .subtitle{color:#6b7280;margin-top:8px;font-size:15px}
    #floating-actions{position:fixed;right:18px;bottom:18px;z-index:9999}
    #floating-actions a{display:block;margin-bottom:8px}
  </style>
</head>
<body>
  <div class="container center">
    <div class="header">
      <div>
        <h1 class="title">Coopérative Moto</h1>
        <div class="subtitle small">Choisissez une action</div>
      </div>

      <!-- navigation / actions (aligned à droite) -->
      <div class="nav-links" style="display:flex;gap:8px;align-items:center">
        <a href="/" class="links">Accueil</a>
        <a href="/dashboard" class="links">Tableau</a>
        <a href="/ui/courses" class="links">Courses</a>
        <a href="/ui/prix-essence" class="links" style="background:#ff9f1c;color:#fff;border-radius:8px;padding:8px 10px;text-decoration:none">Prix essence</a>
        <a href="/ui/delete-all" class="action-btn danger" style="margin-left:6px">Supprimer toutes</a>
      </div>
    </div>

    <div style="margin-top:28px">
      <a class="big-btn btn-primary" href="/dashboard">Tableau financier</a>
      <a class="big-btn btn-create" href="/ui/courses">Nouvelle course +</a>
    </div>

    <p class="small" style="margin-top:24px;color:#8892a6">
      Accéder directement à la liste des courses et aux outils de gestion.
    </p>
  </div>

  <!-- quick access floating buttons -->
  <div id="floating-actions">
    <a href="/ui/delete-all" class="action-btn danger">Supprimer toutes les courses</a>
    <a href="/" class="action-btn view">Accueil</a>
  </div>
</body>
</html>