<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (isset($_POST['action']) && $_POST['action'] === 'sql_login') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($email === 'admin@gmail.com' && $password === 'admin') {
        $_SESSION['user'] = [
            'id_utilisateur' => 0,
            'nom' => 'Admin',
            'role' => 'admin'
        ];
        $_SESSION['email'] = $email;
        echo json_encode(['success' => true, 'redirect' => 'dashboard-admin.php']);
        exit();
    }

    $conn = mysqli_connect("localhost", "root", "root", "hackathon");
    $stmt = mysqli_prepare($conn, "SELECT * FROM utilisateurs WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result)) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['mot_de_passe'])) {
            $_SESSION['user'] = [
                'id_utilisateur' => $user['id_utilisateur'],
                'nom' => $user['nom'],
                'role' => $user['role'] ?? 'candidat' // Assurez-vous que le rôle est défini
            ];
            $_SESSION['email'] = $user['email'];
            echo json_encode(['success' => true, 'redirect' => 'accueil.php']);
            exit();
        }
    }
    echo json_encode(['success' => false, 'error' => 'Email ou mot de passe incorrect.']);
    exit();
}



?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion - Plateforme de Recrutement Intelligent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="index-CSS/styles-index.css">
    <script type="module" src="js/connexion.js" defer></script>
    <link rel="stylesheet" href="connexion.css">
    
</head>

<body>
    <main class="container-fluid px-0">
        <section class="custom-hero">
            <div class="container text-center py-5">
                <h1 class="display-4 mb-4">Connexion à votre compte</h1>
                <p class="lead mb-5">À vous de construire votre avenir, tout commence ici.</p>
                <a href="index.php" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-arrow-left me-2"></i> Retour à l'accueil
                </a>
            </div>
        </section>

        <section class="py-5 form-section">
            <div class="container">
                <h2 class="text-center display-5 mb-5">Connexion Utilisateur</h2>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" class="row g-4" id="loginForm">
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent"><i class="fas fa-envelope"></i></span>
                            <input type="email" name="email" class="form-control" placeholder="votre@email.com" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">Mot de passe</label>
                        <div class="password-toggle">
                            <div class="input-group">
                                <span class="input-group-text bg-transparent"><i class="fas fa-lock"></i></span>
                                <input type="password" id="password" name="password" class="form-control" placeholder="Votre mot de passe" required>
                            </div>
                            <i class="fas fa-eye password-toggle-icon" id="toggleIcon" onclick="togglePassword()"></i>
                        </div>
                    </div>

                    <div class="col-12 text-center">
                        <div class="d-grid gap-3">
                            <button type="submit" name="bout" id="bout" class="btn btn-dark btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i> Se connecter
                            </button>
                            
                            <a href="index.php" class="btn btn-outline-dark btn-lg">
                                <i class="fas fa-user-plus me-2"></i> Créer un compte
                            </a>
                            
                            <button class="btn btn-light btn-lg google-login-btn" id="google-login-btn">
                                <img src="https://img.icons8.com/color/48/000000/google-logo.png" alt="Google Logo" width="24" height="24" class="me-2">
                                Continuer avec Google
                            </button>
                            
                            <div class="mt-3">
                                <a href="#" id="forgot-password-link" class="text-muted text-decoration-none forgot-password-link">
                                    <i class="fas fa-key me-1"></i> Mot de passe oublié ?
                                </a>
                            </div>
                        </div>

                        <div id="forgot-password-form" class="mt-4 p-4 border rounded shadow-sm bg-light" style="display:none;">
                            <div class="mb-3">
                                <input type="email" id="reset-email" class="form-control form-control-lg" placeholder="Votre email" required>
                            </div>
                            <button id="send-reset-email" class="btn btn-secondary btn-lg w-100">
                                <i class="fas fa-paper-plane me-2"></i> Envoyer le lien de réinitialisation
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </section>

        <footer class="bg-light py-4 mt-5">
            <div class="container text-center">
                <p class="mb-0">© <span id="current-year">2023</span> Plateforme de Recrutement Intelligent. Tous droits réservés.</p>
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
                toggleIcon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
        
        // Gestion du mot de passe oublié
        document.getElementById('forgot-password-link').addEventListener('click', function(e) {
            e.preventDefault();
            const form = document.getElementById('forgot-password-form');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        });
        
        // Mise à jour automatique de l'année du copyright
        document.getElementById('current-year').textContent = new Date().getFullYear();
        
        // Animation des champs de formulaire
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('shadow-sm');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('shadow-sm');
            });
        });
    </script>
</body>
</html>