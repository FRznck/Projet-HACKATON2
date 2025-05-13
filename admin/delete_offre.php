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
    $offre_id = intval($_GET['id']);
    $query = "DELETE FROM offres WHERE id_offre = $offre_id";
    if (mysqli_query($conn, $query)) {
        header("Location: liste-offres.php?success=1");
        exit();
    } else {
        header("Location: liste-offres.php?error=1");
        exit();
    }
} else {
    header("Location: liste-offres.php");
    exit();
}
?>