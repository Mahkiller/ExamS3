<?php
if (!function_exists('e')) { require_once __DIR__ . '/../../utils/helpers.php'; }
?>
<div class="header">
  <div>
    <div class="title"><?= e($title ?? 'Coopérative Moto') ?></div>
    <?php if (!empty($subtitle)): ?><div class="small"><?= e($subtitle) ?></div><?php endif; ?>
  </div>
  <div class="nav-links">
    <a href="/" class="links">Accueil</a>
    <a href="/dashboard" class="links">Tableau</a>
    <a href="/ui/courses" class="links">Courses</a>
    <a href="/ui/prix-essence" class="links" style="background:#ff9f1c;color:#fff;border-radius:8px;padding:8px 10px;text-decoration:none">Prix essence</a>
    <a href="/ui/delete-all" class="action-btn danger">Supprimer toutes</a>
  </div>
</div>