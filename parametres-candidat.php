<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = mysqli_connect("localhost", "root", "root", "hackathon");

if (!$conn) {
    die("Erreur de connexion à la base de données: " . mysqli_connect_error());
}

if (!isset($_SESSION['email'])) {
    header("Location: connexion.php");
    exit();
}

$email = $_SESSION['email'];
$error = '';
$success = '';


if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $query = "SELECT avatar FROM utilisateurs WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $photoProfil = !empty($row['avatar']) ? $row['avatar'] : 'default.png';
    } else {
        $photoProfil = 'default.png';
    }
} else {
    $photoProfil = 'default.png';
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['save_profil'])) {
        // Nettoyage
        $nom = mysqli_real_escape_string($conn, $_POST['nom'] ?? '');
        $prenom = mysqli_real_escape_string($conn, $_POST['prenom'] ?? '');
        $email_user = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
        $tel = mysqli_real_escape_string($conn, $_POST['telephone'] ?? '');
        $localisation = mysqli_real_escape_string($conn, $_POST['localisation'] ?? '');
        $competences = mysqli_real_escape_string($conn, $_POST['competences'] ?? '');
        $linkedin = mysqli_real_escape_string($conn, $_POST['linkedin'] ?? '');
        $type_contrat = isset($_POST['type_contrat']) ? implode(',', $_POST['type_contrat']) : '';
        $disponibilite = isset($_POST['disponibilite']) ? implode(',', $_POST['disponibilite']) : '';
        $salaire = mysqli_real_escape_string($conn, $_POST['salaire'] ?? '');

        // Vérification de base
        if (empty($nom) || empty($email_user)) {
            $error = "Le nom et l'email sont obligatoires.";
        } else {
            // Gestion CV
            $cv_name = $user['cv_url'] ?? null;
            if (isset($_FILES['cv']['error'])) {
                if ($_FILES['cv']['error'] === 0) {
                    $allowed_types = [
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                    ];
                    if (in_array($_FILES['cv']['type'], $allowed_types)) {
                        if (!is_dir('uploads/')) {
                            mkdir('uploads/', 0777, true);
                        }
                        $cv_name = uniqid() . '_' . basename($_FILES['cv']['name']);
                        $cv_path = 'uploads/' . $cv_name;
                        if (!move_uploaded_file($_FILES['cv']['tmp_name'], $cv_path)) {
                            $error = "Erreur lors de l'upload du CV.";
                        }
                    } else {
                        $error = "Type de fichier CV non autorisé (PDF, DOC ou DOCX seulement).";
                    }
                }
            }

            // Gestion Avatar
            $avatar_name = $user['avatar'] ?? null;
            if (isset($_FILES['avatar']['error'])) {
                if ($_FILES['avatar']['error'] === 0) {
                    $allowed_avatar_types = ['image/jpeg', 'image/png', 'image/gif'];
                    if (in_array($_FILES['avatar']['type'], $allowed_avatar_types)) {
                        if (!is_dir('avatars/')) {
                            mkdir('avatars/', 0777, true);
                        }
                        $avatar_name = uniqid() . '_' . basename($_FILES['avatar']['name']);
                        $avatar_path = 'avatars/' . $avatar_name;
                        if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $avatar_path)) {
                            $error = "Erreur lors de l'upload de l'avatar.";
                        }
                    } else {
                        $error = "Type de fichier avatar non autorisé (JPEG, PNG ou GIF seulement).";
                    }
                } else {
                    // Si aucun nouveau fichier n'est uploadé, on garde l'ancien avatar S'IL EXISTE et n'est pas default.png
                    if (!empty($user['avatar']) && $user['avatar'] !== 'default.png') {
                        $avatar_name = $user['avatar'];
                    } else {
                        $avatar_name = null; // On ne met rien, la colonne ne sera pas modifiée
                    }
                }
            } else {
                // Si le champ avatar n'est pas présent dans $_FILES, même logique
                if (!empty($user['avatar']) && $user['avatar'] !== 'default.png') {
                    $avatar_name = $user['avatar'];
                } else {
                    $avatar_name = null;
                }
            }

            // Mise à jour en base si pas d'erreur
            if (empty($error)) {
                if ($avatar_name !== null) {
                    $update = "UPDATE utilisateurs SET 
                        nom = ?, prenom = ?, email = ?, telephone = ?, localisation = ?, 
                        competences = ?, profil_linkedin = ?, type_contrat = ?, disponibilite = ?, 
                        cv_url = ?, avatar = ? 
                        WHERE email = ?";
                    $stmt = $conn->prepare($update);
                    $stmt->bind_param(
                        "ssssssssssss",
                        $nom,
                        $prenom,
                        $email_user,
                        $tel,
                        $localisation,
                        $competences,
                        $linkedin,
                        $type_contrat,
                        $disponibilite,
                        $cv_name,
                        $avatar_name,
                        $email
                    );
                } else {
                    $update = "UPDATE utilisateurs SET 
                        nom = ?, prenom = ?, email = ?, telephone = ?, localisation = ?, 
                        competences = ?, profil_linkedin = ?, type_contrat = ?, disponibilite = ?, 
                        cv_url = ?
                        WHERE email = ?";
                    $stmt = $conn->prepare($update);
                    $stmt->bind_param(
                        "sssssssssss",
                        $nom,
                        $prenom,
                        $email_user,
                        $tel,
                        $localisation,
                        $competences,
                        $linkedin,
                        $type_contrat,
                        $disponibilite,
                        $cv_name,
                        $email
                    );
                }
                if ($stmt->execute()) {
                    $success = "Profil mis à jour avec succès.";
                    $_SESSION['email'] = $email_user;
                    $email = $email_user;
                } else {
                    $error = "Erreur lors de la mise à jour du profil.";
                }
                $stmt->close();
            }
        }
    }

    // Mise à jour mot de passe
    if (isset($_POST['update_password'])) {
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $enable_2fa = isset($_POST['enable_2fa']) ? 1 : 0;

        if (!empty($new_password) && $new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE utilisateurs SET mot_de_passe = ?, double_facteur = ? WHERE email = ?");
            $stmt->bind_param("sis", $hashed_password, $enable_2fa, $email);
            if ($stmt->execute()) {
                $success = "Mot de passe mis à jour avec succès.";
            } else {
                $error = "Erreur lors de la mise à jour du mot de passe.";
            }
            $stmt->close();
        } else {
            $error = "Les mots de passe ne correspondent pas.";
        }
    }

    // Désactivation compte
    if (isset($_POST['deactivate_account'])) {
        $stmt = $conn->prepare("UPDATE utilisateurs SET actif = 0 WHERE email = ?");
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            session_destroy();
            header("Location: index.php?msg=compte_desactive");
            exit();
        } else {
            $error = "Erreur lors de la désactivation du compte.";
        }
        $stmt->close();
    }

    // Suppression compte
    if (isset($_POST['delete_account'])) {
        $stmt = $conn->prepare("DELETE FROM utilisateurs WHERE email = ?");
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            session_destroy();
            header("Location: index.php?msg=compte_supprime");
            exit();
        } else {
            $error = "Erreur lors de la suppression du compte.";
        }
        $stmt->close();
    }
}

