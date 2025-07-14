<?php
session_start();
require_once '../includes/db_connexion.php';

if (!isset($_SESSION['utilisateur']) || $_SESSION['role'] !== 'beneficiaire') {
    header("Location: ../connexion.php");
    exit();
}

// Gestion sécurisée de l'ID utilisateur selon ta session
$id_user = is_array($_SESSION['utilisateur']) ? $_SESSION['utilisateur']['id'] : $_SESSION['utilisateur'];

$id_projet = (int)($_GET['id'] ?? 0);
if ($id_projet <= 0) {
    header("Location: dashboard_beneficiaire.php");
    exit();
}

$stmt = $conn->prepare("DELETE FROM projets WHERE id = ? AND utilisateur_id = ? AND statut = 'en attente'");
$deleted = $stmt->execute([$id_projet, $id_user]);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Suppression du projet</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" >
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }
    .confirmation-box {
      background: #fff;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.12);
      text-align: center;
      max-width: 450px;
    }
    h2 {
      color: #dc3545;
      margin-bottom: 20px;
      font-weight: 700;
    }
    p {
      font-size: 1.1rem;
      margin-bottom: 30px;
      color: #333;
    }
    .btn-primary {
      background-color: #dc3545;
      border: none;
      font-weight: 600;
      padding: 10px 30px;
      border-radius: 8px;
      transition: background-color 0.3s ease;
      text-decoration: none;
      color: white;
    }
    .btn-primary:hover {
      background-color: #a71d2a;
    }
  </style>
  <meta http-equiv="refresh" content="3;url=dashboard_beneficiaire.php" />
</head>
<body>
  <div class="confirmation-box">
    <?php if ($deleted): ?>
      <h2>Projet supprimé</h2>
      <p>Votre projet a bien été supprimé. Vous allez être redirigé vers votre tableau de bord.</p>
      <a href="dashboard_beneficiaire.php" class="btn-primary">Retour immédiat</a>
    <?php else: ?>
      <h2>Suppression impossible</h2>
      <p>Le projet n'a pas pu être supprimé. Il est peut-être déjà validé ou n'existe plus.</p>
      <a href="dashboard_beneficiaire.php" class="btn-primary">Retour au tableau de bord</a>
    <?php endif; ?>
  </div>
</body>
</html>

<?php
require_once '../includes/footer.php';
?>
