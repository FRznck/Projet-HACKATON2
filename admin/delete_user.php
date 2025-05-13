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
    $id = $_GET['id'];
    $query = "DELETE FROM utilisateurs WHERE id_utilisateur = $id";
    mysqli_query($conn, $query);
}

header("Location: liste-user.php");
exit();
?>