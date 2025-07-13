<?php
session_start();
require_once 'includes/db_connexion.php';
require_once 'includes/header.php';


// Récupérer toutes les demandes de logement validées
$stmt = $conn->query("
    SELECT dl.id, dl.type_logement, dl.description, dl.justificatif, dl.date_soumission, u.nom AS nom_beneficiaire
    FROM demandes_logement dl
    JOIN utilisateurs u ON dl.utilisateur_id = u.id
    WHERE dl.statut = 'validé'
    ORDER BY dl.date_soumission DESC
");
$demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Module Logement - E-Social</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="css/styles.css"
</head>
<body class="container py-5">

    <h1 class="mb-4">Aide au Logement</h1>
    <p>Ci-dessous les demandes de logement validées, vous pouvez soutenir une personne en difficulté.</p>

    <div class="row">
        <?php if (empty($demandes)): ?>
            <div class="alert alert-info">Aucune demande validée pour le moment.</div>
        <?php else: ?>
            <?php foreach ($demandes as $demande): ?>
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($demande['type_logement']) ?></h5>
                            <p class="card-text"><strong>Bénéficiaire :</strong> <?= htmlspecialchars($demande['nom_beneficiaire']) ?></p>
                            <p><?= nl2br(htmlspecialchars($demande['description'])) ?></p>
                            <?php if (!empty($demande['justificatif'])): ?>
                                <p><a href="uploads/justificatifs/<?= htmlspecialchars($demande['justificatif']) ?>" target="_blank" class="btn btn-outline-info btn-sm">Voir justificatif</a></p>
                            <?php endif; ?>
<a href="faire_don.php?type=logement&id=<?= $logement_id ?>" class="btn btn-primary">Faire un don</a>
                        </div>
                        <div class="card-footer text-muted text-end">
                            Soumis le <?= date('d/m/Y', strtotime($demande['date_soumission'])) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

      <div class="text-center mt-3">
    <button class="btn btn-outline-secondary" onclick="history.back();">← Retour en arrière</button>
</div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
require_once 'includes/footer.php';
