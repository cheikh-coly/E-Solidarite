<?php
session_start();
require_once 'includes/db_connexion.php';
require_once 'includes/header.php';

// Récupération des demandes de soins validées
$stmt = $conn->query("
    SELECT ds.id, ds.type_soin, ds.description, ds.justificatif, ds.date_soumission, u.nom AS nom_beneficiaire
    FROM demandes_sante ds
    JOIN utilisateurs u ON ds.utilisateur_id = u.id
    WHERE ds.statut = 'validé'
    ORDER BY ds.date_soumission DESC
");
$demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Aide aux soins - E-Social</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f4f6f9;
    }
    .section-title {
      color: #0d6efd;
      text-align: center;
      font-weight: 600;
      margin-bottom: 2rem;
    }
    .card {
      border-radius: 10px;
      border: none;
      transition: transform 0.2s ease-in-out;
    }
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    .card-footer {
      background-color: #f8f9fa;
      font-size: 0.85rem;
    }
  </style>
</head>
<body class="container py-5">

  <h1 class="section-title">Demandes de soins validées</h1>
  <p class="text-center mb-4">Soutenez les bénéficiaires en leur offrant une aide pour leurs soins médicaux.</p>

  <div class="row">
    <?php if (empty($demandes)): ?>
      <div class="alert alert-info text-center">Aucune demande validée pour le moment.</div>
    <?php else: ?>
      <?php foreach ($demandes as $demande): ?>
        <div class="col-md-6 col-lg-4 mb-4">
          <div class="card h-100 shadow-sm">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title text-primary"><?= htmlspecialchars($demande['type_soin']) ?></h5>
              <p><strong>Bénéficiaire :</strong> <?= htmlspecialchars($demande['nom_beneficiaire']) ?></p>
              <p class="flex-grow-1"><?= nl2br(htmlspecialchars($demande['description'])) ?></p>

              <?php if (!empty($demande['justificatif'])): ?>
                <p>
                  <a href="uploads/justificatifs/<?= htmlspecialchars($demande['justificatif']) ?>" target="_blank" class="btn btn-outline-info btn-sm">Voir justificatif</a>
                </p>
              <?php endif; ?>

<a href="faire_don.php?type=sante&id=<?= $demande['id'] ?>" class="btn btn-success w-100">Faire un don</a>
            </div>
            <div class="card-footer text-end">
              Soumis le <?= date('d/m/Y', strtotime($demande['date_soumission'])) ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <div class="text-center mt-4">
    <a href="javascript:history.back()" class="btn btn-outline-secondary">← Retour</a>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php require_once 'includes/footer.php'; ?>
