<?php
session_start();
$conn = mysqli_connect("localhost", "root", "root", "hackathon");
if (!$conn) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

if (!isset($_SESSION['email'])) {
    header("Location: ../connexion.php");
    exit();
}

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    // verification si le champ is_blocked existe, sinon onl'ajouter dans la base de données
    $query = "UPDATE utilisateurs SET is_blocked = 1 WHERE id_utilisateur = $user_id";
    if (mysqli_query($conn, $query)) {
        header("Location: liste-user.php?success=1");
        exit();
    } else {
        header("Location: liste-user.php?error=1");
        exit();
    }
} else {
    header("Location: liste-user.php");
    exit();
}
?>