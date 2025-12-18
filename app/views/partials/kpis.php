
<div class="kpis">
  <div class="kpi"><div class="label">Recettes</div><b><?= e(fmt($totals['recette'])) ?> Ar</b></div>
  <div class="kpi"><div class="label">Salaires</div><b><?= e(fmt($totals['salaire'])) ?> Ar</b></div>
  <div class="kpi"><div class="label">Entretien</div><b><?= e(fmt($totals['entretien'])) ?> Ar</b></div>
  <div class="kpi"><div class="label">Dépenses</div><b><?= e(fmt($totals['depense'])) ?> Ar</b></div>
  <div class="kpi"><div class="label">Bénéfice</div><b class="<?= $totals['benefice']>=0 ? 'badge green' : 'badge red' ?>"><?= e(fmt($totals['benefice'])) ?> Ar</b></div>
</div>