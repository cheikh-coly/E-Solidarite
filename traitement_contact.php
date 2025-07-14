<?php
require_once 'includes/db_connexion.php'; // ← ceci charge $conn

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nom = htmlspecialchars(trim($_POST['nom']));
  $email = htmlspecialchars(trim($_POST['email']));
  $sujet = htmlspecialchars(trim($_POST['sujet']));
  $message = htmlspecialchars(trim($_POST['message']));

  // Insertion sécurisée
  $stmt = $conn->prepare("INSERT INTO messages_contact (nom, email, sujet, message) VALUES (?, ?, ?, ?)");
  if ($stmt->execute([$nom, $email, $sujet, $message])) {
    header("Location: contact.php?success=1");
    exit;
  } else {
    echo "Erreur lors de l'envoi du message.";
  }
} else {
  header("Location: contact.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Contact – Confirmation</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
  <div class="container py-5">
    <div class="alert alert-success" role="alert">
      Merci <strong><?= htmlspecialchars($nom) ?></strong>, votre message a bien été reçu.<br>
      Nous vous répondrons dans les plus brefs délais.
    </div>

    <a href="contact.php" class="btn btn-primary">Retour au formulaire</a>
  </div>
</body>
</html>
