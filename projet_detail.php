<?php
session_start();
require_once 'includes/db_connexion.php';
require_once 'includes/header.php';

// Vérifier que l'id est présent et valide
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$projet_id = (int) $_GET['id'];

// Récupérer les infos du projet
$stmt = $conn->prepare("SELECT * FROM projets WHERE id = ? AND statut = 'validé'");
$stmt->execute([$projet_id]);
$projet = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$projet) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($projet['titre']) ?> - Détails du projet</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f5f7fa;
    }
    .project-card {
      max-width: 800px;
      margin: auto;
      background-color: #fff;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    .project-img {
      max-height: 400px;
      object-fit: cover;
      border-radius: 8px;
      margin-bottom: 20px;
    }
    .btn-group {
      display: flex;
      gap: 10px;
      justify-content: center;
    }
  </style>
</head>
<body class="py-5">

  <div class="project-card">
    <h1 class="mb-3 text-primary"><?= htmlspecialchars($projet['titre']) ?></h1>
    <p><strong>Date de création :</strong> <?= date('d/m/Y', strtotime($projet['date_creation'])) ?></p>

    <?php if (!empty($projet['image'])): ?>
      <img src="uploads/projets/<?= htmlspecialchars($projet['image']) ?>" alt="Image du projet" class="img-fluid project-img">
    <?php endif; ?>

    <h4>Description</h4>
    <p><?= nl2br(htmlspecialchars($projet['description'])) ?></p>

    <div class="btn-group mt-4">
<a href="faire_don.php?type=projet&id=<?= $projet['id'] ?>" class="btn btn-success">Faire un don</a>
      <a href="index.php" class="btn btn-outline-secondary">← Retour à l'accueil</a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php require_once 'includes/footer.php'; ?>
