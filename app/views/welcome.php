<?php
if (!class_exists('Flight')) { echo 'Flight non disponible.'; exit; }
require_once __DIR__ . '/../utils/helpers.php';

$db = Flight::db();
$error = null;
$totals = ['recette'=>0.0,'salaire'=>0.0,'entretien'=>0.0,'depense'=>0.0,'benefice'=>0.0];
$dates = [];

$date_debut = $_GET['date_debut'] ?? '';
$date_fin = $_GET['date_fin'] ?? '';
$moto_id = $_GET['moto_id'] ?? '';
$conducteur_id = $_GET['conducteur_id'] ?? '';
$client_id = $_GET['client_id'] ?? '';

try {
    $motos_list = $db->query('SELECT id, immatriculation FROM Moto_motos ORDER BY immatriculation')->fetchAll(PDO::FETCH_ASSOC);

    $conducteurs_list = $db->query('SELECT id, nom FROM Moto_conducteurs ORDER BY nom')->fetchAll(PDO::FETCH_ASSOC);

    $clients_list = $db->query('SELECT id, nom FROM Moto_clients ORDER BY nom')->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $motos_list = [];
    $conducteurs_list = [];
    $clients_list = [];
}

try {
    $sql = "
        SELECT
            c.id,
            DATE(c.date_course) AS date,
            c.date_course,
            c.heure_debut,
            c.heure_fin,
            c.km,
            c.montant,
            c.depart,
            c.arrivee,
            c.valide,
            co.id AS conducteur_id,
            co.nom AS conducteur_nom,
            co.salaire_pourcentage,
            m.id AS moto_id,
            m.immatriculation AS moto_immat,
            m.entretien_pourcentage,
            cl.id AS client_id,
            cl.nom AS client_nom
        FROM Moto_courses c
        LEFT JOIN Moto_conducteurs co ON c.conducteur_id = co.id
        LEFT JOIN Moto_motos m ON c.moto_id = m.id
        LEFT JOIN Moto_clients cl ON c.client_id = cl.id
        WHERE 1=1
    ";
    
    $params = [];

    if (!empty($date_debut)) {
        $sql .= " AND c.date_course >= ?";
        $params[] = $date_debut;
    }

    if (!empty($date_fin)) {
        $sql .= " AND c.date_course <= ?";
        $params[] = $date_fin;
    }

    if (!empty($moto_id) && is_numeric($moto_id)) {
        $sql .= " AND c.moto_id = ?";
        $params[] = (int)$moto_id;
    }

    if (!empty($conducteur_id) && is_numeric($conducteur_id)) {
        $sql .= " AND c.conducteur_id = ?";
        $params[] = (int)$conducteur_id;
    }

    if (!empty($client_id) && is_numeric($client_id)) {
        $sql .= " AND c.client_id = ?";
        $params[] = (int)$client_id;
    }
    
    $sql .= " ORDER BY c.date_course DESC, c.heure_debut ASC, c.id ASC";
    
    if (empty($params)) {
        $stmt = $db->query($sql);
    } else {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
    }
    
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    $totals = ['recette'=>0.0,'salaire'=>0.0,'entretien'=>0.0,'depense'=>0.0,'benefice'=>0.0];
    $dates = [];

    foreach ($courses as $c) {
        $date = $c['date'] ?? 'Sans date';
        $montant = (float)($c['montant'] ?? 0);
        $salaire = $montant * ((float)($c['salaire_pourcentage'] ?? 0) / 100.0);
        $entretien = $montant * ((float)($c['entretien_pourcentage'] ?? 0) / 100.0);
        $depense = $salaire + $entretien;
        $benefice = $montant - $depense;

        $row = [
            'id'=> (int)$c['id'],
            'heure_debut'=>$c['heure_debut'],
            'heure_fin'=>$c['heure_fin'],
            'km'=> (float)$c['km'],
            'montant'=>$montant,
            'depart'=>$c['depart'],
            'arrivee'=>$c['arrivee'],
            'valide'=> (int)$c['valide'],
            'conducteur_nom'=>$c['conducteur_nom'] ?? '—',
            'moto_immat'=>$c['moto_immat'] ?? '—',
            'client_nom'=>$c['client_nom'] ?? '—',
            'salaire'=>$salaire,
            'entretien'=>$entretien,
            'depense'=>$depense,
            'benefice'=>$benefice,
        ];
        
        if (!isset($dates[$date])) {
            $dates[$date] = ['rows'=>[], 'totals'=>['recette'=>0.0,'salaire'=>0.0,'entretien'=>0.0,'depense'=>0.0,'benefice'=>0.0]];
        }
        $dates[$date]['rows'][] = $row;
        $dates[$date]['totals']['recette'] += $montant;
        $dates[$date]['totals']['salaire'] += $salaire;
        $dates[$date]['totals']['entretien'] += $entretien;
        $dates[$date]['totals']['depense'] += $depense;
        $dates[$date]['totals']['benefice'] += $benefice;

        $totals['recette'] += $montant;
        $totals['salaire'] += $salaire;
        $totals['entretien'] += $entretien;
        $totals['depense'] += $depense;
        $totals['benefice'] += $benefice;
    }
} catch (Exception $ex) {
    $error = $ex->getMessage();
}

