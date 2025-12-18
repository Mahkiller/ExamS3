<?php
if (!class_exists('Flight')) { echo 'Flight non disponible.'; exit; }
if (!function_exists('e')) { function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); } }
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
    #floating-actions{position:fixed;right:18px;bottom:18px;z-index:9999}
    #floating-actions a{display:block;margin-bottom:8px}
  </style>
</head>
<body>
  <div class="container center-box">
    <div class="header">
      <div>
        <div class="title">Supprimer toutes les courses</div>
        <div class="small">Cette opération est irréversible.</div>
      </div>

      <div class="nav-links" style="display:flex;gap:8px;align-items:center">
        <a href="/" class="links">Accueil</a>
        <a href="/dashboard" class="links">Tableau</a>
        <a href="/ui/courses" class="links">Courses</a>
        <a href="/ui/prix-essence" class="links" style="background:#ff9f1c;color:#fff;border-radius:8px;padding:8px 10px;text-decoration:none">Prix essence</a>
      </div>
    </div>

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

  <!-- quick access floating buttons -->
  <div id="floating-actions">
    <a href="/ui/delete-all" class="action-btn danger">Supprimer toutes les courses</a>
    <a href="/ui/prix-essence" class="action-btn" style="background:#ff9f1c">Prix essence</a>
    <a href="/" class="action-btn view">Accueil</a>
  </div>

<script>
document.getElementById('cancelBtn').addEventListener('click', ()=> { window.location.href = '/'; });

document.getElementById('delBtn').addEventListener('click', async function(){
  const code = document.getElementById('code').value.trim();
  const msg = document.getElementById('msg');
  if (!code) { msg.textContent = 'Entrez le code de confirmation.'; return; }
  if (!confirm('Confirmez-vous la suppression de toutes les courses ? Cette action est irréversible.')) return;
  msg.textContent = 'Suppression en cours...';
  try {
    const res = await fetch('/courses/delete-all', {
      method: 'POST',
      body: new URLSearchParams({ code })
    });
    const j = await res.json().catch(()=>null);
    if (!res.ok) {
      throw new Error(j && j.message ? j.message : (res.status + ' ' + res.statusText));
    }
    msg.textContent = 'OK : ' + (j.message || 'Suppression effectuée') + ' — ' + (j.deleted ?? 0) + ' lignes supprimées.';
    setTimeout(()=>{ window.location.href = '/dashboard'; }, 900);
  } catch (err) {
    msg.textContent = 'Erreur : ' + err.message;
  }
});
</script>
</body>
</html>