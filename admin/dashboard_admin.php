<?php
session_start();
require_once '../includes/db_connexion.php';
require_once '../includes/header.php';

// Sécurité : seul l'admin peut accéder
if (!isset($_SESSION['utilisateur']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../connexion.php");
    exit();
}

// Requêtes statistiques
$totalUtilisateurs = $conn->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
$totalDons = $conn->query("SELECT COUNT(*) FROM dons")->fetchColumn();
$totalMontant = $conn->query("SELECT SUM(montant) FROM dons")->fetchColumn() ?? 0;

// Projets
$projets = $conn->query("
    SELECT
        SUM(statut = 'validé') AS valides,
        SUM(statut = 'en attente') AS en_attente,
        SUM(statut = 'rejeté') AS rejetes
    FROM projets
")->fetch(PDO::FETCH_ASSOC);

// Santé
$demandesSante = $conn->query("
    SELECT
        SUM(statut = 'validé') AS valides,
        SUM(statut = 'en attente') AS en_attente,
        SUM(statut = 'rejeté') AS rejetes
    FROM demandes_sante
")->fetch(PDO::FETCH_ASSOC);

// Logement
$demandesLogement = $conn->query("
    SELECT
        SUM(statut = 'validé') AS valides,
        SUM(statut = 'en attente') AS en_attente,
        SUM(statut = 'rejeté') AS rejetes
    FROM demandes_logement
")->fetch(PDO::FETCH_ASSOC);

// --- Gestion messages contact ---
$parPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $parPage;

$totalMessages = $conn->query("SELECT COUNT(*) FROM messages_contact")->fetchColumn();

$stmt = $conn->prepare("SELECT * FROM messages_contact ORDER BY date_envoi DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $parPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalPages = ceil($totalMessages / $parPage);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Dashboard admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/styles.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="container mt-5">
  <h2>Tableau de bord - Administration</h2>

  <div class="row mt-4">
    <!-- Utilisateurs -->
    <div class="col-md-4">
      <div class="card text-white bg-primary mb-3">
        <div class="card-header">Utilisateurs inscrits</div>
        <div class="card-body">
          <h5 class="card-title"><?= $totalUtilisateurs ?></h5>
        </div>
      </div>
    </div>

    <!-- Actions -->
    <div class="col-md-4">
      <div class="card-header">Actions</div>
      <div class="card-body d-flex flex-column justify-content-center">
        <a href="valider_projets.php" class="btn btn-light btn-block mb-2">Valider les projets</a>
        <a href="valider_dons_materiels.php" class="btn btn-light btn-block mb-2">Valider les dons</a>
        <a href="valider_demandes_sante.php" class="btn btn-light btn-block mb-2">Valider les demandes de santé</a>
        <a href="valider_demandes_logement.php" class="btn btn-light btn-block">Valider les demandes de logement</a>
      </div>
    </div>

    <!-- Dons -->
    <div class="col-md-4">
      <div class="card text-white bg-success mb-3">
        <div class="card-header">Dons effectués</div>
        <div class="card-body">
          <h5 class="card-title"><?= $totalDons ?></h5>
          <p class="card-text">Total collecté : <?= number_format($totalMontant, 0, ',', ' ') ?> FCFA</p>
        </div>
      </div>
    </div>

    <!-- Projets -->
    <div class="col-md-4">
      <div class="card text-white bg-warning mb-3">
        <div class="card-header">Projets</div>
        <div class="card-body">
          <p class="card-text">✅ Validés : <?= $projets['valides'] ?? 0 ?></p>
          <p class="card-text">⏳ En attente : <?= $projets['en_attente'] ?? 0 ?></p>
          <p class="card-text">❌ Rejetés : <?= $projets['rejetes'] ?? 0 ?></p>
        </div>
      </div>
    </div>

    <!-- Santé -->
    <div class="col-md-4">
      <div class="card text-white bg-info mb-3">
        <div class="card-header">Demandes de santé</div>
        <div class="card-body">
          <p class="card-text">✅ Validées : <?= $demandesSante['valides'] ?? 0 ?></p>
          <p class="card-text">⏳ En attente : <?= $demandesSante['en_attente'] ?? 0 ?></p>
          <p class="card-text">❌ Rejetées : <?= $demandesSante['rejetes'] ?? 0 ?></p>
        </div>
      </div>
    </div>

    <!-- Logement -->
    <div class="col-md-4">
      <div class="card text-white bg-secondary mb-3">
        <div class="card-header">Demandes de logement</div>
        <div class="card-body">
          <p class="card-text">✅ Validées : <?= $demandesLogement['valides'] ?? 0 ?></p>
          <p class="card-text">⏳ En attente : <?= $demandesLogement['en_attente'] ?? 0 ?></p>
          <p class="card-text">❌ Rejetées : <?= $demandesLogement['rejetes'] ?? 0 ?></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Messages de contact -->
  <div class="mt-5">
    <h3>Messages reçus via le formulaire de contact</h3>

    <?php if ($totalMessages == 0): ?>
      <p>Aucun message reçu pour le moment.</p>
    <?php else: ?>
      <table class="table table-striped table-bordered">
        <thead class="table-dark">
          <tr>
            <th>Nom</th>
            <th>Email</th>
            <th>Sujet</th>
            <th>Message</th>
            <th>Date d'envoi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($messages as $msg): ?>
            <tr>
              <td><?= htmlspecialchars($msg['nom']) ?></td>
              <td><?= htmlspecialchars($msg['email']) ?></td>
              <td><?= htmlspecialchars($msg['sujet']) ?></td>
              <td style="max-width:300px; white-space:pre-wrap;"><?= htmlspecialchars($msg['message']) ?></td>
              <td><?= $msg['date_envoi'] ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <nav aria-label="Pagination">
        <ul class="pagination">
          <?php if ($page > 1): ?>
            <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>">Précédent</a></li>
          <?php endif; ?>

          <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <li class="page-item <?= ($p === $page) ? 'active' : '' ?>">
              <a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a>
            </li>
          <?php endfor; ?>

          <?php if ($page < $totalPages): ?>
            <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>">Suivant</a></li>
          <?php endif; ?>
        </ul>
      </nav>
    <?php endif; ?>
  </div>
</body>
</html>

<?php require_once '../includes/footer.php'; ?>
