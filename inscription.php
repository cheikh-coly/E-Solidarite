<?php
session_start();
require_once 'includes/db_connexion.php';

$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];
    $role = $_POST['role'];

    $verif = $conn->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $verif->execute([$email]);

    if ($verif->rowCount() > 0) {
        $erreur = "Cet email est déjà utilisé.";
    } else {
        $mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $insert = $conn->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)");
        $insert->execute([$nom, $email, $mot_de_passe_hache, $role]);

        $_SESSION['utilisateur'] = $conn->lastInsertId();
        header('Location: connexion.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Inscription</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/styles.css" rel="stylesheet">
  <style>
    body {
      background: #f4f7fa;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .register-card {
      width: 100%;
      max-width: 500px;
      padding: 30px;
      border-radius: 8px;
      background-color: white;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.08);
    }

    .register-card h2 {
      margin-bottom: 25px;
      font-weight: 600;
    }
  </style>
</head>
<body>

<div class="register-card">
  <h2 class="text-center text-primary">Créer un compte</h2>

  <?php if ($erreur): ?>
    <div class="alert alert-danger"><?= $erreur ?></div>
  <?php endif; ?>

  <form method="post" novalidate>
    <div class="mb-3">
      <label for="nom" class="form-label">Nom complet</label>
      <input type="text" name="nom" id="nom" class="form-control" required placeholder="Votre nom complet">
    </div>

    <div class="mb-3">
      <label for="email" class="form-label">Adresse email</label>
      <input type="email" name="email" id="email" class="form-control" required placeholder="exemple@mail.com">
    </div>

    <div class="mb-3">
      <label for="mot_de_passe" class="form-label">Mot de passe</label>
      <input type="password" name="mot_de_passe" id="mot_de_passe" class="form-control" required placeholder="********">
    </div>

    <div class="mb-3">
      <label for="role" class="form-label">Rôle</label>
      <select name="role" id="role" class="form-select" required>
        <option value="donateur">Donateur</option>
        <option value="beneficiaire">Bénéficiaire</option>
        <option value="admin">Administrateur</option>
      </select>
    </div>

    <div class="d-grid gap-2">
      <button type="submit" class="btn btn-primary">S'inscrire</button>
    </div>

    <div class="text-center mt-3">
      <a href="connexion.php" class="btn btn-link">Déjà inscrit ? Se connecter</a>
    </div>
  </form>
</div>

</body>
</html>
