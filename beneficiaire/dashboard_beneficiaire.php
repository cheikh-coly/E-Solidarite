<?php
session_start();
require_once '../includes/db_connexion.php';
require_once '../includes/header.php';

if (!isset($_SESSION['utilisateur']) || $_SESSION['role'] !== 'beneficiaire') {
    header("Location: ../connexion.php");
    exit();
}

$id_user = $_SESSION['utilisateur']['id'];  // <-- Correction ici : récupérer l'id depuis le tableau utilisateur

// Récupération des projets du bénéficiaire avec total collecte
$projets = $conn->prepare("
    SELECT p.*,
           (SELECT COALESCE(SUM(montant),0) FROM dons d WHERE d.projet_id = p.id) AS total_collecte
    FROM projets p
    WHERE p.utilisateur_id = ?
    ORDER BY p.date_creation DESC
");
$projets->execute([$id_user]);
$projets = $projets->fetchAll(PDO::FETCH_ASSOC);

// Récupération des demandes santé du bénéficiaire avec total collecte
$demandes_sante = $conn->prepare("
    SELECT s.*,
           (SELECT COALESCE(SUM(montant),0) FROM dons_sante ds WHERE ds.demande_sante_id = s.id) AS total_collecte
    FROM demandes_sante s
    WHERE s.utilisateur_id = ?
    ORDER BY s.date_soumission DESC
");
$demandes_sante->execute([$id_user]);
$demandes_sante = $demandes_sante->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Bénéficiaire</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/styles.css" rel="stylesheet">
</head>
<body class="container mt-5">

  <h2 class="mb-4">Bienvenue sur votre tableau de bord</h2>
  <p class="text-muted mb-4">Voici la liste de vos projets</p>

  <div class="mb-4">
    <a href="ajouter_projet.php" class="btn btn-primary me-2 mb-2">Proposer un nouveau projet</a>
    <a href="ajouter_sante.php" class="btn btn-secondary me-2 mb-2">Ajouter demande de santé</a>
    <a href="ajouter_logement.php" class="btn btn-secondary mb-2">Ajouter demande logement</a>
  </div>

  <?php if (count($projets) === 0): ?>
    <div class="alert alert-info">Vous n'avez pas encore proposé de projet.</div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>Titre</th>
            <th>Objectif</th>
            <th>Collecté</th>
            <th>Statut</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($projets as $p): ?>
            <tr>
              <td><?= htmlspecialchars($p['titre']) ?></td>
              <td><?= number_format($p['objectif'], 0, ',', ' ') ?> FCFA</td>
              <td><?= number_format($p['total_collecte'] ?? 0, 0, ',', ' ') ?> FCFA</td>
              <td>
                <?php
                  $badge = match ($p['statut']) {
                      'validé' => 'success',
                      'en attente' => 'warning',
                      'rejeté' => 'danger',
                      default => 'secondary'
                  };
                ?>
                <span class="badge bg-<?= $badge ?>"><?= ucfirst($p['statut']) ?></span>
              </td>
              <td><?= htmlspecialchars($p['date_creation']) ?></td>
              <?php if ($p['statut'] === 'en attente'): ?>
                <td>
                  <a href="modifier_projet.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning me-1 mb-1">Modifier</a>
                  <a href="supprimer_projet.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Confirmer la suppression ?')">Supprimer</a>
                </td>
              <?php else: ?>
                <td><em class="text-muted">Non modifiable</em></td>
              <?php endif; ?>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  <?php endif ?>

  <h3 class="mt-5 mb-3">Demandes de santé</h3>

  <?php if (count($demandes_sante) === 0): ?>
    <div class="alert alert-info">Vous n'avez pas encore soumis de demande de santé.</div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>Description</th>
            <th>Collecté</th>
            <th>Statut</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($demandes_sante as $ds): ?>
            <tr>
              <td><?= htmlspecialchars(substr(strip_tags($ds['description']), 0, 50)) ?>...</td>
              <td><?= number_format($ds['total_collecte'] ?? 0, 0, ',', ' ') ?> FCFA</td>
              <td>
                <?php
                  $badge = match ($ds['statut']) {
                      'validé' => 'success',
                      'en attente' => 'warning',
                      'rejeté' => 'danger',
                      default => 'secondary'
                  };
                ?>
                <span class="badge bg-<?= $badge ?>"><?= ucfirst($ds['statut']) ?></span>
              </td>
              <td><?= htmlspecialchars($ds['date_soumission']) ?></td>
              <?php if ($ds['statut'] === 'en attente'): ?>
                <td>
                  <a href="modifier_sante.php?id=<?= $ds['id'] ?>" class="btn btn-sm btn-warning me-1 mb-1">Modifier</a>
                  <a href="supprimer_sante.php?id=<?= $ds['id'] ?>" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Confirmer la suppression ?')">Supprimer</a>
                </td>
              <?php else: ?>
                <td><em class="text-muted">Non modifiable</em></td>
              <?php endif; ?>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  <?php endif ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
require_once '../includes/footer.php';
