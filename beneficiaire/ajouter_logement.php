<?php
session_start();
require_once '../includes/db_connexion.php';
require_once '../includes/header.php';

if (!isset($_SESSION['utilisateur'])) {
    header('Location: connexion.php');
    exit;
}

$message = "";
$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = trim($_POST['type_logement']);
    $desc = trim($_POST['description']);
    $objectif = (int) $_POST['objectif'];
    $date_limite = $_POST['date_limite'] ?: null;
    $justificatif = "";

    // Récupérer l'ID utilisateur en fonction de la structure de ta session
    // Par exemple, si $_SESSION['utilisateur'] est un tableau associatif contenant 'id':
    $utilisateur_id = is_array($_SESSION['utilisateur']) ? $_SESSION['utilisateur']['id'] : $_SESSION['utilisateur'];

    // Validation basique
    if ($type === '' || $desc === '' || $objectif <= 0 || !$date_limite) {
        $erreur = "Tous les champs obligatoires doivent être remplis correctement.";
    } else {
        // Gestion du fichier justificatif (facultatif)
        if (!empty($_FILES['justificatif']['name'])) {
            $uploadDir = "../uploads/justificatifs/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $nomFichier = uniqid() . '_' . basename($_FILES['justificatif']['name']);
            $targetFile = $uploadDir . $nomFichier;

            $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
            $fileType = mime_content_type($_FILES['justificatif']['tmp_name']);

            if (!in_array($fileType, $allowedTypes)) {
                $erreur = "Format de fichier non autorisé. Seuls PDF, JPG, PNG sont acceptés.";
            } elseif (!move_uploaded_file($_FILES['justificatif']['tmp_name'], $targetFile)) {
                $erreur = "Erreur lors de l'upload du justificatif.";
            } else {
                $justificatif = $nomFichier;
            }
        }

        if (!$erreur) {
            $stmt = $conn->prepare("INSERT INTO demandes_logement
                (utilisateur_id, type_logement, description, justificatif, objectif_montant, date_limite)
                VALUES (?, ?, ?, ?, ?, ?)");

            if ($stmt->execute([$utilisateur_id, $type, $desc, $justificatif, $objectif, $date_limite])) {
                $message = "Demande de logement soumise avec succès.";
                $_POST = []; // Reset form values
            } else {
                $erreur = "Erreur lors de la soumission de la demande.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Demande de logement</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" >
  <style>
    /* Ton style ici (pas changé) */
  </style>
</head>
<body>
  <div class="container">
    <h1>Demander une aide au logement</h1>

    <?php if ($message): ?>
      <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($erreur): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" novalidate>
      <div class="mb-4">
        <label for="type_logement">Type de logement souhaité</label>
        <input type="text" name="type_logement" id="type_logement" class="form-control" required value="<?= isset($_POST['type_logement']) ? htmlspecialchars($_POST['type_logement']) : '' ?>">
      </div>

      <div class="mb-4">
        <label for="description">Description de votre situation</label>
        <textarea name="description" id="description" class="form-control" rows="5" required><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
      </div>

      <div class="mb-4">
        <label for="objectif">Objectif à collecter (en FCFA)</label>
        <input type="number" name="objectif" id="objectif" class="form-control" required min="1" step="100" value="<?= isset($_POST['objectif']) ? (int)$_POST['objectif'] : '' ?>">
      </div>

      <div class="mb-4">
        <label for="date_limite">Date limite de la demande</label>
        <input type="date" name="date_limite" id="date_limite" class="form-control" required value="<?= isset($_POST['date_limite']) ? htmlspecialchars($_POST['date_limite']) : '' ?>">
      </div>

      <div class="mb-4">
        <label for="justificatif">Justificatif (facultatif)</label>
        <input type="file" name="justificatif" id="justificatif" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
      </div>

      <div class="d-flex justify-content-start align-items-center">
        <button type="submit" class="btn btn-primary">Soumettre</button>
        <a href="module_logement.php" class="btn btn-secondary ms-3">Retour</a>
      </div>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php require_once '../includes/footer.php'; ?>
