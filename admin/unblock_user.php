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
    $query = "UPDATE utilisateurs SET is_blocked = 0 WHERE id_utilisateur = $user_id";
    if (mysqli_query($conn, $query)) {
        header("Location: liste-user.php?success=2");
        exit();
    } else {
        header("Location: liste-user.php?error=2");
        exit();
    }
} else {
    header("Location: liste-user.php");
    exit();
}
?>