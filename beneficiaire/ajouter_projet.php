<?php
session_start();
require_once '../includes/db_connexion.php';
require_once '../includes/header.php';

if (!isset($_SESSION['utilisateur']) || $_SESSION['role'] !== 'beneficiaire') {
    header("Location: ../connexion.php");
    exit();
}

$erreur = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $objectif = (int) $_POST['objectif'];
    $date_limite = $_POST['date_limite'];
    $utilisateur_id = $_SESSION['utilisateur']['id'];  // <-- correction ici

    // Validation simple
    if ($titre === '' || $description === '' || $objectif <= 0 || !$date_limite) {
        $erreur = "Tous les champs sont obligatoires et l'objectif doit être positif.";
    } else {
        $insert = $conn->prepare("INSERT INTO projets (titre, description, objectif, date_limite, utilisateur_id) VALUES (?, ?, ?, ?, ?)");
        if ($insert->execute([$titre, $description, $objectif, $date_limite, $utilisateur_id])) {
            $success = "Projet soumis avec succès. En attente de validation.";
        } else {
            $erreur = "Erreur lors de la soumission du projet.";
        }
    }
}

// Mettre à jour la colonne cloture automatiquement
$conn->query("
    UPDATE projets
    SET cloture = 1
    WHERE cloture = 0
    AND (
        (SELECT COALESCE(SUM(montant), 0) FROM dons WHERE dons.projet_id = projets.id) >= objectif
        OR date_limite < CURDATE()
    )
");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Proposer un projet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" >
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .form-container {
            background: #fff;
            max-width: 600px;
            margin: 50px auto 80px auto;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            font-weight: 700;
            margin-bottom: 30px;
            color: #0d6efd;
        }
        label {
            font-weight: 600;
            color: #444;
        }
        input[type="text"], input[type="number"], input[type="date"], textarea {
            border-radius: 8px;
            border: 1.5px solid #ced4da;
            transition: border-color 0.3s ease;
        }
        input[type="text"]:focus, input[type="number"]:focus, input[type="date"]:focus, textarea:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 8px rgba(13, 110, 253, 0.3);
            outline: none;
        }
        .btn-primary {
            background-color: #0d6efd;
            border: none;
            font-weight: 700;
            padding: 12px 25px;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #0847c5;
        }
        .btn-secondary {
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .btn-secondary:hover {
            background-color: #e2e6ea;
            color: #0d6efd;
        }
        .alert {
            border-radius: 8px;
            font-weight: 600;
            max-width: 600px;
            margin: 20px auto;
        }
    </style>
</head>
<body>

    <div class="form-container shadow-sm">
        <h2>Proposer un projet</h2>

        <?php if ($erreur): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="post" novalidate>
            <div class="mb-4">
                <label for="titre" class="form-label">Titre du projet</label>
                <input type="text" name="titre" id="titre" class="form-control" required value="<?= isset($_POST['titre']) ? htmlspecialchars($_POST['titre']) : '' ?>">
            </div>

            <div class="mb-4">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control" rows="5" required><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
            </div>

            <div class="mb-4">
                <label for="objectif" class="form-label">Objectif à collecter (FCFA)</label>
                <input type="number" name="objectif" id="objectif" class="form-control" min="1" required value="<?= isset($_POST['objectif']) ? (int)$_POST['objectif'] : '' ?>">
            </div>

            <div class="mb-4">
                <label for="date_limite" class="form-label">Date limite</label>
                <input type="date" name="date_limite" id="date_limite" class="form-control" required value="<?= isset($_POST['date_limite']) ? htmlspecialchars($_POST['date_limite']) : '' ?>">
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <button type="submit" class="btn btn-primary">Soumettre le projet</button>
                <a href="dashboard.php" class="btn btn-secondary">Retour</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
require_once '../includes/footer.php';
