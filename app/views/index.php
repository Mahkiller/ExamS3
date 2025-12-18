<?php

// Page d'accueil simple — choix entre tableau financier et création de course
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
  </style>
</head>
<body>
  <div class="container center">
    <h1 class="title">Coopérative Moto</h1>
    <p class="subtitle">Choisissez une action</p>

    <div style="margin-top:28px">
      <a class="big-btn btn-primary" href="/dashboard">Tableau financier</a>
      <a class="big-btn btn-create" href="/ui/courses">Nouvelle course +</a>
    </div>

    <p class="small" style="margin-top:24px;color:#8892a6">
      Accéder directement à la liste des courses et aux outils de gestion.
    </p>
  </div>
</body>
</html>