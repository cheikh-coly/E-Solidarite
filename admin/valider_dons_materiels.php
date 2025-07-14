<?php
session_start();
require_once '../includes/db_connexion.php';
require_once '../includes/header.php';

// Vérifier que l'utilisateur est admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../connexion.php');
    exit;
}

// Valider ou rejeter un don si formulaire soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['don_id'], $_POST['action'])) {
    $don_id = (int) $_POST['don_id'];
    $action = $_POST['action'];

   if (in_array($action, ['accepté', 'refusé'])) {
    $stmt = $conn->prepare("UPDATE dons_materiels SET statut = ? WHERE id = ?");
    $stmt->execute([$action, $don_id]);
}

}

// Récupérer les dons en attente
$stmt = $conn->query("SELECT dm.*, u.email FROM dons_materiels dm JOIN utilisateurs u ON dm.utilisateur_id = u.id WHERE statut = 'en attente' ORDER BY date_soumission ASC");
$dons = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Validation des dons matériels - Admin</title>
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" >
  <link href="css/styles.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script></head>
<body class="container py-5">
  <h1>Validation des dons matériels</h1>

  <?php if (empty($dons)): ?>
    <p>Aucun don matériel en attente de validation.</p>
  <?php else: ?>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>ID</th>
          <th>Utilisateur (email)</th>
          <th>Nom objet</th>
          <th>État</th>
          <th>Description</th>
          <th>Image</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($dons as $don): ?>
        <tr>
          <td><?= $don['id'] ?></td>
          <td><?= htmlspecialchars($don['email']) ?></td>
          <td><?= htmlspecialchars($don['nom_objet']) ?></td>
          <td><?= htmlspecialchars($don['etat_objet']) ?></td>
          <td><?= nl2br(htmlspecialchars($don['description'])) ?></td>
          <td>
            <?php if (!empty($don['image_objet'])): ?>
              <img src="../uploads/materiels/<?= htmlspecialchars($don['image_objet']) ?>" alt="Image" style="max-width:100px;"/>
            <?php else: ?>
              N/A
            <?php endif; ?>
          </td>
          <td><?= date('d/m/Y H:i', strtotime($don['date_soumission'])) ?></td>
          <td>
            <form method="post" style="display:inline-block;">
              <input type="hidden" name="don_id" value="<?= $don['id'] ?>">
              <input type="hidden" name="action" value="accepté">
              <button type="submit" class="btn btn-success btn-sm">Valider</button>
            </form>
            <form method="post" style="display:inline-block;">
              <input type="hidden" name="don_id" value="<?= $don['id'] ?>">
              <input type="hidden" name="action" value="refusé">
              <button type="submit" class="btn btn-danger btn-sm">Rejeter</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
require_once '../includes/footer.php';
