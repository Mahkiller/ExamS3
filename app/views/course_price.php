<?php
if (!class_exists('Flight')) { echo 'Flight non disponible.'; exit; }

if (!function_exists('e')) {
    function e($v){
        return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
if (!function_exists('fmt')) {
    function fmt($n){
        return number_format((float)$n, 2, ',', ' ');
    }
}

$id = isset($id) ? (int)$id : 0;
$prix = isset($prix) ? $prix : null;
$prix_global = isset($prix_global) ? $prix_global : null;
$course = isset($course) ? $course : null;
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Prix essence — Course #<?= e($id) ?></title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="/assets/welcome.css">
  <link rel="stylesheet" href="/assets/course_price.css">
</head>
<body>
  <div class="container">
    <div class="header">
      <div>
        <div class="title">Prix essence — Course #<?= htmlspecialchars($id, ENT_QUOTES) ?></div>
        <div class="small">Définir un prix local pour cette course</div>
      </div>

      <div class="nav-links" style="display:flex;gap:8px;align-items:center">
        <a href="/" class="links">Accueil</a>
        <a href="/dashboard" class="links">Tableau</a>
        <a href="/ui/prix-essence" class="links" style="background:#ff9f1c;color:#fff;border-radius:8px;padding:8px 10px;text-decoration:none">Prix global</a>
        <a href="/ui/delete-all" class="action-btn danger" style="margin-left:6px">Supprimer toutes</a>
      </div>
    </div>

    <div class="card">
      <div class="small">
        Prix global actuel : <strong><?= $prix_global !== null ? e(fmt($prix_global)) . ' Ar' : 'non défini' ?></strong>
      </div>

      <div style="margin-top:12px">
        <form id="prixForm">
          <div class="form-row">
            <label for="prix">Prix essence (Ar) :</label>
            <input id="prix" name="prix" type="number" step="0.01" min="0" value="<?= $prix !== null ? e($prix) : '' ?>" required />
            <div class="muted">Si vide, la course utilisera le prix global</div>
          </div>

          <div class="actions">
            <button id="saveBtn" class="action-btn validate" type="submit">Enregistrer</button>
            <button id="resetBtn" class="action-btn danger" type="button">Supprimer prix local</button>
            <span id="msg" class="small"></span>
          </div>
        </form>

        <?php if ($course): ?>
          <div style="margin-top:16px" class="small">
            Course : <?= e($course['date_course'] ?? '') ?> — Montant : <strong><?= e(fmt($course['montant'] ?? 0)) ?> Ar</strong>
          </div>
        <?php endif; ?>
      </div>
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
document.getElementById('prixForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const prix = document.getElementById('prix').value;
  const msg = document.getElementById('msg');
  msg.textContent = 'Enregistrement...';
  try {
    const res = await fetch(`/courses/price/<?= json_encode($id) ?>`, {
      method: 'POST',
      body: new URLSearchParams({ prix })
    });
    const j = await res.json().catch(()=>null);
    if (!res.ok) throw new Error((j && j.message) ? j.message : 'Erreur serveur');
    msg.textContent = 'Prix enregistré.';
    setTimeout(()=>{ window.location.href = '/dashboard'; }, 700);
  } catch (err) {
    msg.textContent = 'Erreur: ' + err.message;
  }
});

document.getElementById('resetBtn').addEventListener('click', async function(){
  if (!confirm('Supprimer le prix local pour cette course ? Le prix global sera utilisé.')) return;
  const msg = document.getElementById('msg');
  msg.textContent = 'Suppression...';
  try {
    const res = await fetch(`/courses/price/<?= json_encode($id) ?>`, { method: 'DELETE' });
    const j = await res.json().catch(()=>null);
    if (!res.ok) throw new Error((j && j.message) ? j.message : 'Erreur serveur');
    msg.textContent = 'Prix local supprimé.';
    setTimeout(()=>{ window.location.href = '/dashboard'; }, 600);
  } catch (err) {
    msg.textContent = 'Erreur: ' + err.message;
  }
});
</script>
</body>
</html>