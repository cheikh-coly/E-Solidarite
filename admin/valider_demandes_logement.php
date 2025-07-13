<?php
session_start();
require_once '../includes/db_connexion.php';
require_once '../includes/header.php';

if (!isset($_SESSION['utilisateur']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../connexion.php');
    exit;
}

// Clôturer automatiquement les demandes dépassées ou atteignant l’objectif
$conn->query("
    UPDATE demandes_logement
    SET statut = 'clôturé'
    WHERE statut = 'validé'
    AND (
        (date_limite IS NOT NULL AND date_limite < CURDATE())
        OR (montant_collecte >= objectif_montant)
    )
");

// Traitement de validation/rejet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_demande'], $_POST['action'])) {
    $id = (int) $_POST['id_demande'];
    $action = $_POST['action'];

    if (in_array($action, ['valider', 'rejeter'])) {
        $statut = $action === 'valider' ? 'validé' : 'rejeté';
        $stmt = $conn->prepare("UPDATE demandes_logement SET statut = ? WHERE id = ?");
        $stmt->execute([$statut, $id]);
    }

    header('Location: valider_demandes_logement.php');
    exit;
}

// Récupérer toutes les demandes
$req = $conn->query("
    SELECT dl.*, u.email
    FROM demandes_logement dl
    JOIN utilisateurs u ON dl.utilisateur_id = u.id
    ORDER BY dl.date_soumission DESC
");
$demandes = $req->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Administration - Demandes logement</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/styles.css" rel="stylesheet">
</head>
<body class="container py-5">
  <h1>Gestion des demandes de logement</h1>

  <a href="dashboard.php" class="btn btn-secondary mb-3">← Retour au tableau de bord</a>

  <table class="table table-bordered table-hover">
    <thead class="table-light">
      <tr>
        <th>Utilisateur</th>
        <th>Type</th>
        <th>Description</th>
        <th>Objectif</th>
        <th>Collecté</th>
        <th>Date limite</th>
        <th>Justificatif</th>
        <th>Statut</th>
        <th>Date</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($demandes as $d): ?>
        <tr>
          <td><?= htmlspecialchars($d['email']) ?></td>
          <td><?= htmlspecialchars($d['type_logement']) ?></td>
          <td><?= nl2br(htmlspecialchars($d['description'])) ?></td>
          <td><?= number_format($d['objectif_montant'], 0, ',', ' ') ?> FCFA</td>
          <td><?= number_format($d['montant_collecte'], 0, ',', ' ') ?> FCFA</td>
          <td><?= $d['date_limite'] ?: '-' ?></td>
          <td>
            <?php if ($d['justificatif']): ?>
              <a href="../uploads/justificatifs/<?= htmlspecialchars($d['justificatif']) ?>" target="_blank">Voir</a>
            <?php else: ?>
              Aucun
            <?php endif; ?>
          </td>
          <td>
            <?php
              $badge = match ($d['statut']) {
                'validé' => 'success',
                'en attente' => 'warning',
                'rejete' => 'danger',
                'clôturé' => 'secondary',
                default => 'light'
              };
            ?>
            <span class="badge bg-<?= $badge ?>"><?= ucfirst($d['statut']) ?></span>
          </td>
          <td><?= $d['date_soumission'] ?></td>
          <td>
            <?php if ($d['statut'] === 'en attente'): ?>
              <form method="post" style="display:inline-block" onsubmit="return confirm('Confirmer l\'action ?')">
                <input type="hidden" name="id_demande" value="<?= $d['id'] ?>">
                <button type="submit" name="action" value="valider" class="btn btn-sm btn-success">Valider</button>
                <button type="submit" name="action" value="rejeter" class="btn btn-sm btn-danger">Rejeter</button>
              </form>
            <?php else: ?>
              <em>Aucune action</em>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

</body>
</html>

<?php require_once '../includes/footer.php'; ?>
