<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connexion à la base de données
$conn = mysqli_connect("localhost", "root", "root", "hackathon");
if (!$conn) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

if (!isset($_SESSION['email'])) {
    header("Location: connexion.php");
    exit();
}

// Récupérer l'ID du candidat
$email_candidat = $_SESSION['email'];
$query_candidat = "SELECT id_utilisateur FROM utilisateurs WHERE email = ?";
$stmt_candidat = $conn->prepare($query_candidat);
$stmt_candidat->bind_param("s", $email_candidat);
$stmt_candidat->execute();
$result_candidat = $stmt_candidat->get_result();

if ($result_candidat->num_rows === 0) {
    die("Candidat introuvable");
}

$row_candidat = $result_candidat->fetch_assoc();
$id_candidat = $row_candidat['id_utilisateur'];


$sql_notifs  = "
  SELECT id_notification, message, created_at
  FROM notifications
  WHERE user_id = ? AND is_read = 0
  ORDER BY created_at DESC
";
$stmt_notif  = $conn->prepare($sql_notifs);
$stmt_notif->bind_param("i", $id_candidat);
$stmt_notif->execute();
$result_notifs  = $stmt_notif->get_result();
$notifications  = $result_notifs->fetch_all(MYSQLI_ASSOC);
$stmt_notif->close();


// Récupérer les candidatures du candidat
$sql_candidatures = "
    SELECT 
        c.*,
        o.titre AS offre_titre,
        o.description AS offre_description,
        o.competences_requises
    FROM candidatures c
    JOIN offres o ON c.id_offre = o.id_offre
    WHERE c.id_candidat = ?
    ORDER BY c.date_candidature DESC
";

$stmt_candidatures = $conn->prepare($sql_candidatures);
$stmt_candidatures->bind_param("i", $id_candidat);
$stmt_candidatures->execute();
$result_candidatures = $stmt_candidatures->get_result();
$candidatures = $result_candidatures->fetch_all(MYSQLI_ASSOC);

// Gestion des messages
$message = '';
if (isset($_GET['message'])) {
    switch ($_GET['message']) {
        case 'success':
            $message = '<div class="alert alert-success">✅ Candidature envoyée</div>';
            break;
        case 'already_applied':
            $message = '<div class="alert alert-warning">⚠️ Déjà postulé</div>';
            break;
        case 'error':
            $message = '<div class="alert alert-danger">❌ Erreur</div>';
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
<meta charset="UTF-8">
    <title>Mes Candidatures</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="dashboard-candidat.css">
</head>

<body>
    <main class="container py-4">
        <a href="accueil.php" class="btn btn-outline-primary mb-3">
            <i class="bi bi-arrow-left"></i> Retour au tableau de bord
        </a>

        <?php if (!empty($notifications)): ?>
        <div class="alert alert-info mb-4 position-relative" style="border-left: 4px solid #4361ee;">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="this.parentElement.style.display='none'"></button>
            <h5 class="d-flex align-items-center">
                <i class="bi bi-bell-fill me-2" style="color: #4361ee;"></i>
                Vous avez <?= count($notifications) ?> nouvelle(s) notification(s)
            </h5>
            <div class="mt-2">
                <?php foreach ($notifications as $index => $n): ?>
                    <div class="notification-item p-2 mb-2 rounded" style="background-color: rgba(67, 97, 238, 0.05);">
                        <div class="d-flex justify-content-between">
                            <p class="mb-1"><?= htmlspecialchars($n['message']) ?></p>
                            <small class="text-muted"><?= date('d/m/Y H:i', strtotime($n['created_at'])) ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <a href="marquer_notifications_lues.php" class="btn btn-sm btn-primary mt-2">
                <i class="bi bi-check-circle"></i> Marquer comme lues
            </a>
        </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
            <?php

if (!isset($_SESSION['nom'])) {
    // Récupérez le nom depuis la base de données si nécessaire
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
                <h1 class="h3 mb-1">Bonjour <strong><?php echo htmlspecialchars($_SESSION['nom']); ?></strong>,</h1>
                <p class="text-muted mb-0">Voici l'état de vos candidatures</p>
            </div>
            <span class="badge bg-primary rounded-pill"><?= count($candidatures) ?> candidature(s)</span>
        </div>

        <?= $message ?>

        <div class="row g-4">
            <?php if (!empty($candidatures)): ?>
                <?php foreach ($candidatures as $candidature): ?>
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h5 class="card-title mb-3">
                                        <?= htmlspecialchars($candidature['offre_titre']) ?>
                                    </h5>
                                    <span class="statut-badge <?= str_replace(' ', '.', $candidature['statut']) ?>">
                                        <?= $candidature['statut'] ?>
                                    </span>
                                </div>

                                <p class="text-muted small mb-3">
                                    <i class="bi bi-calendar me-1"></i> Postulé le : <?= date('d/m/Y', strtotime($candidature['date_candidature'])) ?>
                                </p>

                                <div class="mb-3">
                                    <h6 class="d-inline-block me-2"><i class="bi bi-list-check"></i> Compétences requises :</h6>
                                    <div class="d-flex flex-wrap mt-2">
                                        <?php 
                                        $competences = explode(',', $candidature['competences_requises']);
                                        foreach ($competences as $competence): 
                                        ?>
                                            <span class="badge bg-light text-dark me-2 mb-2"><?= htmlspecialchars(trim($competence)) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="d-flex mt-4">
                                    <?php if ($candidature['cv_fichier']): ?>
                                        <a href="<?= htmlspecialchars($candidature['cv_fichier']) ?>"
                                            class="btn btn-sm btn-outline-secondary d-flex align-items-center fichier-badge"
                                            target="_blank">
                                            <i class="bi bi-file-earmark-pdf me-1"></i> CV
                                        </a>
                                    <?php endif; ?>

                                    <a href="<?= htmlspecialchars($candidature['lettre_fichier']) ?>"
                                        class="btn btn-sm btn-outline-primary d-flex align-items-center fichier-badge"
                                        target="_blank">
                                        <i class="bi bi-file-earmark-text me-1"></i> Lettre
                                    </a>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-top-0 pt-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle me-1"></i> Statut :
                                    </small>
                                    <?php
                                    switch ($candidature['statut']) {
                                        case 'En attente':
                                            echo '<span class="badge bg-warning text-dark"><i class="bi bi-hourglass me-1"></i>En attente</span>';
                                            break;
                                        case 'Acceptée':
                                            echo '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Acceptée</span>';
                                            break;
                                        case 'Refusée':
                                            echo '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Refusée</span>';
                                            break;
                                        case 'Entretien planifié':
                                            echo '<span class="badge bg-info text-white"><i class="bi bi-calendar-check me-1"></i>Entretien planifié</span>';
                                            break;
                                        default:
                                            echo '<span class="badge bg-secondary">Inconnu</span>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info d-flex align-items-center">
                        <i class="bi bi-info-circle-fill me-2 fs-4"></i>
                        <div>
                            <h5 class="mb-1">Aucune candidature trouvée</h5>
                            <p class="mb-0">Commencez par postuler à des offres qui vous intéressent</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animation pour les cartes lorsqu'elles apparaissent
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.animation = `fadeInUp 0.5s ease forwards ${index * 0.1}s`;
            });
            
            // Ajout du CSS pour l'animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes fadeInUp {
                    from {
                        opacity: 0;
                        transform: translateY(20px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
            `;
            document.head.appendChild(style);
        });
    </script>
</body>

</html>