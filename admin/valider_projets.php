<?php
session_start();
require_once '../includes/db_connexion.php';
require_once '../includes/header.php';

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['utilisateur']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../connexion.php");
    exit();
}

// Traitement validation ou rejet
if (isset($_GET['action'], $_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];

    if (in_array($action, ['valider', 'rejeter'])) {
        $statut = $action === 'valider' ? 'validé' : 'rejeté';
        $stmt = $conn->prepare("UPDATE projets SET statut = ? WHERE id = ?");
        $stmt->execute([$statut, $id]);
    }

    header("Location: valider_projets.php");
    exit();
}

// 🔁 Clôture automatique : projets validés dont l’objectif est atteint ou date dépassée
$conn->query("
    UPDATE projets SET cloture = 1
    WHERE statut = 'validé' AND cloture = 0
    AND (
        (SELECT COALESCE(SUM(d.montant), 0) FROM dons d WHERE d.projet_id = projets.id) >= objectif
        OR date_limite < CURDATE()
    )
");

// Récupérer projets en attente
$projets_en_attente = $conn->query("
    SELECT p.*, u.nom AS nom_user
    FROM projets p
    JOIN utilisateurs u ON p.utilisateur_id = u.id
    WHERE p.statut = 'en attente'
    ORDER BY date_creation DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Projets validés mais encore ouverts (non clôturés)
$projets_valides_ouverts = $conn->query("
    SELECT p.*, u.nom AS nom_user
    FROM projets p
    JOIN utilisateurs u ON p.utilisateur_id = u.id
    WHERE p.statut = 'validé' AND p.cloture = 0
    ORDER BY date_creation DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Projets clôturés
$projets_clotures = $conn->query("
    SELECT p.*, u.nom AS nom_user
    FROM projets p
    JOIN utilisateurs u ON p.utilisateur_id = u.id
    WHERE p.cloture = 1
    ORDER BY date_creation DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Validation des projets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" >
    <link href="css/styles.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="container mt-5">
    <h2>Projets en attente</h2>
    <?php if (empty($projets_en_attente)): ?>
        <div class="alert alert-info">Aucun projet en attente.</div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Objectif</th>
                    <th>Soumis par</th>
                    <th>Date</th>
                    <th>Date limite</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projets_en_attente as $projet): ?>
                    <tr>
                        <td><?= htmlspecialchars($projet['titre']) ?></td>
                        <td><?= htmlspecialchars(substr($projet['description'], 0, 50)) ?>...</td>
                        <td><?= number_format($projet['objectif'], 0, ',', ' ') ?> FCFA</td>
                        <td><?= htmlspecialchars($projet['nom_user']) ?></td>
                        <td><?= $projet['date_creation'] ?></td>
                        <td><?= $projet['date_limite'] ?? 'Non défini' ?></td>
                        <td>
                            <a href="?action=valider&id=<?= $projet['id'] ?>" class="btn btn-success btn-sm">Valider</a>
                            <a href="?action=rejeter&id=<?= $projet['id'] ?>" class="btn btn-danger btn-sm">Rejeter</a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    <?php endif; ?>

    <hr class="my-5">
    <h3>Projets validés ouverts (encore actifs)</h3>
    <?php if (empty($projets_valides_ouverts)): ?>
        <p>Aucun projet en cours.</p>
    <?php else: ?>
        <ul class="list-group">
            <?php foreach ($projets_valides_ouverts as $p): ?>
                <li class="list-group-item">
                    <?= htmlspecialchars($p['titre']) ?> — par <?= htmlspecialchars($p['nom_user']) ?>
                    <small class="text-muted">(Objectif : <?= number_format($p['objectif'], 0, ',', ' ') ?> FCFA, Limite : <?= $p['date_limite'] ?>)</small>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <hr class="my-5">
    <h3>Projets clôturés</h3>
    <?php if (empty($projets_clotures)): ?>
        <p>Aucun projet clôturé.</p>
    <?php else: ?>
        <ul class="list-group">
            <?php foreach ($projets_clotures as $p): ?>
                <li class="list-group-item list-group-item-secondary">
                    <?= htmlspecialchars($p['titre']) ?> — par <?= htmlspecialchars($p['nom_user']) ?>
                    <small class="text-muted">(Clôturé automatiquement)</small>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>

<?php require_once '../includes/footer.php'; ?>
