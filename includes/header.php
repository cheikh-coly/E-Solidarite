<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title> E-Solidarite</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/css/styles.css" rel="stylesheet">
</head>
<body>
  <!-- ✅ Barre de navigation -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold text-primary" href="/e-solidarite/index.php">
        E-Solidarite
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPublic">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarPublic">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="/e-solidarite/index.php">Accueil</a></li>
          <li class="nav-item"><a class="nav-link" href="/e-solidarite/apropos.php">A propos</a></li>
          <li class="nav-item"><a class="nav-link" href="/e-solidarite/modules.php">Modules</a></li>
          <?php if (isset($_SESSION['utilisateur'])): ?>
  <li class="nav-item">
    <a class="nav-link" href="/e-solidarite/dashboard.php">Tableau de bord</a>
  </li>
<?php endif; ?>

        </ul>

        <div class="d-flex">
          <?php if (isset($_SESSION['utilisateur'])): ?>
            <a href="/e-solidarite/deconnexion.php" class="btn btn-outline-danger">Déconnexion</a>
          <?php else: ?>
            <a href="/e-solidarite/connexion.php" class="btn btn-primary">Connexion</a>
            <a href="/e-solidarite/inscription.php" class="btn btn-success">Inscription</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </nav>

  <!-- ✅ Contenu principal -->
  <div class="content">
