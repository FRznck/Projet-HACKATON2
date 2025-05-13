<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
$conn = mysqli_connect("localhost", "root", "root", "hackathon");
if (!$conn) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

if (!isset($_SESSION['email'])) {
    header("Location: ../connexion.php");
    exit();
}

// Traitement du formulaire d'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $type_contrat = $_POST['type_contrat'];
    $disponibilite = $_POST['disponibilite'];
    $competences = $_POST['competences'];
    $telephone = $_POST['telephone'];
    $localisation = $_POST['localisation'];

    $query = "INSERT INTO utilisateurs (nom, prenom, email, role, mot_de_passe, type_contrat, disponibilite, competences, telephone, localisation) 
              VALUES ('$nom', '$prenom', '$email', '$role', '$password', '$type_contrat', '$disponibilite', '$competences', '$telephone', '$localisation')";
    mysqli_query($conn, $query);
    
    header("Location: liste-user.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ajouter utilisateur - Plateforme de recrutement intelligente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .custom-section {
            padding: 3rem 0;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg fixed-top bg-light shadow-sm">
        <div class="container">
            <div class="rounded-circle bg-opacity-10 bg-dark" style="width: 40px; height: 40px"></div>
            <h1 class="h3 mb-0 mx-3">Ajouter un utilisateur</h1>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a href="liste-user.php" class="btn btn-outline-dark">Retour à la liste</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container-fluid" style="margin-top: 80px;">
        <section class="custom-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <form method="POST" class="p-4 bg-white rounded shadow-sm">
                            <h2 class="h4 mb-4">Informations de l'utilisateur</h2>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nom</label>
                                    <input type="text" class="form-control" name="nom" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Prénom</label>
                                    <input type="text" class="form-control" name="prenom" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Mot de passe</label>
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Rôle</label>
                                    <select class="form-select" name="role" required>
                                        <option value="admin">Admin</option>
                                        <option value="recruteur">Recruteur</option>
                                        <option value="candidat">Candidat</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Type de contrat</label>
                                    <select class="form-select" name="type_contrat" required>
                                        <option value="CDI">CDI</option>
                                        <option value="CDD">CDD</option>
                                        <option value="Stage">Stage</option>
                                        <option value="Alternance">Alternance</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Disponibilité</label>
                                    <input type="text" class="form-control" name="disponibilite" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Compétences</label>
                                    <input type="text" class="form-control" name="competences" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Téléphone</label>
                                    <input type="text" class="form-control" name="telephone" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Localisation</label>
                                    <input type="text" class="form-control" name="localisation" required>
                                </div>
                                
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-dark btn-lg">Ajouter l'utilisateur</button>
                                    <a href="liste-user.php" class="btn btn-outline-secondary ms-2">Annuler</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-light py-5">
        <div class="container text-center">
            <p class="mb-0">Plateforme de recrutement intelligente | © 2023 Tous droits réservés</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>