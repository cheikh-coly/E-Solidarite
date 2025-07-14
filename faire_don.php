<?php
session_start();
require_once 'includes/db_connexion.php';

if (!isset($_SESSION['utilisateur']) || $_SESSION['role'] !== 'donateur') {
    header("Location: connexion.php");
    exit();
}

// Paramètres GET
$type_don = $_GET['type'] ?? '';
$element_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$type_don || !$element_id) {
    echo "<p class='text-danger text-center mt-5'>Erreur : projet ou demande non spécifiée.</p>";
    exit();
}

switch ($type_don) {
    case 'projet':
        $data = $conn->prepare("SELECT titre, objectif, montant_collecte FROM projets WHERE id = ? AND statut = 'validé'");
        break;
    case 'sante':
        $data = $conn->prepare("SELECT description AS titre, objectif AS objectif, montant_collecte FROM demandes_sante WHERE id = ? AND statut = 'validé'");
        break;
    case 'logement':
        $data = $conn->prepare("SELECT description AS titre, objectif_montant AS objectif, montant_collecte FROM demandes_logement WHERE id = ? AND statut = 'validé'");
        break;
    default:
        echo "<p class='text-danger text-center mt-5'>Type de don invalide.</p>";
        exit();
}
$data->execute([$element_id]);
$element = $data->fetch();

if (!$element) {
    echo "<p class='text-danger text-center mt-5'>Élément non trouvé.</p>";
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $montant = (float)($_POST['montant'] ?? 0);
    $donateur_id = $_SESSION['utilisateur']['id']; // Correction ici, on récupère bien l'ID

    if ($montant < 100) {
        $message = "Le montant doit être au moins de 100 FCFA.";
    } else {
        switch ($type_don) {
            case 'projet':
                $stmt = $conn->prepare("INSERT INTO dons (projet_id, utilisateur_id, montant, date_don) VALUES (?, ?, ?, NOW())");
                break;
            case 'sante':
                $stmt = $conn->prepare("INSERT INTO dons_sante (demande_sante_id, utilisateur_id, montant, date_don) VALUES (?, ?, ?, NOW())");
                break;
            case 'logement':
                $stmt = $conn->prepare("INSERT INTO dons_logement (demande_logement_id, utilisateur_id, montant, date_don) VALUES (?, ?, ?, NOW())");
                break;
        }
        $stmt->execute([$element_id, $donateur_id, $montant]);

        // Mise à jour du montant collecté dans la table concernée
        $update = null;
        switch ($type_don) {
            case 'projet':
                $update = $conn->prepare("UPDATE projets SET montant_collecte = montant_collecte + ? WHERE id = ?");
                break;
            case 'sante':
                $update = $conn->prepare("UPDATE demandes_sante SET montant_collecte = montant_collecte + ? WHERE id = ?");
                break;
            case 'logement':
                $update = $conn->prepare("UPDATE demandes_logement SET montant_collecte = montant_collecte + ? WHERE id = ?");
                break;
        }
        if ($update) {
            $update->execute([$montant, $element_id]);
        }

        echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><title>Merci !</title>
        <meta http-equiv="refresh" content="5;url=index.php">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>.icon-check {font-size: 60px; color: green; animation: pop 0.6s ease;}
        @keyframes pop {0% {transform: scale(0);} 80% {transform: scale(1.2);} 100% {transform: scale(1);}}</style>
        </head><body class="d-flex justify-content-center align-items-center vh-100 bg-light">
        <div class="text-center"><div class="icon-check mb-3">✅</div>
        <h2>Merci pour votre don !</h2><p>Redirection dans 5 secondes...</p>
        <a href="index.php" class="btn btn-outline-primary mt-3">Retour accueil</a></div></body></html>';
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Faire un don</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .progress { height: 20px; }
    .preset-buttons button { margin-right: 10px; margin-bottom: 10px; }
  </style>
</head>
<body class="container py-5">

  <h2 class="mb-4 text-center text-primary">Faire un don pour : <?= htmlspecialchars($element['titre']) ?></h2>

  <?php if ($message): ?>
    <div class="alert alert-danger text-center"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <form method="post" class="mx-auto" style="max-width: 600px;">
    <input type="hidden" name="type_don" value="<?= htmlspecialchars($type_don) ?>">
    <input type="hidden" name="element_id" value="<?= (int)$element_id ?>">

    <div class="mb-3">
      <label class="form-label fw-bold">Progression du financement</label>
      <?php
        $objectif = $element['objectif'];
        $collecte = $element['montant_collecte'];
        $percent = ($objectif > 0) ? min(100, round($collecte / $objectif * 100)) : 0;
      ?>
      <div class="progress mb-1">
        <div class="progress-bar bg-success" role="progressbar" style="width: <?= $percent ?>%" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"><?= $percent ?>%</div>
      </div>
      <small class="text-muted"><?= number_format($collecte, 0, ',', ' ') ?> / <?= number_format($objectif, 0, ',', ' ') ?> FCFA</small>
    </div>

    <div class="mb-3">
      <label for="montant" class="form-label fw-bold">Montant à donner</label>
      <div class="preset-buttons mb-2">
        <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('montant').value=1000">1000 FCFA</button>
        <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('montant').value=2000">2000 FCFA</button>
        <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('montant').value=5000">5000 FCFA</button>
      </div>
      <input type="number" name="montant" id="montant" class="form-control" placeholder="Autre montant" min="100" required>
    </div>

    <button type="submit" class="btn btn-success w-100 fw-bold">Valider mon don</button>
  </form>

</body>
</html>
