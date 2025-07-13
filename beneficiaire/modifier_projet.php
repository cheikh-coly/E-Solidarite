<?php
session_start();
require_once '../includes/db_connexion.php';
require_once '../includes/header.php';

// Vérifier utilisateur connecté et bénéficiaire
if (!isset($_SESSION['utilisateur']) || $_SESSION['role'] !== 'beneficiaire') {
    header("Location: ../connexion.php");
    exit();
}

$id_user = is_array($_SESSION['utilisateur']) ? $_SESSION['utilisateur']['id'] : $_SESSION['utilisateur'];
$id_projet = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id_projet <= 0) {
    header("Location: dashboard_beneficiaire.php");
    exit();
}

// Vérifier que le projet appartient à l'utilisateur et est modifiable
$stmt = $conn->prepare("SELECT * FROM projets WHERE id = ? AND utilisateur_id = ? AND statut = 'en attente'");
$stmt->execute([$id_projet, $id_user]);
$projet = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$projet) {
    header("Location: dashboard_beneficiaire.php");
    exit();
}

$erreur = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $objectif = (int) $_POST['objectif'];
    $date_limite = $_POST['date_limite'];

    if ($titre === '' || $description === '' || $objectif <= 0 || !$date_limite) {
        $erreur = "Tous les champs sont obligatoires et valides.";
    } else {
        try {
            $update = $conn->prepare("UPDATE projets SET titre = ?, description = ?, objectif = ?, date_limite = ? WHERE id = ? AND utilisateur_id = ?");
            if ($update->execute([$titre, $description, $objectif, $date_limite, $id_projet, $id_user])) {
                $success = "Projet mis à jour avec succès.";
                $stmt->execute([$id_projet, $id_user]);
                $projet = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $erreur = "Erreur lors de la mise à jour.";
            }
        } catch (PDOException $e) {
            $erreur = "Erreur SQL : " . $e->getMessage(); // À désactiver en prod
        }
    }
}
?>
