<?php
session_start();
require_once 'includes/db_connexion.php';


$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($mot_de_passe, $user['mot_de_passe'])) {
            $_SESSION['utilisateur'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            switch ($user['role']) {
                case 'admin': header("Location: dashboard.php"); break;
                case 'beneficiaire': header("Location: dashboard.php"); break;
                case 'donateur': header("Location: dashboard.php"); break;
                default: header("Location: index.php");
            }
            exit();
        } else {
            $erreur = "Mot de passe incorrect.";
        }
    } else {
        $erreur = "Email introuvable.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Connexion</title>
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

    .login-card {
      width: 100%;
      max-width: 420px;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
      background-color: white;
    }

    .login-card h2 {
      margin-bottom: 25px;
      font-weight: 600;
    }
  </style>
</head>
<body>

<div class="login-card">
  <h2 class="text-center text-primary">Connexion</h2>

  <?php if ($erreur): ?>
    <div class="alert alert-danger"><?= $erreur ?></div>
  <?php endif; ?>

  <form method="post" novalidate>
    <div class="mb-3">
      <label for="email" class="form-label">Adresse email</label>
      <input type="email" name="email" id="email" class="form-control" required placeholder="votre@email.com">
    </div>

    <div class="mb-3">
      <label for="mot_de_passe" class="form-label">Mot de passe</label>
      <input type="password" name="mot_de_passe" id="mot_de_passe" class="form-control" required placeholder="********">
    </div>

    <div class="d-grid gap-2 mb-3">
      <button type="submit" class="btn btn-success">Se connecter</button>
    </div>

    <div class="text-center">
      <a href="inscription.php" class="btn btn-link">Créer un compte</a> |
      <a href="#">Mot de passe oublié ?</a>
    </div>
  </form>
</div>

</body>
</html>
