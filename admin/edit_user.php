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

// Récupérer l'utilisateur à modifier
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM utilisateurs WHERE id_utilisateur = $id";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $query = "UPDATE utilisateurs SET nom = '$nom', prenom = '$prenom', email = '$email', role = '$role' WHERE id_utilisateur = $id";
    mysqli_query($conn, $query);
    
    header("Location: liste-user.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier utilisateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Modifier l'utilisateur</h1>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $user['id_utilisateur'] ?>">
            
            <div class="mb-3">
                <label class="form-label">Nom</label>
                <input type="text" class="form-control" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Prénom</label>
                <input type="text" class="form-control" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Rôle</label>
                <select class="form-select" name="role" required>
                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="recruteur" <?= $user['role'] === 'recruteur' ? 'selected' : '' ?>>Recruteur</option>
                    <option value="candidat" <?= $user['role'] === 'candidat' ? 'selected' : '' ?>>Candidat</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="liste-user.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</body>
</html>