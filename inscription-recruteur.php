<?php
// Connexion à la base de données
session_start();

// Connexion à la base de données
$id = mysqli_connect("localhost", "root", "root", "hackathon");
if (!$id) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

// Traitement du formulaire d'inscription
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $nom_entreprise = $id->real_escape_string($_POST['companyName']);
    $contact_rh = $id->real_escape_string($_POST['contactName']);
    $email_professionnel = $id->real_escape_string($_POST['professionalEmail']);
    $telephone = $id->real_escape_string($_POST['phoneNumber']);
    $mot_de_passe = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hachage du mot de passe
    $secteur_activite = $id->real_escape_string($_POST['industry']);
    $taille_entreprise = $id->real_escape_string($_POST['companySize']);
    $adresse_siege = $id->real_escape_string($_POST['headquartersAddress']);
    $disponibilite = 

    // Préparer la requête SQL
    $sql = "INSERT INTO entreprise (nom_entreprise, contact_rh, email_professionnel, telephone, mot_de_passe, secteur_activite, taille_entreprise, adresse_siege, date_creation)
            VALUES ('$nom_entreprise', '$contact_rh', '$email_professionnel', '$telephone', '$mot_de_passe', '$secteur_activite', '$taille_entreprise', '$adresse_siege', now())";

    // Exécuter la requête
    if ($id->query($sql) === TRUE) {
        echo "<script>alert('Compte créé avec succès !');</script>";
        header("Location: connexion-recruteur.php"); // Rediriger vers la page de connexion
        exit();
    } else {
        echo "Erreur : " . $sql . "<br>" . $id->error;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Espace recruteur - Plateforme de Recrutement Intelligent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="inscription-recruteur.css">
</head>

<body>
    <main class="container-fluid px-0">
        <!-- Hero Section -->
        <section class="custom-hero">
            <div class="container text-center py-5">
                <h1 class="display-4 mb-4 fw-bold">Espace Recruteur</h1>
                <p class="lead mb-5 fs-4">On s'efface quand vous entrez en scène, <strong>à vous de jouer.</strong></p>
                <a href="index.php" class="btn btn-outline-light btn-lg px-4 py-2">
                    <i class="fas fa-arrow-left me-2"></i>Retour à l'accueil
                </a>
            </div>
        </section>

        <!-- Formulaire Création de Compte Entreprise -->
        <section class="py-5 form-section">
            <div class="container">
                <h2 class="text-center display-5 mb-5 form-title">Création de Compte Entreprise</h2>
                <form class="row g-4" method="POST" action="">
                    <div class="col-md-6">
                        <label class="form-label">Nom de l'entreprise</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-building"></i></span>
                            <input type="text" class="form-control" name="companyName" placeholder="Entrez le nom de l'entreprise" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nom/prénom du contact RH</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                            <input type="text" class="form-control" name="contactName" placeholder="Entrez le nom et prénom du contact RH" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email professionnel</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" name="professionalEmail" placeholder="Entrez l'email professionnel" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Numéro de téléphone</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="tel" class="form-control" name="phoneNumber" placeholder="Entrez le numéro de téléphone" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" name="password" placeholder="Entrez un mot de passe" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Secteur d'activité</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-industry"></i></span>
                            <input type="text" class="form-control" name="industry" placeholder="Entrez le secteur d'activité" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Taille de l'entreprise</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-users"></i></span>
                            <select class="form-select" name="companySize" required>
                                <option value="" selected disabled>Choisissez...</option>
                                <option value="1-10 employés">1-10 employés</option>
                                <option value="11-50 employés">11-50 employés</option>
                                <option value="51-200 employés">51-200 employés</option>
                                <option value="201-500 employés">201-500 employés</option>
                                <option value="500+ employés">500+ employés</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Adresse du siège</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                            <input type="text" class="form-control" name="headquartersAddress" placeholder="Entrez l'adresse du siège" required>
                        </div>
                    </div>
                    <div class="col-12 d-flex gap-3 justify-content-center mt-4">
                        <button type="submit" class="btn btn-dark px-4 py-2">
                            <i class="fas fa-user-plus me-2"></i>Créer un compte
                        </button>
                        <a href="connexion-recruteur.php" class="btn btn-outline-dark px-4 py-2">
                            <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                        </a>
                    </div>
                </form>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-4">
            <div class="container text-center">
                <p class="mb-0">© 2023 Plateforme de Recrutement Intelligent. Tous droits réservés.</p>
                <div class="mt-3">
                    <a href="#" class="text-white mx-2"><i class="fab fa-linkedin fa-lg"></i></a>
                    <a href="#" class="text-white mx-2"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="#" class="text-white mx-2"><i class="fab fa-facebook fa-lg"></i></a>
                </div>
            </div>
        </footer>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>