<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['email'])) {
    header("Location: connexion.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "root", "hackathon");
if (!$conn) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

// Récupération CORRECTE de l'ID via l'email de la session
$email = $_SESSION['email'];
$query = "SELECT id_utilisateur FROM utilisateurs WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Utilisateur introuvable");
}

$user_id = $result->fetch_assoc()['id_utilisateur'];
$stmt->close();

// Mise à jour avec le bon user_id
$sql = "UPDATE notifications SET is_read = 1 WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);

if (!$stmt->execute()) {
    die("Erreur : " . $stmt->error);
}

$stmt->close();
$conn->close();

header("Location: dashboard-candidat.php");
exit();