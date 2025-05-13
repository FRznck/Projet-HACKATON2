<?php
session_start();

// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connexion à la base de données
$conn = mysqli_connect("localhost", "root", "root", "hackathon");
if (!$conn) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

// Récupérer toutes les offres
$sql_offres = "
    SELECT o.*, e.nom_entreprise, o.competences_requises
    FROM offres o
    LEFT JOIN entreprise e ON o.id_entreprise = e.id
    ORDER BY o.date_publication DESC
";

$resultat_offres = mysqli_query($conn, $sql_offres);

if (isset($_POST['submit'])) {
    $nom = mysqli_real_escape_string($conn, $_POST['nom']);
    $prenom = mysqli_real_escape_string($conn, $_POST['prenom']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mot_de_passe = $_POST['password'];
    $profil_linkedin = mysqli_real_escape_string($conn, $_POST['profil_linkedin']);
    $disponibilite = mysqli_real_escape_string($conn, $_POST['disponibilite']);
    $type_contrat = mysqli_real_escape_string($conn, $_POST['type_contrat']);
    $competences = mysqli_real_escape_string($conn, $_POST['competence']);
    $telephone = $_POST['telephone'];
    $localisation = $_POST['localisation'];



    // Vérifier si l'email existe déjà
    $verif = mysqli_query($conn, "SELECT * FROM utilisateurs WHERE email='$email'");
    if (mysqli_num_rows($verif) > 0) {
        echo "<h3>Le mail est déjà utilisé...</h3>";
        exit();
    }

    // Générer un nouvel ID utilisateur en évitant les NULL
    $res_max_id = mysqli_query($conn, "SELECT MAX(id_utilisateur) AS maxi FROM utilisateurs");
    $ligne = mysqli_fetch_assoc($res_max_id);
    $newId = ($ligne['maxi'] !== null) ? $ligne['maxi'] + 1 : 1;

    // Créer les dossiers si besoin
    if (!is_dir('uploads/photos')) mkdir('uploads/photos', 0777, true);
    if (!is_dir('uploads/cv')) mkdir('uploads/cv', 0777, true);

    // Gestion avatar (photo de profil)
    $avatar = "";
    if (isset($_FILES['photo_profil']) && $_FILES['photo_profil']['error'] === 0) {
        $ext = pathinfo($_FILES['photo_profil']['name'], PATHINFO_EXTENSION);
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($ext), $allowed_ext)) {
            $avatar_filename = $newId . '.' . strtolower($ext);
            move_uploaded_file($_FILES['photo_profil']['tmp_name'], "uploads/photos/$avatar_filename");
            $avatar = $avatar_filename;
        } else {
            echo "<h3>Extension de photo non autorisée !</h3>";
            exit();
        }
    }

    // Gestion du CV
    $cv_url = "";
    if (isset($_FILES['cv']) && $_FILES['cv']['error'] === 0) {
        $ext_cv = pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION);
        $allowed_cv_ext = ['pdf', 'doc', 'docx'];
        if (in_array(strtolower($ext_cv), $allowed_cv_ext)) {
            $cv_filename = $newId . '.' . strtolower($ext_cv);
            move_uploaded_file($_FILES['cv']['tmp_name'], "uploads/cv/$cv_filename");
            $cv_url = $cv_filename;
        } else {
            echo "<h3>Extension de CV non autorisée !</h3>";
            exit();
        }
    }

    // Hash du mot de passe sécurisé
    $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    // Insertion des données dans la base
    $sql_insert = "
        INSERT INTO utilisateurs (
            id_utilisateur,
            nom,
            prenom,
            email,
            mot_de_passe,
            profil_linkedin,
            competences,
            cv_url,
            avatar,
            disponibilite,
            type_contrat,
            telephone,
            localisation
        ) VALUES (
            '$newId',
            '$nom',
            '$prenom',
            '$email',
            '$mot_de_passe_hash',
            '$profil_linkedin',
            '$competences',
            '$cv_url',
            '$avatar',
            '$disponibilite',
            '$type_contrat',
            '$telephone',
            '$localisation'
        )
    ";

    if (mysqli_query($conn, $sql_insert)) {
        $_SESSION['message'] = "<h3>Inscription réussie !</h3>";
        header("Location: connexion.php");
        exit();
    } else {
        echo "<h3>Erreur : " . mysqli_error($conn) . "</h3>";
        // Vous pouvez aussi logguer cette erreur pour debug plus avancé.
        exit();
    }
}

