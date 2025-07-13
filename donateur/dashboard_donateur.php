<?php
session_start();
require_once '../includes/db_connexion.php';
require_once '../includes/header.php';

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'donateur') {
    header("Location: ../connexion.php");
    exit();
}

$id_user = $_SESSION['utilisateur']['id'];

// Récupère les dons effectués par le donateur
$requete = $conn->prepare("
    SELECT d.montant, d.date_don, p.titre, p.statut
    FROM dons d
    JOIN projets p ON d.projet_id = p.id
    WHERE d.utilisateur_id = ?
    ORDER BY d.date_don DESC
");
$requete->execute([$id_user]);
$dons = $requete->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Donateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Tableau de bord - Donateur</h2>
        <div>
            <a href="../faire_don.php" class="btn btn-primary me-2">Faire un don</a>
            <a href="ajouter_don_materiel.php" class="btn btn-secondary">Faire un don matériel</a>
        </div>
    </div>

    <p class="text-muted mb-3">Historique de vos dons</p>

    <?php if (count($dons) === 0): ?>
        <div class="alert alert-info">Vous n'avez effectué aucun don pour le moment.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Projet soutenu</th>
                        <th>Montant</th>
                        <th>Date du don</th>
                        <th>Statut du projet</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dons as $don): ?>
                        <tr>
                            <td><?= htmlspecialchars($don['titre']) ?></td>
                            <td><?= number_format($don['montant'], 0, ',', ' ') ?> FCFA</td>
                            <td><?= date('d/m/Y', strtotime($don['date_don'])) ?></td>
                            <td>
                                <?php
                                    $badge = match ($don['statut']) {
                                        'validé' => 'success',
                                        'en attente' => 'warning',
                                        'rejeté' => 'danger',
                                        default => 'secondary'
                                    };
                                ?>
                                <span class="badge bg-<?= $badge ?> text-capitalize"><?= htmlspecialchars($don['statut']) ?></span>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>

        <a href="../index.php" class="btn btn-outline-secondary mt-4">← Retour à l'accueil</a>
    <?php endif ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php require_once '../includes/footer.php'; ?>
