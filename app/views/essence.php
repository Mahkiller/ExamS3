<?php
if (!class_exists('Flight')) { echo 'Flight non disponible.'; exit; }
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
$prix_global = $prix_global ?? null;
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
    <div class="header">
      <div>
        <div class="title">Prix global de l'essence</div>
        <div class="small">Définir le prix d’essence global</div>
      </div>

      <div class="nav-links" style="display:flex;gap:8px;align-items:center">
        <a href="/" class="links">Accueil</a>
        <a href="/dashboard" class="links">Tableau</a>
        <a href="/ui/courses" class="links">Courses</a>
        <a href="/ui/delete-all" class="action-btn danger" style="margin-left:6px">Supprimer toutes</a>
      </div>
    </div>

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

<!-- quick access floating buttons -->
<style>#floating-actions{position:fixed;right:18px;bottom:18px;z-index:9999}#floating-actions a{display:block;margin-bottom:8px}</style>
<div id="floating-actions">
  <a href="/ui/delete-all" class="action-btn danger">Supprimer toutes les courses</a>
  <a href="/ui/prix-essence" class="action-btn" style="background:#ff9f1c">Prix essence</a>
  <a href="/" class="action-btn view">Accueil</a>
</div>

<script>
document.getElementById('globalPrixForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const prix = document.getElementById('prix_global').value;
  const msg = document.getElementById('msg');
  msg.textContent = 'Enregistrement...';
  try {
    const res = await fetch('/parametres/prix-essence', {
      method: 'POST',
      body: new URLSearchParams({ prix })
    });
    const j = await res.json();
    if (!res.ok) throw new Error(j.message || 'Erreur serveur');
    msg.textContent = 'Prix global enregistré.';
    setTimeout(()=>location.href='/dashboard',700);
  } catch (err) {
    msg.textContent = 'Erreur: ' + err.message;
  }
});
</script>
</body>
</html>