// Fermer la connexion à la fin du script principal
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Plateforme de Recrutement Intelligent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index-CSS/styles-index.css">
    <link rel="stylesheet" href="styles-custom.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC3l_Df3HNP8LWJRcfymEO_mSez5NS1QeM&libraries=places"></script>
    <script src="js/maps.js"></script>
    <script src="js/pwd-generator.js"></script>
    <script type="module" src="js/connexion.js" defer></script>
</head>

<body>
    <main class="container-fluid px-0">
        <!-- Hero Section -->
        <section class="custom-hero text-white">
            <div class="container text-center py-5">
                <h1 class="display-4 mb-4 fw-bold">Bienvenue sur notre Plateforme<br>de Recrutement Intelligent</h1>
                <p class="lead mb-5">Trouvez l'emploi de vos rêves ou le candidat idéal en toute simplicité.</p>
                <div class="d-flex justify-content-center gap-3">
                    <?php if (!isset($_SESSION['user'])): ?>
                        <a href="#inscription-section" class="btn btn-dark btn-lg" onclick="document.querySelector('.form-section').scrollIntoView({behavior: 'smooth'})">S'inscrire</a>
                        <a href="connexion.php" class="btn btn-outline-dark btn-lg">Se connecter</a>
                    <?php else: ?>
                        <a href="dashboard-<?php echo $_SESSION['user']['role']; ?>.php" class="btn btn-dark btn-lg">Mon Tableau de Bord</a>
                        <a href="deconnexion.php" class="btn btn-outline-dark btn-lg">Se Déconnecter</a>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Toutes les Offres -->
        <section class="py-5">
            <div class="container">
                <h2 class="text-center display-5 mb-5">Toutes les Offres</h2>
                <div class="row g-4">
                    <?php if (mysqli_num_rows($resultat_offres) > 0): ?>
                        <?php while ($offre = mysqli_fetch_assoc($resultat_offres)): ?>
                            <div class="col-md-4 mb-4">
                                <article class="job-card position-relative h-100 p-4 shadow-sm rounded">
                                    <?php if (strtotime($offre['date_publication']) > strtotime('-7 days')): ?>
                                        <span class="badge-premium position-absolute top-0 end-0 m-3 badge rounded-pill bg-danger">🔥 Hot</span>
                                    <?php endif; ?>

                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-wrapper me-3">💼</div>
                                        <h3 class="h5 mb-0"><?= htmlspecialchars($offre['titre']); ?></h3>
                                    </div>

                                    <div class="description-wrapper mb-3">
                                        <p class="text-muted small"><?= htmlspecialchars(substr($offre['description'], 0, 150)) . '...'; ?></p>
                                    </div>

                                    <div class="info-grid mb-3">
                                        <div class="info-item">
                                            <span class="text-primary">📄 Type:</span>
                                            <span><?= htmlspecialchars($offre['type_contrat']); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <span class="text-primary">📍 Lieu:</span>
                                            <span><?= htmlspecialchars($offre['lieu']); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <span class="text-primary">💰 Salaire:</span>
                                            <span><?= htmlspecialchars($offre['salaire'] ? $offre['salaire'] . ' €' : 'Non précisé'); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <span class="text-primary">🏢 Entreprise:</span>
                                            <span><?= htmlspecialchars($offre['nom_entreprise'] ?? 'Non précisée'); ?></span>
                                        </div>
                                    </div>

                                    <div class="skills-wrapper mb-3">
                                        <p class="mb-2"><strong>Compétences requises:</strong></p>
                                        <?php
                                        $competences = explode(',', $offre['competences_requises'] ?? '');
                                        foreach ($competences as $competence): ?>
                                            <span class="badge bg-light text-dark me-1 mb-1"><?= htmlspecialchars(trim($competence)); ?></span>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="mt-auto">
                                        <p class="small text-muted mb-2">
                                            <i class="bi bi-calendar"></i>
                                            Publié le <?= htmlspecialchars(date('d/m/Y', strtotime($offre['date_publication']))); ?>
                                        </p>
                                        <a href="postuler.php?id_offre=<?= $offre['id_offre']; ?>"
                                            class="btn btn-primary w-100">Postuler maintenant</a>
                                    </div>
                                </article>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center">Aucune offre disponible pour le moment.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


