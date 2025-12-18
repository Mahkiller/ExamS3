<?php
// Tableau financier amélioré avec filtres.
if (!class_exists('Flight')) {
    echo 'Flight non disponible.';
    exit;
}
function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function fmt($n) { return number_format((float)$n, 2, ',', ' '); }

$db = Flight::db();
$error = null;
$totals = ['recette'=>0.0,'salaire'=>0.0,'entretien'=>0.0,'depense'=>0.0,'benefice'=>0.0];
$dates = [];

// Récupérer les filtres depuis GET
$date_debut = $_GET['date_debut'] ?? '';
$date_fin = $_GET['date_fin'] ?? '';
$moto_id = $_GET['moto_id'] ?? '';
$conducteur_id = $_GET['conducteur_id'] ?? '';
$client_id = $_GET['client_id'] ?? '';

// Récupérer les listes pour les filtres
try {
    // Liste des motos pour le filtre
    $motos_list = $db->query('SELECT id, immatriculation FROM Moto_motos ORDER BY immatriculation')->fetchAll(PDO::FETCH_ASSOC);
    
    // Liste des conducteurs pour le filtre
    $conducteurs_list = $db->query('SELECT id, nom FROM Moto_conducteurs ORDER BY nom')->fetchAll(PDO::FETCH_ASSOC);
    
    // Liste des clients pour le filtre
    $clients_list = $db->query('SELECT id, nom FROM Moto_clients ORDER BY nom')->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $motos_list = [];
    $conducteurs_list = [];
    $clients_list = [];
}

