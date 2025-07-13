<?php
session_start();
require_once '../includes/db_connexion.php';
require_once '../includes/header.php';

if (!isset($_SESSION['utilisateur']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../connexion.php");
    exit();
}

// ⚠️ Clôture automatique si date limite dépassée ou montant atteint
$conn->query("
    UPDATE demandes_sante
    SET statut = 'clôturé'
    WHERE statut = 'validé' AND (
        (date_limite IS NOT NULL AND date_limite < CURDATE()) OR
        (objectif_montant > 0 AND montant_collecte >= objectif_montant)
    )
");

// Traitement validation ou rejet
if (isset($_GET['action'], $_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];

    if (in_array($action, ['valider', 'rejeter'])) {
        $statut = $action === 'valider' ? 'validé' : 'rejeté';
        $stmt = $conn->prepare("UPDATE demandes_sante SET statut = ? WHERE id = ?");
        $stmt->execute([$statut, $id]);
    }
    header("Location: valider_demandes_sante.php");
    exit();
}

// Récupérations groupées
function getDemandesByStatut($conn, $statut) {
    $stmt = $conn->prepare("
        SELECT ds.*, u.nom AS nom_user
        FROM demandes_sante ds
        JOIN utilisateurs u ON ds.utilisateur_id = u.id
        WHERE ds.statut = ?
        ORDER BY ds.date_soumission DESC
    ");
    $stmt->execute([$statut]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$demandes_en_attente = getDemandesByStatut($conn, 'en attente');
$demandes_valides = getDemandesByStatut($conn, 'validé');
$demandes_rejetees = getDemandesByStatut($conn, 'rejeté');
$demandes_cloturees = getDemandesByStatut($conn, 'clôturé');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Validation des demandes de soins</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/styles.css" rel="stylesheet">
</head>
<body class="container mt-5">
  <h2>Demandes de soins en attente</h2>
  <?php if (empty($demandes_en_attente)): ?>
    <div class="alert alert-info">Aucune demande en attente.</div>
  <?php else: ?>
    <table class="table table-bordered">
      <thead class="table-light">
        <tr>
          <th>Utilisateur</th>
          <th>Type de soins</th>
          <th>Description</th>
          <th>Montant collecté</th>
          <th>Objectif</th>
          <th>Date limite</th>
          <th>Justificatif</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($demandes_en_attente as $d): ?>
          <tr>
            <td><?= htmlspecialchars($d['nom_user']) ?></td>
            <td><?= htmlspecialchars($d['type_soin']) ?></td>
            <td style="max-width:250px"><?= nl2br(htmlspecialchars(substr($d['description'], 0, 80))) ?>...</td>
            <td><?= number_format($d['montant_collecte'], 0, ',', ' ') ?> FCFA</td>
            <td><?= number_format($d['objectif_montant'], 0, ',', ' ') ?> FCFA</td>
            <td><?= $d['date_limite'] ?? '—' ?></td>
            <td>
              <?php if ($d['justificatif']): ?>
                <a href="../uploads/justificatifs/<?= htmlspecialchars($d['justificatif']) ?>" target="_blank">Voir</a>
              <?php else: ?>
                Aucun
              <?php endif; ?>
            </td>
            <td><?= $d['date_creation'] ?? '—' ?></td>
            <td>
              <a href="?action=valider&id=<?= $d['id'] ?>" class="btn btn-success btn-sm">Valider</a>
              <a href="?action=rejeter&id=<?= $d['id'] ?>" class="btn btn-danger btn-sm">Rejeter</a>
            </td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  <?php endif; ?>

  <hr class="my-5">
  <h3>Demandes validées</h3>
  <?php if (empty($demandes_valides)): ?>
    <p>Aucune demande validée.</p>
  <?php else: ?>
    <ul class="list-group">
      <?php foreach ($demandes_valides as $d): ?>
        <li class="list-group-item">
          <?= htmlspecialchars($d['type_soin']) ?> – par <?= htmlspecialchars($d['nom_user']) ?> (<?= $d['montant_collecte'] ?> / <?= $d['objectif_montant'] ?> FCFA)
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <hr class="my-5">
  <h3>Demandes rejetées</h3>
  <?php if (empty($demandes_rejetees)): ?>
    <p>Aucune demande rejetée.</p>
  <?php else: ?>
    <ul class="list-group">
      <?php foreach ($demandes_rejetees as $d): ?>
        <li class="list-group-item list-group-item-danger">
          <?= htmlspecialchars($d['type_soin']) ?> – par <?= htmlspecialchars($d['nom_user']) ?>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <hr class="my-5">
  <h3>Demandes clôturées automatiquement</h3>
  <?php if (empty($demandes_cloturees)): ?>
    <p>Aucune demande clôturée.</p>
  <?php else: ?>
    <ul class="list-group">
      <?php foreach ($demandes_cloturees as $d): ?>
        <li class="list-group-item list-group-item-secondary">
          <?= htmlspecialchars($d['type_soin']) ?> – <?= htmlspecialchars($d['nom_user']) ?> (<?= $d['montant_collecte'] ?> / <?= $d['objectif_montant'] ?> FCFA – jusqu’au <?= $d['date_limite'] ?>)
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</body>
</html>

<?php require_once '../includes/footer.php'; ?>
