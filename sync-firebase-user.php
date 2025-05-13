<?php
session_start();
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$conn = mysqli_connect("localhost", "root", "root", "hackathon");

// on verifie si l'utilisateur existe déjà
$stmt = mysqli_prepare($conn, "SELECT * FROM utilisateurs WHERE email = ?");
mysqli_stmt_bind_param($stmt, "s", $data['email']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) === 0) {

    $sql = "INSERT INTO utilisateurs (id_utilisateur, email, auth_method) VALUES (?, ?, 'firebase')";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $data['uid'], $data['email']);
    mysqli_stmt_execute($stmt);
}


$_SESSION = [
    "id_utilisateur" => $data['uid'],
    "email" => $data['email'],
    "auth_method" => "firebase"
];

echo json_encode(["success" => true]);