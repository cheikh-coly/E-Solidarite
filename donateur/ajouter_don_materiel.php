<?php
session_start();
require_once '../includes/db_connexion.php';
require_once '../includes/header.php';

if (!isset($_SESSION['utilisateur']) || $_SESSION['role'] !== 'donateur') {
    header('Location: ../connexion.php');
    exit;
}

$donateur_id = $_SESSION['utilisateur'];
$message = "";

// Supprimer un don (via GET ?delete_id=xx)
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    // Vérifier que le don appartient bien à l'utilisateur
    $stmtCheck = $conn->prepare("SELECT id FROM dons_materiels WHERE id = ? AND utilisateur_id = ?");
    $stmtCheck->execute([$delete_id, $donateur_id]);
    if ($stmtCheck->rowCount() > 0) {
        $stmtDel = $conn->prepare("DELETE FROM dons_materiels WHERE id = ?");
        $stmtDel->execute([$delete_id]);
        $message = "Don matériel supprimé avec succès.";
    } else {
        $message = "Don non trouvé ou accès refusé.";
    }
}

// Traitement du formulaire d'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom_objet'];
    $etat = $_POST['etat_objet'];
    $desc = $_POST['description'];
    $image = "";

    if (!empty($_FILES['image_objet']['name'])) {
        $nomFichier = uniqid() . '_' . basename($_FILES['image_objet']['name']);
        $uploadDir = '../uploads/materiels/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        move_uploaded_file($_FILES['image_objet']['tmp_name'], $uploadDir . $nomFichier);
        $image = $nomFichier;
    }

    $stmt = $conn->prepare("INSERT INTO dons_materiels (utilisateur_id, nom_objet, etat_objet, description, image_objet, statut, date_soumission) VALUES (?, ?, ?, ?, ?, 'en attente', NOW())");
    $stmt->execute([$donateur_id, $nom, $etat, $desc, $image]);

    $message = "Don matériel proposé avec succès.";
}

// Récupérer les dons déjà proposés avec statut
$dons_existants = $conn->prepare("SELECT * FROM dons_materiels WHERE utilisateur_id = ? ORDER BY date_soumission DESC");
$dons_existants->execute([$donateur_id]);
$dons = $dons_existants->fetchAll(PDO::FETCH_ASSOC);

function badgeClass($statut) {
    return match ($statut) {
        'accepté' => 'success',
        'rejeté' => 'danger',
        'en attente' => 'warning',
        default => 'secondary',
    };
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Proposer un don matériel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .success-animation {
        animation: fadeIn 1s ease-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.9); }
      to { opacity: 1; transform: scale(1); }
    }
    .card-img-top {
        height: 180px;
        object-fit: cover;
    }
  </style>
  <script>
    function confirmDelete() {
      return confirm('Voulez-vous vraiment supprimer ce don matériel ?');
    }
  </script>
</head>
<body class="container py-5">
  <h1 class="mb-4">Proposer un don matériel</h1>

  <?php if ($message): ?>
    <div class="alert alert-success success-animation">
      <strong>✅ <?= htmlspecialchars($message) ?></strong>
    </div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" class="mb-5">
    <div class="mb-3">
      <label for="nom_objet" class="form-label">Nom de l’objet</label>
      <input type="text" name="nom_objet" class="form-control" required>
    </div>
    <div class="mb-3">
      <label for="etat_objet" class="form-label">État</label>
      <input type="text" name="etat_objet" class="form-control" required>
    </div>
    <div class="mb-3">
      <label for="description" class="form-label">Description</label>
      <textarea name="description" class="form-control" rows="4" required></textarea>
    </div>
    <div class="mb-3">
      <label for="image_objet" class="form-label">Photo de l’objet (facultatif)</label>
      <input type="file" name="image_objet" class="form-control" accept="image/*">
    </div>
    <button type="submit" class="btn btn-primary">Soumettre</button>
    <a href="../module_dons_materiels.php" class="btn btn-secondary">Retour</a>
  </form>

  <?php if ($dons): ?>
    <h3 class="mb-3">Mes dons matériels proposés</h3>
    <div class="row">
      <?php foreach ($dons as $don): ?>
        <div class="col-md-4 mb-4">
          <div class="card h-100">
            <?php if (!empty($don['image_objet'])): ?>
              <img src="../uploads/materiels/<?= htmlspecialchars($don['image_objet']) ?>" class="card-img-top" alt="Objet">
            <?php else: ?>
              <img src="../assets/img/default-image.jpg" class="card-img-top" alt="Aucune image">
            <?php endif; ?>
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?= htmlspecialchars($don['nom_objet']) ?></h5>
              <p class="card-text"><strong>État :</strong> <?= htmlspecialchars($don['etat_objet']) ?></p>
              <p class="card-text"><?= nl2br(htmlspecialchars($don['description'])) ?></p>
              <span class="badge bg-<?= badgeClass($don['statut']) ?> mb-2 align-self-start text-capitalize"><?= htmlspecialchars($don['statut']) ?></span>
              <div class="mt-auto d-flex justify-content-between align-items-center">
                <small class="text-muted">Soumis le <?= date('d/m/Y', strtotime($don['date_soumission'])) ?></small>
                <a href="?delete_id=<?= $don['id'] ?>" onclick="return confirmDelete()" class="btn btn-sm btn-danger">Supprimer</a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="text-muted">Vous n'avez pas encore proposé de dons matériels.</p>
  <?php endif; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php require_once '../includes/footer.php'; ?>
