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
    $type = trim($_POST['type_soin']);
    $desc = trim($_POST['description']);
    $date_limite = $_POST['date_limite'];
    $objectif = (int)$_POST['objectif'];
    $justificatif = "";

    // Validation simple
    if ($type === '' || $desc === '' || $objectif <= 0 || !$date_limite) {
        $erreur = "Tous les champs obligatoires doivent être correctement remplis.";
    } else {
        // Gestion du fichier justificatif si présent
        if (!empty($_FILES['justificatif']['name'])) {
            $uploadDir = "../uploads/justificatifs/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $nomFichier = uniqid() . '_' . basename($_FILES['justificatif']['name']);
            $targetFile = $uploadDir . $nomFichier;

            // Vérification type mime autorisé (pdf, jpg, jpeg, png)
            $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
            $fileType = mime_content_type($_FILES['justificatif']['tmp_name']);

            if (!in_array($fileType, $allowedTypes)) {
                $erreur = "Format de fichier non autorisé. Seuls PDF, JPG, PNG sont acceptés.";
            } else {
                if (!move_uploaded_file($_FILES['justificatif']['tmp_name'], $targetFile)) {
                    $erreur = "Erreur lors de l'upload du justificatif.";
                } else {
                    $justificatif = $nomFichier;
                }
            }
        }

        // Si pas d'erreur d'upload
        if (!$erreur) {
            $stmt = $conn->prepare("INSERT INTO demandes_sante (utilisateur_id, type_soin, description, justificatif, date_limite, objectif, statut, montant_collecte) VALUES (?, ?, ?, ?, ?, ?, 'en attente', 0)");
            $success = $stmt->execute([$_SESSION['utilisateur']['id'], $type, $desc, $justificatif, $date_limite, $objectif]);

            if ($success) {
                $message = "Demande de soins soumise avec succès.";
                // Réinitialiser les champs après soumission
                $_POST = [];
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
  <title>Demande de soins</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" >
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .container {
      max-width: 650px;
      background: #fff;
      padding: 30px 40px;
      margin-top: 60px;
      border-radius: 10px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    h1 {
      color: #0d6efd;
      font-weight: 700;
      margin-bottom: 30px;
      text-align: center;
    }
    label {
      font-weight: 600;
      color: #333;
    }
    input[type="text"],
    input[type="number"],
    input[type="date"],
    textarea,
    input[type="file"] {
      border-radius: 8px;
      border: 1.5px solid #ced4da;
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    input[type="text"]:focus,
    input[type="number"]:focus,
    input[type="date"]:focus,
    textarea:focus,
    input[type="file"]:focus {
      border-color: #0d6efd;
      box-shadow: 0 0 8px rgba(13,110,253,0.3);
      outline: none;
    }
    .btn-primary {
      background-color: #0d6efd;
      font-weight: 700;
      border-radius: 8px;
      padding: 12px 30px;
      transition: background-color 0.3s ease;
    }
    .btn-primary:hover {
      background-color: #0847c5;
    }
    .btn-secondary {
      border-radius: 8px;
      font-weight: 600;
      padding: 12px 30px;
      transition: background-color 0.3s ease, color 0.3s ease;
    }
    .btn-secondary:hover {
      background-color: #e2e6ea;
      color: #0d6efd;
    }
    .alert {
      border-radius: 8px;
      font-weight: 600;
      max-width: 650px;
      margin: 20px auto 30px auto;
      text-align: center;
    }
    form {
      margin: 0 auto;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Soumettre une demande de soins</h1>

    <?php if ($message): ?>
      <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($erreur): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" novalidate>
      <div class="mb-4">
        <label for="type_soin">Type de soins</label>
        <input type="text" name="type_soin" id="type_soin" class="form-control" required value="<?= isset($_POST['type_soin']) ? htmlspecialchars($_POST['type_soin']) : '' ?>">
      </div>

      <div class="mb-4">
        <label for="description">Description du besoin</label>
        <textarea name="description" id="description" class="form-control" rows="5" required><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
      </div>

      <div class="mb-4">
        <label for="date_limite">Date limite</label>
        <input type="date" name="date_limite" id="date_limite" class="form-control" required value="<?= isset($_POST['date_limite']) ? htmlspecialchars($_POST['date_limite']) : '' ?>">
      </div>

      <div class="mb-4">
        <label for="objectif">Objectif de collecte (FCFA)</label>
        <input type="number" name="objectif" id="objectif" class="form-control" required min="1" value="<?= isset($_POST['objectif']) ? (int)$_POST['objectif'] : '' ?>">
      </div>

      <div class="mb-4">
        <label for="justificatif">Justificatif (facultatif)</label>
        <input type="file" name="justificatif" id="justificatif" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
      </div>

      <div class="d-flex justify-content-start align-items-center">
        <button type="submit" class="btn btn-primary">Soumettre</button>
        <a href="dashboard.php" class="btn btn-secondary ms-3">Retour</a>
      </div>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
require_once '../includes/footer.php';
?>
