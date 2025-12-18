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
        <div class="small">Définir le prix d’essence global (table Moto_parametres)</div>
      </div>
      <div>
        <a href="/" class="links">← Retour accueil</a>
        <a href="/dashboard" class="links" style="margin-left:8px">Tableau</a>
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