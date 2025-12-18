<?php
// Vue d'accueil modifiée — calcule recettes, salaires, entretien, dépenses et bénéfices par date.
// Ne crée aucun fichier. Tente une requête robuste et tombe en fallback si schéma différent.
if (!class_exists('Flight')) {
    echo 'Flight non disponible.';
    exit;
}

function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

$db = Flight::db();
$rows = [];
$totals = ['recette' => 0.0, 'salaire' => 0.0, 'entretien' => 0.0, 'depense' => 0.0, 'benefice' => 0.0];
$error = null;
$debug = ['queries' => [], 'notes' => [], 'param_columns' => []];

try {
    // Essayer d'agréger directement depuis les courses en utilisant les pourcentages fournis
    // salaire = montant * conducteur.salaire_pourcentage/100
    // entretien = montant * moto.entretien_pourcentage/100
    $sql = "
        SELECT DATE(c.date_course) AS date,
               SUM(c.montant) AS recette,
               SUM(c.montant * (co.salaire_pourcentage / 100)) AS salaire,
               SUM(c.montant * (m.entretien_pourcentage / 100)) AS entretien
        FROM Moto_courses c
        LEFT JOIN Moto_conducteurs co ON c.conducteur_id = co.id
        LEFT JOIN Moto_motos m ON c.moto_id = m.id
        GROUP BY DATE(c.date_course)
        ORDER BY DATE(c.date_course) DESC
    ";
    $debug['queries'][] = $sql;
    $stmt = $db->query($sql);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($data)) {
        // si aucune ligne, on tentera une autre approche (ex: tables manquantes ou colonnes différentes)
        $debug['notes'][] = 'Aucune ligne retournée par l\'agrégation principale.';
    } else {
        foreach ($data as $r) {
            $date = $r['date'] ?? 'Sans date';
            $recette = (float) ($r['recette'] ?? 0.0);
            $salaire = (float) ($r['salaire'] ?? 0.0);
            $entretien = (float) ($r['entretien'] ?? 0.0);
            $depense = $salaire + $entretien;
            $benefice = $recette - $depense;

            $rows[] = [
                'date' => $date,
                'recette' => $recette,
                'salaire' => $salaire,
                'entretien' => $entretien,
                'depense' => $depense,
                'benefice' => $benefice,
            ];

            $totals['recette'] += $recette;
            $totals['salaire'] += $salaire;
            $totals['entretien'] += $entretien;
            $totals['depense'] += $depense;
        }
        $totals['benefice'] = $totals['recette'] - $totals['depense'];
    }

    // Si la requête principale a échoué (colonnes/tables manquantes), fallback : détecter colonnes et faire des requêtes séparées
} catch (Exception $ex) {
    $debug['queries_error'] = $ex->getMessage();
    // Fallback : tenter d'obtenir les recettes par date (si date_course existe)
    try {
        $stmt = $db->query("SELECT DATE(date_course) AS date, SUM(montant) AS recette FROM Moto_courses GROUP BY DATE(date_course) ORDER BY DATE(date_course) DESC");
        $recettes = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Exception $ex2) {
        $recettes = [];
        $debug['recette_error'] = $ex2->getMessage();
    }

    // Tentative de calcul des salaires/entretien par jonction manuelle si colonnes existantes
    try {
        $stmt = $db->query("SELECT DATE(c.date_course) AS date, SUM(COALESCE(c.montant,0) * COALESCE(co.salaire_pourcentage,0) / 100) AS salaire, SUM(COALESCE(c.montant,0) * COALESCE(m.entretien_pourcentage,0) / 100) AS entretien FROM Moto_courses c LEFT JOIN Moto_conducteurs co ON c.conducteur_id = co.id LEFT JOIN Moto_motos m ON c.moto_id = m.id GROUP BY DATE(c.date_course) ORDER BY DATE(c.date_course) DESC");
        $deps = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Exception $ex3) {
        $deps = [];
        $debug['deps_error'] = $ex3->getMessage();
    }

    // Fusionner recettes et deps de fallback
    $map = [];
    foreach ($recettes as $r) {
        $d = $r['date'] ?? 'Sans date';
        $map[$d]['date'] = $d;
        $map[$d]['recette'] = (float) ($r['recette'] ?? 0.0);
    }
    foreach ($deps as $d) {
        $dt = $d['date'] ?? 'Sans date';
        $map[$dt]['date'] = $dt;
        $map[$dt]['salaire'] = (float) ($d['salaire'] ?? 0.0);
        $map[$dt]['entretien'] = (float) ($d['entretien'] ?? 0.0);
    }

    foreach ($map as $m) {
        $rec = $m['recette'] ?? 0.0;
        $sal = $m['salaire'] ?? 0.0;
        $ent = $m['entretien'] ?? 0.0;
        $dep = $sal + $ent;
        $ben = $rec - $dep;
        $rows[] = [
            'date' => $m['date'],
            'recette' => $rec,
            'salaire' => $sal,
            'entretien' => $ent,
            'depense' => $dep,
            'benefice' => $ben,
        ];
        $totals['recette'] += $rec;
        $totals['salaire'] += $sal;
        $totals['entretien'] += $ent;
        $totals['depense'] += $dep;
    }
    $totals['benefice'] = $totals['recette'] - $totals['depense'];
}

