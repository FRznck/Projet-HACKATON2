<?php
session_start();

// Connexion à la base de données
$id = mysqli_connect("localhost", "root", "root", "hackathon");
if (!$id) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

// Traitement du formulaire de connexion
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $email = $id->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Vérifier si l'utilisateur existe dans la base de données
    $sql = "SELECT * FROM entreprise WHERE email_professionnel = ?";
    $stmt = $id->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Vérifier le mot de passe
        if (password_verify($password, $user['mot_de_passe'])) {
            
            $_SESSION['id'] = $user['id'];
            $_SESSION['nom_entreprise'] = $user['nom_entreprise'];
            $_SESSION['email_professionnel'] = $user['email_professionnel'];
            $_SESSION['role'] = $user['role'];

            echo "<h3>Connexion réussie, vous allez être redirigé....";
            
            header("Location: dashboard-recruteur.php");
            exit();
        } else {
            $error = "Mot de passe incorrect.";
        }
    } else {
        $error = "Aucun compte trouvé avec cet email.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion - Plateforme de Recrutement Intelligent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="connexion-recruteur.css">
</head>
<body>
    <main class="container-fluid px-0">
        <!-- Hero Section -->
        <section class="custom-hero animate__animated animate__fadeIn">
            <div class="container text-center py-5">
                <h1 class="display-4 mb-4 fw-bold">Espace Recruteur</h1>
                <p class="lead mb-5 fs-4">Le terrain est prêt, <strong class="text-warning">à vous de jouer.</strong></p>
                <a href="index.php" class="btn btn-outline-light btn-lg px-4 py-2 animate__animated animate__pulse animate__infinite">
                    <i class="fas fa-arrow-left me-2"></i>Retour à l'accueil
                </a>
            </div>
        </section>

        <!-- Formulaire Connexion -->
        <section class="py-5 form-section animate__animated animate__fadeInUp">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="display-5 fw-bold mb-3">Vos talents vous attendent !</h2>
                    <p class="text-muted">Connectez-vous pour accéder à votre espace recruteur</p>
                </div>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger text-center animate__animated animate__shakeX"><?= htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <form class="row g-4" method="POST" action="">
                    <div class="col-12">
                        <label class="form-label fw-bold">Email professionnel</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-envelope text-primary"></i></span>
                            <input type="email" class="form-control" name="email" placeholder="Entrez votre email professionnel" required>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label fw-bold">Mot de passe</label>
                        <div class="password-toggle">
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-lock text-primary"></i></span>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Entrez votre mot de passe" required>
                                <span class="password-toggle-icon" onclick="togglePassword()">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </span>
                            </div>
                        </div>
                        <div class="text-end mt-2">
                            <a href="forgot-password.php" class="text-decoration-none">Mot de passe oublié ?</a>
                        </div>
                    </div>
                    
                    <div class="col-12 d-flex gap-3 justify-content-center mt-4">
                        <button type="submit" class="btn btn-dark px-4 py-2">
                            <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                        </button>
                        <a href="inscription-recruteur.php" class="btn btn-outline-dark px-4 py-2">
                            <i class="fas fa-user-plus me-2"></i>Créer un compte
                        </a>
                    </div>
                </form>
                
                <div class="text-center mt-5 pt-4 border-top">
                    <p class="text-muted">Vous êtes candidat ? <a href="connexion-candidat.php" class="text-primary text-decoration-none">Connectez-vous ici</a></p>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-dark py-4 mt-5">
            <div class="container text-center">
                <div class="mb-3">
                    <a href="#" class="text-white mx-2"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white mx-2"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-white mx-2"><i class="fab fa-linkedin-in"></i></a>
                </div>
                <p class="mb-0 text-light">© 2023 Plateforme de Recrutement Intelligent. Tous droits réservés.</p>
            </div>
        </footer>
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fonction pour basculer la visibilité du mot de passe
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        // Animation pour les champs du formulaire
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('animate__animated', 'animate__pulse');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('animate__animated', 'animate__pulse');
            });
        });
    </script>
</body>
</html>