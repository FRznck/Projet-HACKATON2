<?php
session_start();
header('Content-Type: application/json');

// 1. Récupération des données
$input = json_decode(file_get_contents('php://input'), true);

$uid = $input['uid'] ?? null;
$email = filter_var($input['email'] ?? null, FILTER_VALIDATE_EMAIL);
$displayName = $input['displayName'] ?? '';
$photoURL = $input['photoURL'] ?? '';

// 2. Validation basique
if (!$uid || !$email) {
    http_response_code(400);
    echo json_encode(['error' => 'Données manquantes']);
    exit;
}

// 3. Connexion BDD
$conn = mysqli_connect("localhost", "root", "root", "hackathon");
if (!$conn) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur BDD']);
    exit;
}

// 4. Vérification existence utilisateur
$stmt = mysqli_prepare($conn, "SELECT * FROM utilisateurs WHERE email = ?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    $_SESSION['user'] = mysqli_fetch_assoc($result);
    echo json_encode(['success' => true]);
    exit;
}

// 5. Préparation des données
$names = explode(' ', $displayName, 2);
$prenom = $names[0] ?? '';
$nom = $names[1] ?? '';

// 6. Insertion sécurisée
$sql = "INSERT INTO utilisateurs (
    email, 
    nom, 
    prenom, 
    avatar, 
    role, 
    type_contrat, 
    disponibilite,
    telephone,
    localisation,
    firebase_uid
) VALUES (
    ?, ?, ?, ?, 
    'candidat', 
    'Non spécifié', 
    'Non spécifié',
    'Non renseigné',
    'Non renseigné',
    ?
)";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sssss", $email, $nom, $prenom, $photoURL, $uid);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['user'] = [
        'id_utilisateur' => mysqli_insert_id($conn),
        'email' => $email,
        'nom' => $nom,
        'prenom' => $prenom,
        'role' => 'candidat'
    ];
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur création compte: '.mysqli_error($conn)]);
}

mysqli_close($conn);