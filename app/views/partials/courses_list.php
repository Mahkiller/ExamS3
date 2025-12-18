<?php
$block_totals = $block['totals'];
?>
<div class="date-block" style="margin-top:18px">
  <div class="date-header">
    <div>
      <button class="collapsible" data-target="tbl-<?= e($date) ?>"><?= e($date) ?></button>
    </div>
    <div class="date-totals">
      <div class="small">Recette: <strong><?= e(fmt($block_totals['recette'])) ?> Ar</strong></div>
      <div class="small" style="margin-left:12px">Dépense: <strong><?= e(fmt($block_totals['depense'])) ?> Ar</strong></div>
      <div class="small" style="margin-left:12px">Bénéfice: <strong><?= e(fmt($block_totals['benefice'])) ?> Ar</strong></div>
    </div>
  </div>

  <div id="tbl-<?= e($date) ?>" style="margin-top:12px;overflow:auto">
    <table class="table" aria-live="polite">
      <thead>
        <tr>
          <th>Heure</th>
          <th>Conducteur</th>
          <th>Client</th>
          <th>Moto</th>
          <th>KM</th>
          <th>Montant</th>
          <th>Salaires</th>
          <th>Entretien</th>
          <th>Dépense</th>
          <th>Bénéfice</th>
          <th>Départ → Arrivée</th>
          <th>Validée</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($block['rows'] as $r): ?>
          <tr>
            <td class="small"><?= e(($r['heure_debut']?:'') . ($r['heure_fin'] ? ' — '.$r['heure_fin'] : '')) ?></td>
            <td><?= e($r['conducteur_nom']) ?></td>
            <td><?= e($r['client_nom']) ?></td>
            <td><?= e($r['moto_immat']) ?></td>
            <td class="small"><strong><?= e(fmt($r['km'])) ?></strong></td>
            <td><strong><?= e(fmt($r['montant'])) ?></strong></td>
            <td><?= e(fmt($r['salaire'])) ?></td>
            <td><?= e(fmt($r['entretien'])) ?></td>
            <td style="color:var(--danger)"><?= e(fmt($r['depense'])) ?></td>
            <td class="<?= $r['benefice']>=0 ? 'positive' : 'negative' ?>"><?= e(fmt($r['benefice'])) ?></td>
            <td><?= e($r['depart'] . ' → ' . $r['arrivee']) ?></td>
            <td class="small"><?= $r['valide'] ? 'Oui' : 'Non' ?></td>
            <td>
              <?php if (! $r['valide']): ?>
                <button class="action-btn validate" onclick="validateCourse(<?= $r['id'] ?>)">Valider</button>
                <button class="action-btn danger" onclick="cancelCourse(<?= $r['id'] ?>)">Annuler</button>
                <a class="action-btn view" href="/ui/course/<?= $r['id'] ?>">Modifier</a>
                <a class="action-btn" href="/ui/course-price/<?= $r['id'] ?>" style="background:#ff9f1c">Prix essence</a>
              <?php else: ?>
                <span class="small">Course validée</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>