// Formatage et tri : s'assurer tri date desc, 'Sans date' en fin
usort($rows, function($a, $b) {
    $da = $a['date'] ?? '';
    $dbt = $b['date'] ?? '';
    if ($da === 'Sans date') return 1;
    if ($dbt === 'Sans date') return -1;
    return strcmp($dbt, $da);
});

// Helper format
function fmt($n) { return number_format((float)$n, 2, ',', ' '); }
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Rapport financier</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        :root{--bg:#f6f8fb;--card:#fff;--muted:#6b7280;--accent:#0f62fe;--success:#16a34a;--danger:#dc2626}
        body{font-family:Inter,ui-sans-serif,system-ui,Segoe UI,Roboto,Helvetica,Arial;color:#111827;background:var(--bg);padding:28px;margin:0}
        .container{max-width:1100px;margin:0 auto}
        h1{font-size:20px;margin:0 0 12px;color:#0f172a}
        .card{background:var(--card);border-radius:10px;padding:18px;box-shadow:0 6px 18px rgba(15,23,42,0.06);margin-bottom:16px}
        .summary{display:flex;gap:16px;flex-wrap:wrap;align-items:center}
        .tot{padding:10px 14px;border-radius:8px;background:#f8fafc;border:1px solid #eef2ff;min-width:180px}
        .tot b{display:block;font-size:18px}
        .tot .muted{font-size:13px;color:var(--muted)}
        table{width:100%;border-collapse:collapse;margin-top:8px;font-size:14px}
        thead th{background:#0f172a;color:#fff;padding:10px;text-align:right;border-radius:4px;font-weight:600}
        thead th.left{text-align:left}
        tbody tr:nth-child(odd){background:#ffffff}
        tbody tr:nth-child(even){background:#fafafb}
        th,td{padding:10px;border-bottom:1px solid #eef2f6;text-align:right}
        td.left,th.left{text-align:left}
        .positive{color:var(--success);font-weight:600}
        .negative{color:var(--danger);font-weight:600}
        .small{font-size:13px;color:var(--muted)}
        .debug{margin-top:12px;padding:12px;background:#fff7ed;border:1px dashed #ffd8a8;color:#92400e;border-radius:6px;font-size:13px}
        @media(max-width:720px){ thead th,td,th{font-size:13px} .summary{flex-direction:column;align-items:flex-start} }
    </style>
</head>
<body>
<div class="container">
    <h1>Rapport financier</h1>

    <div class="card">
        <div class="summary">
            <div class="tot">
                <span class="small">Recettes</span>
                <b><?= e(fmt($totals['recette'])) ?> Ar</b>
            </div>
            <div class="tot">
                <span class="small">Salaires (total)</span>
                <b><?= e(fmt($totals['salaire'])) ?> Ar</b>
            </div>
            <div class="tot">
                <span class="small">Entretien (total)</span>
                <b><?= e(fmt($totals['entretien'])) ?> Ar</b>
            </div>
            <div class="tot">
                <span class="small">Dépenses (salaires + entretien)</span>
                <b><?= e(fmt($totals['depense'])) ?> Ar</b>
            </div>
            <div class="tot">
                <span class="small">Bénéfice (recette - dépense)</span>
                <b class="<?= $totals['benefice'] >= 0 ? 'positive' : 'negative' ?>"><?= e(fmt($totals['benefice'])) ?> Ar</b>
            </div>
        </div>

        <table aria-label="Rapport financier par date">
            <thead>
                <tr>
                    <th class="left">Date</th>
                    <th>Recette (Ar)</th>
                    <th>Salaires (Ar)</th>
                    <th>Entretien (Ar)</th>
                    <th>Dépense (Ar)</th>
                    <th>Bénéfice (Ar)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr><td class="left" colspan="6">Aucune donnée disponible.</td></tr>
                <?php else: ?>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td class="left"><?= e($r['date'] ?? '') ?></td>
                            <td><?= e(fmt($r['recette'] ?? 0)) ?></td>
                            <td><?= e(fmt($r['salaire'] ?? 0)) ?></td>
                            <td><?= e(fmt($r['entretien'] ?? 0)) ?></td>
                            <td><?= e(fmt($r['depense'] ?? (($r['salaire'] ?? 0)+($r['entretien'] ?? 0)))) ?></td>
                            <td class="<?= ($r['benefice'] ?? 0) >= 0 ? 'positive' : 'negative' ?>"><?= e(fmt($r['benefice'] ?? 0)) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="debug">
            <div><strong>Debug —</strong> colonnes Moto_parametres: <?= e(implode(', ', ($debug['param_columns'] ?: ['(non vérifiées)']))) ?></div>
            <?php if (!empty($debug['queries_error'])): ?><div style="color:#a00">Query error: <?= e($debug['queries_error']) ?></div><?php endif; ?>
            <?php if (!empty($debug['recette_error'])): ?><div style="color:#a00">Recette error: <?= e($debug['recette_error']) ?></div><?php endif; ?>
            <?php if (!empty($debug['deps_error'])): ?><div style="color:#a00">Deps error: <?= e($debug['deps_error']) ?></div><?php endif; ?>
            <?php foreach ($debug['notes'] as $n): ?><div class="small"><?= e($n) ?></div><?php endforeach; ?>
            <div class="small">Remarque: Les salaires et l'entretien sont calculés à partir des pourcentages présents dans les tables Moto_conducteurs et Moto_motos si disponibles.</div>
        </div>
    </div>
</div>
</body>
</html>