// Récupérer infos utilisateur
$query = "SELECT * FROM utilisateurs WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $type_contrat = isset($user['type_contrat']) ? explode(',', $user['type_contrat']) : [];
    $disponibilite = isset($user['disponibilite']) ? explode(',', $user['disponibilite']) : [];
} else {
    $user = [];
}
$stmt->close();
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Paramètres Candidat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="parametres-candidat-CSS/parametres-styles.css">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC3l_Df3HNP8LWJRcfymEO_mSez5NS1QeM&libraries=places"></script>
    <script src="js/maps.js"></script>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg bg-white shadow-sm fixed-top">
        <div class="container">
            <div class="d-flex align-items-center">
                <div class="bg-secondary bg-opacity-10 rounded-circle me-3" style="width: 40px; height: 40px; overflow: hidden;">
                    <img src="avatars/<?php echo htmlspecialchars($photoProfil); ?>" alt="Photo de profil" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <a class="navbar-brand fw-medium fs-4" href="parametres-candidat.php">Plateforme de Recrutement Intelligente</a>
            </div>
            <div class="d-flex align-items-center gap-4">
                <a href="accueil.php" class="text-dark text-decoration-none">Accueil</a>
                <span class="text-dark">Paramètres du compte</span>
            </div>
        </div>
    </nav>

    <main class="container pt-5 mt-5">
        <!-- Affichage des messages -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <!-- En-tête -->
        <section class="text-center mb-5">
            <h1 class="display-5 fw-bold mb-4">Paramètres du compte</h1>
            <hr class="mx-auto mb-5" style="width: 95%;">
        </section>

        <form method="POST" enctype="multipart/form-data">
    <!-- Section Profil -->
    <section class="settings-section">
        <div class="row align-items-center mb-5">
            <div class="col-md-3 text-center mb-4 mb-md-0">
                <div class="avatar-upload">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="avatars/<?= htmlspecialchars($user['avatar']) ?>" class="rounded-circle mb-2" alt="Photo de profil" style="width: 120px; height: 120px; object-fit: cover;">
                    <?php else: ?>
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-2" style="width: 120px; height: 120px;">
                            <span class="fs-1 text-muted">👤</span>
                        </div>
                    <?php endif; ?>
                    <input type="file" class="form-control mt-2" accept="image/*" name="avatar">
                    <small class="text-muted">Format: JPG, PNG, GIF (max 2 Mo)</small>
                </div>
            </div>

            <div class="col-md-9">
                <h2 class="form-section-title">Informations Personnelles</h2>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nom" value="<?= htmlspecialchars($user['nom'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Prénom</label>
                        <input type="text" class="form-control" name="prenom" value="<?= htmlspecialchars($user['prenom'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Adresse e-mail <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Téléphone</label>
                        <input type="tel" class="form-control" name="telephone" value="<?= htmlspecialchars($user['telephone'] ?? '') ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Localisation</label>
                        <input type="text" class="form-control" name="localisation" value="<?= htmlspecialchars($user['localisation'] ?? '') ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Profil LinkedIn</label>
                        <input type="url" class="form-control" name="linkedin" value="<?= htmlspecialchars($user['profil_linkedin'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Professionnelle -->
    <section class="settings-section">
        <h2 class="form-section-title">Informations Professionnelles</h2>
        <div class="row g-4">
            <div class="col-md-12">
                <label class="form-label">Compétences clés</label>
                <textarea class="form-control" name="competences" rows="3"><?= htmlspecialchars($user['competences'] ?? '') ?></textarea>
                <small class="text-muted">Séparez vos compétences par des virgules.</small>
            </div>

            <div class="col-md-12">
                <label class="form-label">CV (PDF, DOC, DOCX)</label>
                <input type="file" class="form-control" name="cv" accept=".pdf,.doc,.docx">
                <?php if (!empty($user['cv_url'])): ?>
                    <div class="mt-2">
                        <small>CV actuel :</small>
                        <a href="uploads/<?= htmlspecialchars($user['cv_url']) ?>" target="_blank" class="text-decoration-none">
                            <?= htmlspecialchars($user['cv_url']) ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Section Préférences -->
    <section class="settings-section">
        <h2 class="form-section-title">Préférences de Recherche</h2>
        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label">Type de contrat</label>
                <select class="form-select" name="type_contrat[]" multiple>
                    <option value="CDI" <?= in_array('CDI', $type_contrat) ? 'selected' : '' ?>>CDI</option>
                    <option value="CDD" <?= in_array('CDD', $type_contrat) ? 'selected' : '' ?>>CDD</option>
                    <option value="Stage" <?= in_array('Stage', $type_contrat) ? 'selected' : '' ?>>Stage</option>
                    <option value="Freelance" <?= in_array('Freelance', $type_contrat) ? 'selected' : '' ?>>Freelance</option>
                    <option value="Alternance" <?= in_array('Alternance', $type_contrat) ? 'selected' : '' ?>>Alternance</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Disponibilité</label>
                <select class="form-select" name="disponibilite[]" multiple>
                    <option value="Immédiate" <?= in_array('Immédiate', $disponibilite) ? 'selected' : '' ?>>Immédiate</option>
                    <option value="En recherche active" <?= in_array('En recherche active', $disponibilite) ? 'selected' : '' ?>>En recherche active</option>
                    <option value="Disponible sous 1 mois" <?= in_array('Disponible sous 1 mois', $disponibilite) ? 'selected' : '' ?>>Disponible sous 1 mois</option>
                    <option value="Disponible sous 3 mois" <?= in_array('Disponible sous 3 mois', $disponibilite) ? 'selected' : '' ?>>Disponible sous 3 mois</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Salaire souhaité</label>
                <input type="text" class="form-control" name="salaire" value="<?= htmlspecialchars($user['salaire'] ?? '') ?>" placeholder="Ex: 35k€ - 45k€">
            </div>
        </div>
    </section>

    <!-- Section Sécurité -->
    <section class="settings-section">
        <h2 class="form-section-title">Sécurité</h2>
        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label">Nouveau mot de passe</label>
                <input type="password" class="form-control" name="new_password">
            </div>
            <div class="col-md-6">
                <label class="form-label">Confirmation</label>
                <input type="password" class="form-control" name="confirm_password">
            </div>
            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="enable_2fa" id="2faCheck" <?= (isset($user['double_facteur']) && $user['double_facteur'] == 1) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="2faCheck">Authentification à deux facteurs</label>
                </div>
            </div>
            <div class="col-12">
                <button class="btn btn-primary" name="update_password" type="submit">Mettre à jour la sécurité</button>
            </div>
        </div>
    </section>

    <!-- Section Gestion du compte -->
    <section class="settings-section border border-danger mt-5">
        <h2 class="form-section-title text-danger">Gestion du compte</h2>
        <div class="alert alert-warning">
            <strong>Attention :</strong> Ces actions sont irréversibles.
        </div>
        <div class="row g-4">
            <div class="col-md-6">
                <button disabled class="btn btn-warning w-100" name="deactivate_account" type="submit" onclick="return confirm('Êtes-vous sûr de vouloir désactiver votre compte ?')">
                    Désactiver le compte
                </button>
            </div>
            <div class="col-md-6">
                <button class="btn btn-danger w-100" name="delete_account" type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer définitivement votre compte ?')">
                    Supprimer définitivement le compte
                </button>
            </div>
        </div>
    </section>

    <!-- Bouton Final -->
    <div class="text-center mt-5">
        <button class="btn btn-success btn-lg px-5" name="save_profil" type="submit">Enregistrer toutes les modifications</button>
    </div>
</form>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Confirmation avant suppression du CV
        function confirmDeleteCV() {
            if (confirm("Êtes-vous sûr de vouloir supprimer ce CV?")) {
                document.getElementById('delete_cv').value = '1';
                return true;
            }
            return false;
        }
    </script>
</body>

</html>