<!-- Connexion/Inscription -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center display-5 mb-5"><i class="fas fa-sign-in-alt me-2"></i>Connexion ou Inscription</h2>
        <div class="row g-4 justify-content-center">
            <div class="col-md-6">
                <a href="connexion.php" class="text-decoration-none">
                    <div class="card h-100 text-center p-4">
                        <div class="icon-wrapper mx-auto mb-3"><i class="fas fa-user-graduate"></i></div>
                        <h3>Candidat</h3>
                        <p class="text-muted">Trouvez l'emploi de vos rêves</p>
                    </div>
                </a>
            </div>
            <div class="col-md-6">
                <a href="inscription-recruteur.php" class="text-decoration-none">
                    <div class="card h-100 text-center p-4">
                        <div class="icon-wrapper mx-auto mb-3"><i class="fas fa-briefcase"></i></div>
                        <h3>Recruteur</h3>
                        <p class="text-muted">Trouvez les meilleurs talents</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Formulaire Inscription -->
<section class="py-5 form-section bg-light">
    <div class="container">
        <h2 class="text-center display-5 mb-5 fw-bold"><i class="fas fa-user-plus me-2"></i>Inscription Candidat</h2>
        <form class="row g-4 needs-validation" method="POST" enctype="multipart/form-data" novalidate>
            <div class="col-md-6">
                <label class="form-label"><i class="fas fa-user me-2"></i>Nom</label>
                <input type="text" class="form-control" name="nom" placeholder="Entrez votre nom" required>
            </div>

            <div class="col-md-6">
                <label class="form-label"><i class="fas fa-user me-2"></i>Prénom</label>
                <input type="text" class="form-control" name="prenom" placeholder="Entrez votre prénom" required>
            </div>

            <div class="col-md-6">
                <label class="form-label"><i class="fas fa-envelope me-2"></i>Email</label>
                <input type="email" class="form-control" name="email" placeholder="Entrez votre email" required>
            </div>

            <div class="col-12">
                <label class="form-label"><i class="fas fa-lock me-2"></i>Mot de passe</label>
                <div class="input-group">
                    <input type="password" class="form-control" name="password" id="passwordField" placeholder="Entrez votre mot de passe" required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="col-12">
                <label for="description" class="form-label"><i class="fas fa-tools me-2"></i>Compétences</label>
                <textarea id="competence" name="competence" class="form-control" rows="4" placeholder="Entrez vos compétences" required style="resize: none;"></textarea>
            </div>

            <div class="col-12">
                <label class="form-label"><i class="fab fa-linkedin me-2"></i>Profil LinkedIn</label>
                <input type="url" class="form-control" name="profil_linkedin" placeholder="Entrez l'URL de votre profil LinkedIn" required>
            </div>

            <div class="col-12">
                <label class="form-label"><i class="fas fa-file-pdf me-2"></i>CV</label>
                <input type="file" class="form-control" name="cv" accept=".pdf, .doc, .docx" required>
            </div>

            <div class="col-12">
                <label class="form-label"><i class="fas fa-camera me-2"></i>Photo de profil</label>
                <input type="file" class="form-control" name="photo_profil" accept="image/*" required>
            </div>

            <div class="col-md-6">
                <label class="form-label"><i class="fas fa-calendar-check me-2"></i>Disponibilité</label>
                <select class="form-select" name="disponibilite" required>
                    <option value="">Choisir...</option>
                    <option value="immédiate">Immédiate</option>
                    <option value="1 mois">Dans 1 mois</option>
                    <option value="2 mois">Dans 2 mois</option>
                    <option value="3 mois ou plus">Dans 3 mois ou plus</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label"><i class="fas fa-file-signature me-2"></i>Type de contrat recherché</label>
                <select class="form-select" name="type_contrat" required>
                    <option value="">Choisir...</option>
                    <option value="CDI">CDI</option>
                    <option value="CDD">CDD</option>
                    <option value="Alternance">Alternance</option>
                    <option value="Stage">Stage</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-medium"><i class="fas fa-phone me-2"></i>Téléphone</label>
                <input type="tel" class="form-control" name="telephone" placeholder="Entrez votre numéro de téléphone" pattern="[0-9]{10}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium"><i class="fas fa-map-marker-alt me-2"></i>Localisation</label>
                <input type="text" class="form-control" name="localisation" placeholder="Entrez votre localisation" required>
            </div>

            <div class="col-12 d-flex justify-content-center gap-3">
                <button type="submit" name="submit" class="btn btn-primary px-5"><i class="fas fa-user-plus me-2"></i>Créer un compte</button>
                
                <a href="connexion.php" class="btn btn-outline-dark" style="text-decoration: none;">
                    <i class="fas fa-sign-in-alt me-2"></i>Se connecter ?
                </a>
                <button class="btn btn-outline-dark rounded-pill px-4" id="google-login-btn">
                    <img src="https://img.icons8.com/color/48/000000/google-logo.png"
                        alt="Google Logo"
                        class="me-3"
                        width="28"
                        height="28">
                    <span class="fw-medium">Continuer avec Google</span>
                </button>
            </div>
        </form>
    </div>