try {
    // Construction de la requête avec filtres
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
    
    // Filtre par date de début
    if (!empty($date_debut)) {
        $sql .= " AND c.date_course >= ?";
        $params[] = $date_debut;
    }
    
    // Filtre par date de fin
    if (!empty($date_fin)) {
        $sql .= " AND c.date_course <= ?";
        $params[] = $date_fin;
    }
    
    // Filtre par moto
    if (!empty($moto_id) && is_numeric($moto_id)) {
        $sql .= " AND c.moto_id = ?";
        $params[] = (int)$moto_id;
    }
    
    // Filtre par conducteur
    if (!empty($conducteur_id) && is_numeric($conducteur_id)) {
        $sql .= " AND c.conducteur_id = ?";
        $params[] = (int)$conducteur_id;
    }
    
    // Filtre par client
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

    // Réinitialiser les totaux
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
            'id'=>(int)$c['id'],
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
  <div class="header">
    <div>
      <div class="title">Tableau financier - Coopérative Moto</div>
      <div class="small">Affiche totaux et détail des courses</div>
    </div>
    <div class="links">
      <a href="/ui/courses">Gestion des courses</a>
      <a href="/ui/course/1" style="background:#06b6d4">Modifier une course</a>
    </div>
  </div>

  <div class="card">
    <?php if ($error): ?>
      <div style="color:#a00;margin-bottom:10px">Erreur : <?= e($error) ?></div>
    <?php endif; ?>

    <!-- Section des filtres -->
    <div class="filter-section">
      <h3 style="margin-top:0;margin-bottom:15px;">Filtres de recherche</h3>
      <form method="GET" action="/dashboard" class="filter-form">
        <div class="filter-grid">
          <div class="filter-group">
            <label for="date_debut">Date début</label>
            <input type="date" id="date_debut" name="date_debut" value="<?= e($date_debut) ?>">
          </div>
          
          <div class="filter-group">
            <label for="date_fin">Date fin</label>
            <input type="date" id="date_fin" name="date_fin" value="<?= e($date_fin) ?>">
          </div>
          
          <div class="filter-group">
            <label for="moto_id">Moto</label>
            <select id="moto_id" name="moto_id">
              <option value="">Toutes les motos</option>
              <?php foreach ($motos_list as $moto): ?>
                <option value="<?= e($moto['id']) ?>" <?= $moto_id == $moto['id'] ? 'selected' : '' ?>>
                  <?= e($moto['immatriculation']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <div class="filter-group">
            <label for="conducteur_id">Conducteur</label>
            <select id="conducteur_id" name="conducteur_id">
              <option value="">Tous les conducteurs</option>
              <?php foreach ($conducteurs_list as $conducteur): ?>
                <option value="<?= e($conducteur['id']) ?>" <?= $conducteur_id == $conducteur['id'] ? 'selected' : '' ?>>
                  <?= e($conducteur['nom']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <div class="filter-group">
            <label for="client_id">Client</label>
            <select id="client_id" name="client_id">
              <option value="">Tous les clients</option>
              <?php foreach ($clients_list as $client): ?>
                <option value="<?= e($client['id']) ?>" <?= $client_id == $client['id'] ? 'selected' : '' ?>>
                  <?= e($client['nom']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        
        <div class="filter-actions">
          <button type="submit" class="action-btn" style="background:#0f62fe;">Appliquer les filtres</button>
          <a href="/dashboard" class="action-btn" style="background:#6b7280;text-decoration:none;">Réinitialiser</a>
        </div>
      </form>
      
      <?php if (!empty($date_debut) || !empty($date_fin) || !empty($moto_id) || !empty($conducteur_id) || !empty($client_id)): ?>
        <div class="active-filters">
          <div class="small" style="margin-top:10px;">
            <strong>Filtres actifs :</strong>
            <?php 
              $active_filters = [];
              if (!empty($date_debut)) $active_filters[] = "À partir du " . e($date_debut);
              if (!empty($date_fin)) $active_filters[] = "Jusqu'au " . e($date_fin);
              if (!empty($moto_id)) {
                $moto_name = '';
                foreach ($motos_list as $m) {
                  if ($m['id'] == $moto_id) {
                    $moto_name = e($m['immatriculation']);
                    break;
                  }
                }
                $active_filters[] = "Moto: " . $moto_name;
              }
              if (!empty($conducteur_id)) {
                $conducteur_name = '';
                foreach ($conducteurs_list as $c) {
                  if ($c['id'] == $conducteur_id) {
                    $conducteur_name = e($c['nom']);
                    break;
                  }
                }
                $active_filters[] = "Conducteur: " . $conducteur_name;
              }
              if (!empty($client_id)) {
                $client_name = '';
                foreach ($clients_list as $cl) {
                  if ($cl['id'] == $client_id) {
                    $client_name = e($cl['nom']);
                    break;
                  }
                }
                $active_filters[] = "Client: " . $client_name;
              }
              echo implode(' • ', $active_filters);
            ?>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <div class="kpis">
      <div class="kpi"><div class="label">Recettes</div><b><?= e(fmt($totals['recette'])) ?> Ar</b></div>
      <div class="kpi"><div class="label">Salaires</div><b><?= e(fmt($totals['salaire'])) ?> Ar</b></div>
      <div class="kpi"><div class="label">Entretien</div><b><?= e(fmt($totals['entretien'])) ?> Ar</b></div>
      <div class="kpi"><div class="label">Dépenses</div><b><?= e(fmt($totals['depense'])) ?> Ar</b></div>
      <div class="kpi"><div class="label">Bénéfice</div><b class="<?= $totals['benefice']>=0 ? 'badge green' : 'badge red' ?>"><?= e(fmt($totals['benefice'])) ?> Ar</b></div>
    </div>

    <div class="test-results">
      <button class="test-btn" id="testCoursesBtn">Tester API courses</button>
      <button class="test-btn" id="testCourseBtn">Tester modification</button>
      <div id="testOutput" class="small" style="margin-left:8px"></div>
    </div>

    <?php if (empty($dates)): ?>
      <div class="small" style="margin-top:12px">Aucune course enregistrée.</div>
    <?php else: ?>
      <?php foreach ($dates as $date => $block): ?>
        <div class="date-block" id="date-<?= e($date) ?>">
          <div class="date-header">
            <div>
              <button class="collapsible" data-target="tbl-<?= e($date) ?>"><?= e($date) ?></button> 
              <span class="small">(<?= count($block['rows']) ?> course<?= count($block['rows'])>1?'s':'' ?>)</span>
            </div>
            <div class="date-totals small">
              Recette: <strong><?= e(fmt($block['totals']['recette'])) ?> Ar</strong>&nbsp;|
              Dépense: <strong><?= e(fmt($block['totals']['depense'])) ?> Ar</strong>&nbsp;|
              Bénéfice: <strong class="<?= $block['totals']['benefice']>=0 ? 'badge green' : 'badge red' ?>"><?= e(fmt($block['totals']['benefice'])) ?> Ar</strong>
            </div>
          </div>

          <table class="table" id="tbl-<?= e($date) ?>">
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
                      <!-- nouveau : accès page modifier le prix de l'essence -->
                      <a class="action-btn" href="/ui/course-price/<?= $r['id'] ?>" style="background:#ff9f1c">Prix essence</a>
                    <?php else: ?>
                      <!-- course validée : actions limitées -->
                      <span class="small">Course validée</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<script>
// Gestion des sections collapsibles
document.querySelectorAll('.collapsible').forEach(btn => {
  btn.addEventListener('click', () => {
    const id = btn.getAttribute('data-target');
    const tbl = document.getElementById(id);
    if (tbl) {
      tbl.style.display = tbl.style.display === 'none' ? '' : 'none';
    }
  });
});

// Tests API
async function testUrl(u) {
  try {
    const res = await fetch(u, { method: 'GET' });
    return `${res.status} ${res.statusText}`;
  } catch (e) {
    return 'Erreur: ' + e.message;
  }
}

document.getElementById('testCoursesBtn').addEventListener('click', async () => {
  document.getElementById('testOutput').textContent = 'Test en cours...';
  const r = await testUrl('/courses');
  document.getElementById('testOutput').textContent = '/courses → ' + r;
});

document.getElementById('testCourseBtn').addEventListener('click', async () => {
  document.getElementById('testOutput').textContent = 'Test en cours...';
  const r = await testUrl('/courses/1');
  document.getElementById('testOutput').textContent = '/courses/1 → ' + r;
});

// Validation d'une course
async function validateCourse(id) {
  if (!confirm('Valider la course ? (une fois validée, elle ne sera plus modifiable)')) return;
  try {
    const res = await fetch(`/courses/validate/${id}`, { 
      method: 'POST'
    });
    if (!res.ok) {
      const error = await res.json().catch(()=>({message:'Erreur serveur'}));
      throw new Error(error.message || 'Erreur serveur');
    }
    const result = await res.json();
    if (result.success) {
      alert('Course validée avec succès !');
      location.reload();
    } else {
      throw new Error(result.message || 'Échec de la validation');
    }
  } catch (e) {
    alert('Erreur: ' + e.message);
  }
}

async function cancelCourse(id){
  if(!confirm('Annuler (supprimer) cette course ?')) return;
  try {
    // Utiliser POST pour compatibilité serveur
    const res = await fetch(`/courses/delete/${id}`, { method: 'POST' });
    if (!res.ok) {
      const err = await res.json().catch(()=>({message:'Erreur serveur'}));
      throw new Error(err.message || 'Erreur serveur');
    }
    const j = await res.json();
    if (j.success) {
      location.reload();
    } else {
      throw new Error(j.message || 'Erreur suppression');
    }
  } catch (e) {
    alert('Erreur: ' + e.message);
  }
}
</script>
</body>
</html>