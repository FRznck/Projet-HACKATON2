<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['email'])) {
    header("Location: connexion.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "root", "hackathon");
if (!$conn) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

// Récupération de l'id_offre depuis l'URL
$id_offre = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Vérifie si l'offre existe
$sql_offre = "SELECT * FROM offres WHERE id_offre = '$id_offre'";
$resultat_offre = mysqli_query($conn, $sql_offre);
if (!$resultat_offre || mysqli_num_rows($resultat_offre) === 0) {
    echo "<script>alert('Offre introuvable.');</script>";
    header("Location: dashboard-candidat.php");
    exit();
}
$offre = mysqli_fetch_assoc($resultat_offre);

// Récupère l'email du candidat depuis la session
$email_candidat = $_SESSION['email'];
$message_confirmation = "";

// Cherche l'id du candidat par son email
$query_candidat = "SELECT id_utilisateur FROM utilisateurs WHERE email = '$email_candidat'";
$result_candidat = mysqli_query($conn, $query_candidat);

if ($result_candidat && mysqli_num_rows($result_candidat) > 0) {
    $row_candidat = mysqli_fetch_assoc($result_candidat);
    $id_candidat = $row_candidat['id_utilisateur'];
} else {
    echo "<script>alert('Candidat introuvable.');</script>";
    header("Location: dashboard-candidat.php");
    exit();
}

// Gérer la candidature
if (isset($_POST['postuler'])) {
    $lettre_motivation = mysqli_real_escape_string($conn, $_POST['lettre_motivation']);
    $date_candidature = date("Y-m-d H:i:s");
    $upload_errors = [];
    $upload_success = true;

    
    if (!file_exists('uploads')) {
        mkdir('uploads', 0777, true);
    }

    // Upload du CV (obligatoire)
    if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
        // Vérifier l'extension
        $cv_ext = strtolower(pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION));
        if ($cv_ext !== 'pdf') {
            $upload_errors[] = "Le CV doit être un fichier PDF.";
            $upload_success = false;
        } else {
            $cv_tmp = $_FILES['cv']['tmp_name'];
            $cv_filename = 'uploads/cv_' . time() . '_' . basename($_FILES['cv']['name']);
            if (!move_uploaded_file($cv_tmp, $cv_filename)) {
                $upload_errors[] = "Erreur lors de l'upload du CV.";
                $upload_success = false;
            }
        }
    } else {
        $upload_errors[] = "Veuillez télécharger un CV.";
        $upload_success = false;
    }

    // Upload de la lettre de motivation 
    if (isset($_FILES['lettre_fichier']) && $_FILES['lettre_fichier']['error'] === UPLOAD_ERR_OK) {
        // Vérif l'extension
        $lettre_ext = strtolower(pathinfo($_FILES['lettre_fichier']['name'], PATHINFO_EXTENSION));
        if ($lettre_ext !== 'pdf') {
            $upload_errors[] = "La lettre de motivation doit être un fichier PDF.";
            $upload_success = false;
        } else {
            $lettre_tmp = $_FILES['lettre_fichier']['tmp_name'];
            $lettre_filename = 'uploads/lettre_' . time() . '_' . basename($_FILES['lettre_fichier']['name']);
            if (!move_uploaded_file($lettre_tmp, $lettre_filename)) {
                $upload_errors[] = "Erreur lors de l'upload de la lettre de motivation.";
                $upload_success = false;
            }
        }
    } else {
        $upload_errors[] = "Veuillez télécharger une lettre de motivation (PDF).";
        $upload_success = false;
    }

    if ($upload_success) {
        // on échappe les noms des fichiers
        $cv_filename = mysqli_real_escape_string($conn, $cv_filename);
        $lettre_filename = mysqli_real_escape_string($conn, $lettre_filename);

        // Vérifie si le candidat a déjà postulé
        $verif_candidature = "SELECT * FROM candidatures WHERE id_candidat = '$id_candidat' AND id_offre = '$id_offre'";
        $resultat_verif = mysqli_query($conn, $verif_candidature);

        if (mysqli_num_rows($resultat_verif) > 0) {
            $message_confirmation = "⚠️ Vous avez déjà postulé à cette offre.";
        } else {
            // on récupére l'id du recruteur depuis l'offre
            $id_recruteur = $offre['id_recruteur']; 
            
            $requete_candidature = "
                INSERT INTO candidatures 
                (id_candidat, id_offre, lettre_motivation, cv_fichier, lettre_fichier, date_candidature, statut, id_recruteur, id_utilisateur)
                VALUES 
                ('$id_candidat', '$id_offre', '$lettre_motivation', '$cv_filename', '$lettre_filename', '$date_candidature', 'en attente', '$id_recruteur', '$id_candidat')
            ";

            if (mysqli_query($conn, $requete_candidature)) {
                $message_confirmation = "✅ Votre candidature a été envoyée avec succès !";
            } else {
                $message_confirmation = "❌ Erreur lors de l'envoi de votre candidature : " . mysqli_error($conn);
            }
        }
    } else {
        $message_confirmation = "❌ " . implode("<br>", $upload_errors);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Postuler à une offre</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="postuler-CSS/postuler.css">
</head>
<body>

<main class="container py-5">
<a href="accueil.php" class="btn btn-secondary mb-3">Retour au tableau de bord</a>
<?php
if (!isset($_SESSION['nom'])) {
    // on Récupère le nom depuis la base de données si nécessaire
    $email = $_SESSION['email'];
    $query = "SELECT nom FROM utilisateurs WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['nom'] = $user['nom'];
    } else {
        $_SESSION['nom'] = "Candidat"; // Valeur par défaut
    }
}
?>
    <h1 class="h2 mb-4"><strong><?php echo htmlspecialchars($_SESSION['nom']); ?></strong>, vous êtes sur le point de postuler à </h1>
    <h1 class="h3 mb-4">L'offre : <strong><?php echo htmlspecialchars($offre['titre']); ?></strong></h1>

    <div class="mb-4">
        <h2 class="h5">Détails de l'offre</h2>
        <p><strong>Compétences requises :</strong> <?php echo htmlspecialchars($offre['competences_requises']); ?></p>
        <p><strong>Description :</strong> <?php echo htmlspecialchars($offre['description']); ?></p>
    </div>

    <?php if (!empty($message_confirmation)): ?>
        <div class="alert alert-info"><?php echo $message_confirmation; ?></div>
    <?php endif; ?>

    

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="lettre_motivation" class="form-label">Quelque chose à ajouter ?</label>
            <textarea id="lettre_motivation" name="lettre_motivation" class="form-control" rows="5" maxlength="80" placeholder="Écrivez ici... (80 caractères max)" required></textarea>
        </div>

        <div class="mb-3">
            <label for="cv" class="form-label">Télécharger votre CV (PDF - obligatoire)</label>
            <input type="file" name="cv" class="form-control" accept=".pdf" required>
        </div>

        <div class="mb-3">
            <label for="lettre_fichier" class="form-label">Lettre de motivation (PDF - obligatoire)</label>
            <input type="file" name="lettre_fichier" class="form-control" accept=".pdf" required>
        </div>

        <button type="submit" name="postuler" class="btn btn-primary">Envoyer ma candidature</button>
    </form>
</main>
</body>
</html>