</section>

<!-- Section Premium -->
<section class="py-5 bg-primary bg-opacity-10">
    <div class="container text-center">
        <h2 class="display-5 mb-4 fw-bold"><i class="fas fa-crown me-2"></i>Fonctionnalités <span class="text-primary">Premium</span></h2>
        <p class="lead mb-5">Passez à la version premium pour des avantages exclusifs</p>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="premium-card border rounded shadow-lg overflow-hidden">
                    <div class="p-4 bg-light text-dark">
                        <h3 class="mb-3"><i class="fas fa-gem me-2"></i>Badge Premium</h3>
                        <div class="badge bg-primary mb-3 fs-6"><i class="fas fa-tag me-2"></i>À partir de 9,99 $/mois</div>
                        <h4 class="mb-2"><i class="fas fa-star me-2"></i>Abonnement Premium</h4>
                        <p class="text-muted"><i class="fas fa-check-circle me-2"></i>Débloquez toutes les fonctionnalités : visibilité accrue, messagerie illimitée, gestion avancée des candidatures, et plus encore.</p>
                        <div class="fs-1 mb-3"><i class="fas fa-diamond text-primary"></i></div>

                        <a href="page-paiement.php" class="btn btn-success btn-lg w-100">
                            <i class="fas fa-rocket me-2"></i>Souscrire maintenant
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // Script pour afficher/masquer le mot de passe
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordField = document.getElementById('passwordField');
        const icon = this.querySelector('i');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
</script>

        <!-- Footer -->
        <footer class="bg-light py-4">
            <div class="container text-center">
                <p class="mb-0">© 2022 Plateforme de Recrutement Intelligent. Tous droits réservés.</p>
            </div>
        </footer>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
