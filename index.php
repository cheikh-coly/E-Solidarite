<?php
session_start();
require_once 'includes/db_connexion.php';
require_once 'includes/header.php';

$projets = $conn->query("SELECT id, titre, description, objectif, montant_collecte, date_creation FROM projets WHERE statut = 'validé'")->fetchAll(PDO::FETCH_ASSOC);
$projets_recents = $conn->query("SELECT id, titre, description, objectif, montant_collecte, date_creation FROM projets WHERE statut = 'validé' ORDER BY date_creation DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
$demandes_sante_validees = $conn->query("SELECT id, description, justificatif, objectif_montant, montant_collecte, date_soumission FROM demandes_sante WHERE statut = 'validé' ORDER BY date_soumission DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);

$modules = [
  ['titre' => 'Logement', 'icone' => '🏠', 'description' => 'Aide au logement pour les personnes dans le besoin.', 'lien' => 'module_logement.php'],
  ['titre' => 'Santé', 'icone' => '🩺', 'description' => 'Soutien médical et soins.', 'lien' => 'module_sante.php'],
  ['titre' => 'Dons matériels', 'icone' => '📦', 'description' => 'Collecte et distribution de biens matériels.', 'lien' => 'module_dons_materiels.php'],
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>E-Solidarité</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="e-solidarite/css/style.css" />
</head>
<body>

<!-- 🖼️ Carousel -->
<div id="carouselAccueil" class="carousel slide carousel-fade mb-5" data-bs-ride="carousel" data-bs-interval="3000">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="images/slide1.jpg" class="d-block w-100" style="max-height:500px; object-fit:cover;" alt="...">
      <div class="carousel-caption bg-dark bg-opacity-50 rounded">Solidarité en action</div>
    </div>
    <div class="carousel-item">
      <img src="images/slide2.jpg" class="d-block w-100" style="max-height:500px; object-fit:cover;" alt="...">
      <div class="carousel-caption bg-dark bg-opacity-50 rounded">Soutien aux projets locaux</div>
    </div>
    <div class="carousel-item">
      <img src="images/slide3.jpg" class="d-block w-100" style="max-height:500px; object-fit:cover;" alt="...">
      <div class="carousel-caption bg-dark bg-opacity-50 rounded">Ensemble pour la santé</div>
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselAccueil" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselAccueil" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>
</div>

<!-- 💸 Formulaire de don -->
<div class="container py-5">
  <h2 class="text-center mb-4">Faites un don</h2>
  <form id="donForm" class="row g-3 justify-content-center" onsubmit="return redirigerVersFaireDon();">
    <div class="col-md-5">
      <select name="projet_id" id="projet_id" class="form-control" required>
        <option value="">Choisissez un projet</option>
        <?php foreach ($projets as $projet): ?>
          <option value="<?= $projet['id'] ?>"><?= htmlspecialchars($projet['titre']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3">
      <input type="number" name="montant" id="montant_don" class="form-control" placeholder="Montant (FCFA)" min="100" required />
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-success w-100">Donner</button>
    </div>
  </form>
</div>

<!-- 📰 Projets récents -->
<section class="container py-5">
  <h2 class="text-center mb-4">Projets récents</h2>
  <div class="row">
    <?php foreach ($projets_recents as $projet):
      $objectif = $projet['objectif'];
      $collecte = $projet['montant_collecte'];
      $percent = ($objectif > 0) ? min(100, round($collecte / $objectif * 100)) : 0;
    ?>
    <div class="col-md-4 mb-3">
      <div class="card h-100 shadow-sm">
        <div class="card-body d-flex flex-column">
          <h5 class="card-title"><?= htmlspecialchars($projet['titre']) ?></h5>
          <p class="card-text flex-grow-1"><?= nl2br(htmlspecialchars($projet['description'])) ?></p>
          <div class="mb-2">
            <div class="progress" style="height: 20px;">
              <div class="progress-bar bg-success" role="progressbar" style="width: <?= $percent ?>%;"><?= $percent ?>%</div>
            </div>
            <small class="text-muted"><?= number_format($collecte, 0, ',', ' ') ?> FCFA sur <?= number_format($objectif, 0, ',', ' ') ?> FCFA</small>
          </div>
          <a href="projet_detail.php?id=<?= $projet['id'] ?>" class="btn btn-primary mt-auto">Voir détails</a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- 🩺 Demandes santé -->
<section class="container py-5">
  <h2 class="text-center mb-4">Soutiens médicaux validés</h2>
  <div class="row">
    <?php if (empty($demandes_sante_validees)): ?>
      <p class="text-center">Aucune demande médicale validée pour le moment.</p>
    <?php else: ?>
      <?php foreach ($demandes_sante_validees as $demande):
        $objectif = $demande['objectif_montant'];
        $collecte = $demande['montant_collecte'];
        $percent = ($objectif > 0) ? min(100, round($collecte / $objectif * 100)) : 0;
      ?>
      <div class="col-md-4 mb-3">
        <div class="card h-100 shadow-sm">
          <?php if (!empty($demande['justificatif'])): ?>
            <img src="uploads/justificatifs/<?= htmlspecialchars($demande['justificatif']) ?>" class="card-img-top" style="max-height:200px; object-fit:cover;" alt="Justificatif médical">
          <?php endif; ?>
          <div class="card-body d-flex flex-column">
            <h5 class="card-title">Demande #<?= $demande['id'] ?></h5>
            <p class="card-text"><?= nl2br(htmlspecialchars($demande['description'])) ?></p>
            <div class="mb-2">
              <div class="progress" style="height: 20px;">
                <div class="progress-bar bg-success" style="width: <?= $percent ?>%;"><?= $percent ?>%</div>
              </div>
              <small class="text-muted"><?= number_format($collecte, 0, ',', ' ') ?> FCFA sur <?= number_format($objectif, 0, ',', ' ') ?> FCFA</small>
            </div>
            <a href="faire_don.php?type=sante&id=<?= $demande['id'] ?>" class="btn btn-danger mt-auto">Faire un don</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<!-- 📦 Modules -->
<section class="container py-5 bg-light">
  <h2 class="text-center mb-4">Nos modules</h2>
  <div class="row">
    <?php foreach ($modules as $module): ?>
    <div class="col-md-4 mb-3">
      <div class="card h-100 text-center shadow-sm">
        <div class="card-body d-flex flex-column justify-content-center align-items-center">
          <div style="font-size: 3rem;"><?= $module['icone'] ?></div>
          <h5 class="card-title mt-3"><?= htmlspecialchars($module['titre']) ?></h5>
          <p class="card-text"><?= htmlspecialchars($module['description']) ?></p>
          <a href="<?= htmlspecialchars($module['lien']) ?>" class="btn btn-outline-primary mt-auto">Découvrir</a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- ❤️ Valeurs -->
<section class="container py-5">
  <h2 class="text-center mb-4">Nos valeurs</h2>
  <div class="row text-center">
    <div class="col-md-4"><h4>Solidarité</h4><p>Unissons nos forces pour aider ceux qui en ont besoin.</p></div>
    <div class="col-md-4"><h4>Transparence</h4><p>Chaque don est suivi et justifié.</p></div>
    <div class="col-md-4"><h4>Impact</h4><p>Vous changez des vies concrètement.</p></div>
  </div>
</section>

<!-- 🤝 Devenir bénévole -->
<section class="container py-5 bg-light">
  <h2 class="text-center mb-4">Devenir bénévole</h2>
  <div class="text-center">
    <p class="lead">Vous souhaitez vous impliquer ? Rejoignez notre équipe de bénévoles et faites la différence sur le terrain.</p>
    <a href="contact.php#benevole" class="btn btn-outline-success btn-lg">Je veux devenir bénévole</a>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function redirigerVersFaireDon() {
    const projetId = document.getElementById('projet_id').value;
    const montant = document.getElementById('montant_don').value;
    if (!projetId || !montant || montant < 100) {
      alert('Veuillez choisir un projet et un montant valide (minimum 100 FCFA).');
      return false;
    }
    window.location.href = `faire_don.php?type=projet&id=${projetId}&montant=${montant}`;
    return false;
  }
</script>
</body>
</html>
