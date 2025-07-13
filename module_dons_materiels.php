<?php
session_start();
require_once 'includes/db_connexion.php';
require_once 'includes/header.php';

// Récupérer tous les dons matériels validés
$stmt = $conn->query("SELECT dm.*, u.nom AS nom_donateur
                      FROM dons_materiels dm
                      JOIN utilisateurs u ON dm.utilisateur_id = u.id
                      WHERE dm.statut = 'accepté'
                      ORDER BY dm.date_soumission DESC");
$dons_materiels = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Dons matériels validés</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f5f7fa;
    }
    .section-title {
      text-align: center;
      color: #0d6efd;
      margin-bottom: 2rem;
      font-weight: bold;
    }
    .card {
      box-shadow: 0 4px 12px rgba(0,0,0,0.06);
      border: none;
      border-radius: 10px;
      transition: transform 0.2s ease-in-out;
    }
    .card:hover {
      transform: translateY(-5px);
    }
    .card-img-top {
      max-height: 200px;
      object-fit: cover;
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
    }
    .btn-outline-primary {
      margin-top: 1rem;
    }
  </style>
</head>
<body class="container py-5">

  <h1 class="section-title">Dons matériels validés</h1>

  <?php if (count($dons_materiels) === 0): ?>
    <div class="alert alert-info text-center">Aucun don matériel validé n'est disponible pour le moment.</div>
  <?php else: ?>
    <div class="row">
      <?php foreach ($dons_materiels as $don): ?>
        <div class="col-md-6 col-lg-4 mb-4">
          <div class="card h-100">
            <?php if (!empty($don['image_objet'])): ?>
              <img src="uploads/materiels/<?= htmlspecialchars($don['image_objet']) ?>" class="card-img-top" alt="Image de <?= htmlspecialchars($don['nom_objet']) ?>">
            <?php else: ?>
              <img src="assets/img/default-image.jpg" class="card-img-top" alt="Pas d'image disponible">
            <?php endif; ?>
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?= htmlspecialchars($don['nom_objet']) ?></h5>
              <p class="mb-1"><strong>État :</strong> <?= htmlspecialchars($don['etat_objet']) ?></p>
              <p class="flex-grow-1"><?= nl2br(htmlspecialchars($don['description'])) ?></p>
            </div>
            <div class="card-footer text-muted text-end">
              Proposé par <strong><?= htmlspecialchars($don['nom_donateur']) ?></strong><br>
              Le <?= date('d/m/Y', strtotime($don['date_soumission'])) ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div class="text-center">
    <a href="javascript:history.back()" class="btn btn-outline-primary">← Retour</a>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php require_once 'includes/footer.php'; ?>