uksort($dates, function($a,$b){ 
    if ($a==='Sans date') return 1; 
    if ($b==='Sans date') return -1; 
    return strcmp($b,$a); 
});

$title = 'Tableau financier';
$subtitle = $message ?? '';
require __DIR__ . '/partials/header.php';
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Tableau - Coopérative Moto</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="/assets/welcome.css">
</head>
<body>
<div class="container">
  <div class="card">
    <?php if ($error): ?>
      <div style="color:#a00;margin-bottom:10px">Erreur : <?= e($error) ?></div>
    <?php endif; ?>

    <div class="filter-section">
      <form method="GET" action="/dashboard" class="filter-form">
        <div class="filter-grid">
          <div class="filter-group">
            <label>Du</label>
            <input type="date" name="date_debut" value="<?= e($date_debut) ?>">
          </div>
          <div class="filter-group">
            <label>Au</label>
            <input type="date" name="date_fin" value="<?= e($date_fin) ?>">
          </div>
          <div class="filter-group">
            <label>Moto</label>
            <select name="moto_id">
              <option value="">Tous</option>
              <?php foreach ($motos_list as $m): ?>
                <option value="<?= e($m['id']) ?>" <?= $moto_id == $m['id'] ? 'selected' : '' ?>><?= e($m['immatriculation']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="filter-group">
            <label>Conducteur</label>
            <select name="conducteur_id">
              <option value="">Tous</option>
              <?php foreach ($conducteurs_list as $c): ?>
                <option value="<?= e($c['id']) ?>" <?= $conducteur_id == $c['id'] ? 'selected' : '' ?>><?= e($c['nom']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="filter-group">
            <label>Client</label>
            <select name="client_id">
              <option value="">Tous</option>
              <?php foreach ($clients_list as $cl): ?>
                <option value="<?= e($cl['id']) ?>" <?= $client_id == $cl['id'] ? 'selected' : '' ?>><?= e($cl['nom']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="filter-actions">
          <button class="action-btn validate" type="submit">Appliquer</button>
          <a class="action-btn view" href="/dashboard">Réinitialiser</a>
        </div>
      </form>
    </div>

    <?php require __DIR__ . '/partials/kpis.php'; ?>

    <?php if (empty($dates)): ?>
      <div class="small" style="margin-top:12px">Aucune course enregistrée.</div>
    <?php else: ?>
      <?php foreach ($dates as $date => $block): ?>
        <?php require __DIR__ . '/partials/courses_list.php'; ?>
      <?php endforeach; ?>
    <?php endif; ?>

  </div>
</div>

<script src="/assets/js/welcome.js"></script>
</body>
</html>