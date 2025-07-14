<?php
session_start();
require_once 'includes/db_connexion.php'; // Pour récupérer les infos utilisateur si besoin
require_once 'includes/header.php';

if (!isset($_SESSION['utilisateur'])) {
  header('Location: connexion.php');
  exit;
}

// Vérifie si l'utilisateur est un tableau ou juste un ID
if (is_array($_SESSION['utilisateur'])) {
  $utilisateur = $_SESSION['utilisateur'];
} else {
  // Sinon on va chercher les infos en base
  $id = (int) $_SESSION['utilisateur'];
  $stmt = $conn->prepare("SELECT id, nom, role FROM utilisateurs WHERE id = ?");
  $stmt->execute([$id]);
  $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$utilisateur) {
    echo "Utilisateur introuvable.";
    exit;
  }

  // Met à jour la session pour les prochaines fois
  $_SESSION['utilisateur'] = $utilisateur;
}

$role = $utilisateur['role'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Tableau de bord </title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
  <h2 class="mb-4 text-center">Tableau de bord - Bienvenue <?= htmlspecialchars($utilisateur['nom']) ?></h2>

  <div class="row justify-content-center">

    <!-- Section accessible à tous -->
    <div class="col-md-4 mb-3">
      <div class="card text-center shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Accueil</h5>
          <a href="index.php" class="btn btn-outline-secondary">Retour à l'accueil</a>
        </div>
      </div>
    </div>

    <!-- Section admin -->
    <?php if ($role === 'admin'): ?>
      <div class="col-md-4 mb-3">
        <div class="card text-center shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Dashboard Administrateur</h5>
            <a href="admin/dashboard_admin.php" class="btn btn-outline-primary">Gérer la plateforme</a>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- Section bénéficiaire -->
    <?php if ($role === 'beneficiaire'): ?>
      <div class="col-md-4 mb-3">
        <div class="card text-center shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Dashboard Bénéficiaire</h5>
            <a href="beneficiaire/dashboard_beneficiaire.php" class="btn btn-outline-danger">Mes projets ou demandes</a>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- Section donateur -->
    <?php if ($role === 'donateur'): ?>
      <div class="col-md-4 mb-3">
        <div class="card text-center shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Dashboard Donateur</h5>
            <a href="donateur/dashboard_donateur.php" class="btn btn-outline-success">Mes dons</a>
          </div>
        </div>
      </div>
    <?php endif; ?>

  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
</body>
</html>
