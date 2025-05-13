<?php
session_start();


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Affichage des messages de session
if (isset($_SESSION['success'])) {
    echo "<script>alert('" . $_SESSION['success'] . "');</script>";
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    echo "<script>alert('" . $_SESSION['error'] . "');</script>";
    unset($_SESSION['error']);
}

// Connexion à la base de données
$id = mysqli_connect("localhost", "root", "root", "hackathon");
if (!$id) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

mysqli_set_charset($id, "utf8mb4");

// Vérification de la session
if (!isset($_SESSION['id'])) {
    header("Location: connexion-recruteur.php");
    exit();
}

// Publier une offre
if (isset($_POST['publier_offre'])) {
    $id_recruteur = $_SESSION['id'];
    $titre = mysqli_real_escape_string($id, $_POST['titre']);
    $competences = mysqli_real_escape_string($id, $_POST['competences']);
    $type_contrat = mysqli_real_escape_string($id, $_POST['type_contrat']);
    $duree = mysqli_real_escape_string($id, $_POST['duree']);
    $salaire = mysqli_real_escape_string($id, $_POST['salaire']);
    $description = mysqli_real_escape_string($id, $_POST['description']);
    $lieu = mysqli_real_escape_string($id, $_POST['lieu']);
    $nom_entreprise = mysqli_real_escape_string($id, $_POST['nom_entreprise']);

    $id_entreprise = isset($_SESSION['id_entreprise']) ? $_SESSION['id_entreprise'] : 0; 
    $requete = "
        INSERT INTO offres (id_recruteur, id_entreprise, titre, competences_requises, type_contrat, duree, salaire, description, lieu, nom_entreprise, date_publication)
        VALUES ('$id_recruteur', '$id_entreprise', '$titre', '$competences', '$type_contrat', '$duree', '$salaire', '$description', '$lieu', '$nom_entreprise', NOW())";

    if (mysqli_query($id, $requete)) {
        echo "<script>alert('Offre publiée avec succès.');</script>";
        header("Location: dashboard-recruteur.php");
        exit();
    } else {
        echo "<script>alert('Offre publiée avec succès.');</script>";
    }
}

// Supprimer une offre
if (isset($_GET['supprimer_offre'])) {
    $id_offre = mysqli_real_escape_string($id, $_GET['supprimer_offre']);
    $id_recruteur = $_SESSION['id']; 

    $requete = "DELETE FROM offres WHERE id_offre = '$id_offre' AND id_recruteur = '$id_recruteur'";

    if (mysqli_query($id, $requete)) {
        // Utilise uniquement header() pour rediriger, sans echo avant
        header("Location: dashboard-recruteur.php?message=offre_supprimee");
        exit();
    } else {
        echo "<script>alert('Erreur lors de la suppression de l\\'offre : " . mysqli_error($id) . "');</script>";
    }
}


// Modifier une offre
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['modifier_offre'])) {
    //$id_recruteur = $_SESSION['id'];
    $id_offre = mysqli_real_escape_string($id, $_POST['id_offre']);
    $titre = mysqli_real_escape_string($id, $_POST['titre']);
    $description = mysqli_real_escape_string($id, $_POST['description']);
    $lieu = mysqli_real_escape_string($id, $_POST['lieu']);
    $salaire = mysqli_real_escape_string($id, $_POST['salaire']);
    $type_contrat = mysqli_real_escape_string($id, $_POST['type_contrat']);
    $duree = mysqli_real_escape_string($id, $_POST['duree']);
    $competences = mysqli_real_escape_string($id, $_POST['competences']);
    $nom_entreprise = mysqli_real_escape_string($id, $_POST['nom_entreprise']);


    $sql = "
        UPDATE offres
        SET titre = '$titre', 
            description = '$description', 
            lieu = '$lieu', 
            salaire = '$salaire', 
            type_contrat = '$type_contrat', 
            duree = '$duree', 
            competences_requises = '$competences', 
            nom_entreprise = '$nom_entreprise', 
            date_publication = NOW()
        WHERE id_offre = '$id_offre' AND id_recruteur = '{$_SESSION['id']}'
    ";

    if (mysqli_query($id, $sql)) {
        echo "<script>alert('Offre modifiée avec succès !');</script>";
        header("Location: dashboard-recruteur.php");
        exit();
    } else {
        echo "Erreur : " . mysqli_error($id);
    }
}

// Récupérer les offres publiées
$sql_offres = "
SELECT o.*, e.nom_entreprise
FROM offres o
JOIN entreprise e ON o.id_recruteur = e.id
WHERE o.id_recruteur = ?
";

$stmt_offres = $id->prepare($sql_offres);
$stmt_offres->bind_param("i", $_SESSION['id']);
$stmt_offres->execute();
$result_offres = $stmt_offres->get_result();
$offres = [];
while ($row = $result_offres->fetch_assoc()) {
    $offres[] = $row;
}




$sql_candidatures = "
    SELECT c.*, o.titre AS offre, u.nom, u.prenom, u.email, u.telephone, u.localisation, u.profil_linkedin
    FROM candidatures c
    JOIN offres o ON c.id_offre = o.id_offre
    LEFT JOIN utilisateurs u ON c.id_utilisateur = u.id_utilisateur
    WHERE o.id_recruteur = ?
";


$stmt_candidatures = $id->prepare($sql_candidatures);
$stmt_candidatures->bind_param("i", $_SESSION['id']);
$stmt_candidatures->execute();
$result_candidatures = $stmt_candidatures->get_result();
$candidatures = [];
while ($row = $result_candidatures->fetch_assoc()) {
    $candidatures[] = $row;
}
// Récupérer les messages premium
$sql_messages = "
    SELECT m.id_message, m.contenu, m.date_envoi, c.nom AS candidat_nom, c.email AS candidat_email, c.id AS id_candidat
    FROM messages m
    JOIN candidats c ON m.id_expediteur = c.id
    WHERE m.id_destinataire = ?
    ORDER BY m.date_envoi DESC
";
$stmt_messages = $id->prepare($sql_messages);
$stmt_messages->bind_param("i", $_SESSION['id']);
$stmt_messages->execute();
$result_messages = $stmt_messages->get_result();
$messages = [];
while ($row = $result_messages->fetch_assoc()) {
    $messages[] = $row;
}

// Répondre à un message premium
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['repondre_message'])) {
    $id_candidat = mysqli_real_escape_string($id, $_POST['id_candidat']);
    $reponse = mysqli_real_escape_string($id, $_POST['reponse']);
    $id_recruteur = $_SESSION['id'];

    $sql_reponse = "
        INSERT INTO messages (id_expediteur, id_destinataire, contenu, date_envoi)
        VALUES ('$id_recruteur', '$id_candidat', '$reponse', NOW())
    ";

    if (mysqli_query($id, $sql_reponse)) {
        echo "<script>alert('Réponse envoyée avec succès.');</script>";
        header("Location: dashboard-recruteur.php");
        exit();
    } else {
        echo "<script>alert('Erreur lors de l\'envoi de la réponse : " . mysqli_error($id) . "');</script>";
    }
}

//teste




?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tableau de Bord Recruteur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="dashboard-recruteur.css">
</head>

<body>
    <nav class="navbar fixed-top custom-top-bar">
        <div class="container-fluid">
            <h1 class="navbar-brand fs-3 fw-medium mb-0">
                <i class="fas fa-briefcase me-2"></i>Tableau de Bord Recruteur
            </h1>
            <a href="deconnexion_recruteur.php" class="btn btn-outline-danger">
    <i class="fas fa-sign-out-alt me-1"></i> Déconnexion
</a>

        </div>
    </nav>
    <main class="container-fluid" style="margin-top: 100px;">
        <!-- Ajouter une Offre -->
        <section class="py-5">
            <div class="container">
                <h2 class="section-title text-center">Publier une offre</h2>
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <form class="row g-4" method="POST">
                            <div class="col-md-6">
                                <label for="titre" class="form-label">Titre de l'offre</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-heading"></i></span>
                                    <input type="text" id="titre" name="titre" class="form-control" placeholder="Entrez le titre de l'offre" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="competences" class="form-label">Compétences requises</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-tools"></i></span>
                                    <input type="text" id="competences" name="competences" class="form-control" placeholder="Entrez les compétences requises" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="type_contrat" class="form-label">Type de contrat</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-file-contract"></i></span>
                                    <select id="type_contrat" name="type_contrat" class="form-select" required>
                                        <option value="CDD">CDD</option>
                                        <option value="CDI">CDI</option>
                                        <option value="Stage">Stage</option>
                                        <option value="Apprentissage">Contrat d'apprentissage</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="duree" class="form-label">Durée</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="text" id="duree" name="duree" class="form-control" placeholder="Exemple : 6 mois">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="salaire" class="form-label">Salaire</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-euro-sign"></i></span>
                                    <input type="number" id="salaire" name="salaire" class="form-control" placeholder="Exemple : 2500" step="0.01">
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea id="description" name="description" class="form-control" rows="4" placeholder="Entrez la description de l'offre" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="lieu" class="form-label">Lieu</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    <input type="text" id="lieu" name="lieu" class="form-control" placeholder="Entrez le lieu de travail" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="nom_entreprise" class="form-label"><strong>Quelle entreprise postule ?</strong></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                                    <input type="text" id="nom_entreprise" name="nom_entreprise" class="form-control" placeholder="Exemple : PlateformeRecrute.Inc" required>
                                </div>
                                <details>
                                    <summary><strong>A savoir</strong></summary>
                                    <p>Cette section peut correspondre à votre entreprise</p>
                                    <p>ou à un département spécifique pour lequel vous recrutez au sein de votre organisation.</p>
                                </details>
                            </div>
                            <div class="col-12 text-center mt-3">
                                <button type="submit" name="publier_offre" class="btn btn-dark btn-lg px-5 py-3">
                                    <i class="fas fa-paper-plane me-2"></i>Publier l'offre
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Offres Publiées -->
        <section class="row justify-content-center mb-5 py-5 bg-light">
            <div class="col-12 col-lg-10">
                <h2 class="section-title">Vos Offres Publiées</h2>
                <div class="row g-4">
                    <?php if (!empty($offres)): ?>
                        <?php foreach ($offres as $offre): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="job-card">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h3><?= htmlspecialchars($offre['titre']); ?></h3>
                                        <span class="badge bg-primary"><?= htmlspecialchars($offre['type_contrat']); ?></span>
                                    </div>
                                    <p class="text-muted"><?= htmlspecialchars(substr($offre['description'], 0, 100)) . '...'; ?></p>
                                    <div class="job-details mt-3">
                                        <p><i class="fas fa-map-marker-alt me-2 text-secondary"></i> <?= htmlspecialchars($offre['lieu']); ?></p>
                                        <p><i class="fas fa-euro-sign me-2 text-secondary"></i> <?= htmlspecialchars($offre['salaire'] ? $offre['salaire'] . ' €' : 'Non précisé'); ?></p>
                                        <p><i class="fas fa-clock me-2 text-secondary"></i> <?= htmlspecialchars($offre['duree']); ?></p>
                                        <p class="text-muted"><small><i class="fas fa-calendar me-2"></i>Publié le : <?= htmlspecialchars($offre['date_publication']); ?></small></p>
                                    </div>
                                    <div class="d-flex justify-content-between mt-4">
                                        <form method="GET" class="me-2">
                                            <input type="hidden" name="supprimer_offre" value="<?= $offre['id_offre']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash me-1"></i> Supprimer
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modifierOffre<?= $offre['id_offre']; ?>">
                                            <i class="fas fa-edit me-1"></i> Modifier
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Modifier Offre -->
                            <div class="modal fade" id="modifierOffre<?= $offre['id_offre']; ?>" tabindex="-1" aria-labelledby="modifierOffreLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="modifierOffreLabel">Modifier l'Offre</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form method="POST" action="">
                                            <div class="modal-body">
                                                <input type="hidden" name="id_offre" value="<?= $offre['id_offre']; ?>">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Titre</label>
                                                        <input type="text" class="form-control" name="titre" value="<?= htmlspecialchars($offre['titre']); ?>" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Type de contrat</label>
                                                        <input type="text" class="form-control" name="type_contrat" value="<?= htmlspecialchars($offre['type_contrat']); ?>" required>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label">Description</label>
                                                        <textarea class="form-control" name="description" rows="4" required><?= htmlspecialchars($offre['description']); ?></textarea>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Lieu</label>
                                                        <input type="text" class="form-control" name="lieu" value="<?= htmlspecialchars($offre['lieu']); ?>" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Salaire</label>
                                                        <input type="text" class="form-control" name="salaire" value="<?= htmlspecialchars($offre['salaire']); ?>">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Durée</label>
                                                        <input type="text" class="form-control" name="duree" value="<?= htmlspecialchars($offre['duree']); ?>">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Compétences requises</label>
                                                        <input type="text" class="form-control" name="competences" value="<?= htmlspecialchars($offre['competences_requises']); ?>">
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label">Qui postule ?</label>
                                                        <input type="text" class="form-control" name="nom_entreprise" value="<?= htmlspecialchars($offre['nom_entreprise']); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                <button type="submit" name="modifier_offre" class="btn btn-primary">Enregistrer</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> Aucune offre publiée pour le moment.
                            </div>
                            <a href="#publier-offre" class="btn btn-primary mt-3">
                                <i class="fas fa-plus me-2"></i>Publier votre première offre
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Candidatures Reçues -->
        <section class="row justify-content-center mb-5 py-5">
            <div class="col-12 col-lg-10">
                <h2 class="section-title">Candidatures Reçues</h2>
                <div class="row g-4">
                    <?php if (!empty($candidatures)): ?>
                        <?php foreach ($candidatures as $candidature): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="candidate-card p-4 border rounded shadow-sm h-100">
                                    <div class="candidate-header d-flex align-items-center mb-3">
                                        <div class="candidate-avatar me-3">
                                            <i class="fas fa-user-circle fa-3x text-secondary"></i>
                                        </div>
                                        <div>
                                            <h4 class="mb-0">
                                                <?= isset($candidature['prenom']) && !empty($candidature['prenom']) ? htmlspecialchars($candidature['prenom']) : 'Candidat'; ?>
                                                <?= isset($candidature['nom']) && !empty($candidature['nom']) ? htmlspecialchars($candidature['nom']) : ''; ?>
                                            </h4>
                                            <small class="text-muted">
                                                <i class="fas fa-envelope me-1"></i>
                                                <?= isset($candidature['email']) && !empty($candidature['email']) ? htmlspecialchars($candidature['email']) : 'Email non disponible'; ?>
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div class="candidate-info mb-3">
                                        <p class="mb-1"><i class="fas fa-phone me-2 text-secondary"></i> <?= isset($candidature['telephone']) && !empty($candidature['telephone']) ? htmlspecialchars($candidature['telephone']) : 'Non renseigné'; ?></p>
                                        <p class="mb-1"><i class="fas fa-map-marker-alt me-2 text-secondary"></i> <?= isset($candidature['localisation']) && !empty($candidature['localisation']) ? htmlspecialchars($candidature['localisation']) : 'Non renseignée'; ?></p>
                                        <p class="mb-1"><i class="fab fa-linkedin me-2 text-secondary"></i> <?= isset($candidature['profil_linkedin']) && !empty($candidature['profil_linkedin']) ? htmlspecialchars($candidature['profil_linkedin']) : 'Non renseigné'; ?></p>
                                    </div>
                                    
                                    <div class="candidature-details bg-light p-3 rounded mb-3">
                                        <h5 class="text-primary mb-2"><i class="fas fa-briefcase me-2"></i>Poste :</h5>
                                        <p><?= htmlspecialchars($candidature['offre'] ?? 'Non spécifié'); ?></p>
                                        
                                        <h5 class="text-primary mb-2 mt-3"><i class="fas fa-calendar me-2"></i>Date :</h5>
                                        <p><?= htmlspecialchars($candidature['date_candidature'] ?? 'Non précisée'); ?></p>
                                        
                                        <?php if (!empty($candidature['lettre_motivation'])): ?>
                                            <div class="motivation-section mt-3">
                                                <h5 class="text-primary"><i class="fas fa-envelope-open-text me-2"></i>Lettre de motivation :</h5>
                                                <div class="p-3 bg-white rounded mt-2">
                                                    <?= nl2br(htmlspecialchars(substr($candidature['lettre_motivation'], 0, 150) . '...')); ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="documents-section mb-3">
                                        <h5 class="text-primary"><i class="fas fa-file me-2"></i>Documents :</h5>
                                        <div class="d-flex flex-wrap mt-2">
                                            <?php if (!empty($candidature['cv_fichier'])): ?>
                                                <a href="<?= htmlspecialchars($candidature['cv_fichier']); ?>" target="_blank" class="btn btn-outline-primary btn-sm me-2 mb-2">
                                                    <i class="fas fa-file-pdf me-1"></i> CV
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($candidature['lettre_fichier'])): ?>
                                                <a href="<?= htmlspecialchars($candidature['lettre_fichier']); ?>" target="_blank" class="btn btn-outline-secondary btn-sm mb-2">
                                                    <i class="fas fa-file-alt me-1"></i> Lettre
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <form method="POST" action="update-statut.php" class="mt-auto">
                                        <input type="hidden" name="id_utilisateur" value="<?= htmlspecialchars($candidature['id_utilisateur']); ?>">
                                        <input type="hidden" name="id_offre" value="<?= htmlspecialchars($candidature['id_offre']); ?>">
                                        
                                        <div class="form-group mb-3">
                                            <label for="statut" class="form-label">Statut :</label>
                                            <select name="statut" class="form-select" required>
                                                <option value="En attente" <?= ($candidature['statut'] ?? '') === 'En attente' ? 'selected' : '' ?>>
                                                    <i class="fas fa-clock me-1"></i> En attente
                                                </option>
                                                <option value="Acceptée" <?= ($candidature['statut'] ?? '') === 'Acceptée' ? 'selected' : '' ?>>
                                                    <i class="fas fa-check-circle me-1"></i> Acceptée
                                                </option>
                                                <option value="Refusée" <?= ($candidature['statut'] ?? '') === 'Refusée' ? 'selected' : '' ?>>
                                                    <i class="fas fa-times-circle me-1"></i> Refusée
                                                </option>
                                                <option value="Entretien planifié" <?= ($candidature['statut'] ?? '') === 'Entretien planifié' ? 'selected' : '' ?>>
                                                    <i class="fas fa-calendar-check me-1"></i> Entretien planifié
                                                </option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-save me-1"></i> Mettre à jour
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> Aucune candidature reçue pour le moment.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Messages Premium -->
        <section class="row justify-content-center mb-5 py-5 bg-light">
            <div class="col-12 col-lg-10">
                <h2 class="section-title">Messages Reçus</h2>
                <?php if (!empty($messages)): ?>
                    <div class="accordion" id="messagesAccordion">
                        <?php foreach ($messages as $index => $message): ?>
                            <div class="accordion-item border-0 mb-3 shadow-sm">
                                <h2 class="accordion-header" id="heading<?= $index; ?>">
                                    <button class="accordion-button <?= $index > 0 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $index; ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false'; ?>" aria-controls="collapse<?= $index; ?>">
                                        <i class="fas fa-envelope me-3 text-primary"></i>
                                        <strong>Message de <?= htmlspecialchars($message['candidat_nom']); ?></strong>
                                        <small class="text-muted ms-3">(<?= htmlspecialchars($message['candidat_email']); ?>)</small>
                                    </button>
                                </h2>
                                <div id="collapse<?= $index; ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : ''; ?>" aria-labelledby="heading<?= $index; ?>" data-bs-parent="#messagesAccordion">
                                    <div class="accordion-body">
                                        <div class="message-content bg-white p-4 rounded mb-4">
                                            <p class="mb-3"><strong><i class="fas fa-quote-left me-2 text-secondary"></i>Message :</strong></p>
                                            <p class="ps-4"><?= htmlspecialchars($message['contenu']); ?></p>
                                            <p class="text-muted text-end mt-3"><small><i class="fas fa-clock me-1"></i>Envoyé le : <?= htmlspecialchars($message['date_envoi']); ?></small></p>
                                        </div>
                                        
                                        <form method="POST" action="dashboard-recruteur.php">
                                            <input type="hidden" name="id_candidat" value="<?= $message['id_candidat']; ?>">
                                            <div class="mb-3">
                                                <label for="reponse" class="form-label"><i class="fas fa-reply me-2"></i>Votre réponse :</label>
                                                <textarea id="reponse" name="reponse" class="form-control" rows="3" required></textarea>
                                            </div>
                                            <button type="submit" name="repondre_message" class="btn btn-primary">
                                                <i class="fas fa-paper-plane me-1"></i> Envoyer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Aucun message reçu pour le moment.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Candidatures Spontanées -->
        <section class="row justify-content-center mb-5 py-5">
            <div class="col-12 col-lg-10">
                <h2 class="section-title">Candidatures Spontanées</h2>
                <div id="applications-list" class="container my-5">
                    <script>
                        fetch('http://localhost:5000/list_applications')
                            .then(res => res.json())
                            .then(data => {
                                if (data.error) {
                                    document.getElementById('applications-list').innerHTML = `
                                        <div class="alert alert-danger" role="alert">
                                            <i class="fas fa-exclamation-triangle me-2"></i> Erreur : ${data.error}
                                        </div>`;
                                    return;
                                }
                                
                                if (data.length === 0) {
                                    document.getElementById('applications-list').innerHTML = `
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i> Aucune candidature spontanée reçue pour le moment.
                                        </div>`;
                                    return;
                                }
                                
                                let html = `
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th><i class="fas fa-file me-2"></i>Fichier</th>
                                                    <th><i class="fas fa-calendar me-2"></i>Date de réception</th>
                                                    <th><i class="fas fa-download me-2"></i>Télécharger</th>
                                                </tr>
                                            </thead>
                                            <tbody>`;
                                
                                data.forEach(app => {
                                    html += `
                                        <tr>
                                            <td>
                                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                                ${app.filename}
                                            </td>
                                            <td>
                                                ${new Date(app.uploaded_at * 1000).toLocaleString()}
                                            </td>
                                            <td>
                                                <a href="/frontend2/ia/applications/${app.filename}" 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download me-1"></i> Télécharger
                                                </a>
                                            </td>
                                        </tr>`;
                                });
                                
                                html += `</tbody></table></div>`;
                                document.getElementById('applications-list').innerHTML = html;
                            });
                    </script>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p class="mb-0">
                <i class="fas fa-briefcase me-2"></i> Plateforme de Recrutement Intelligent © 2023
            </p>
            <div class="mt-3">
                <a href="#" class="text-white mx-2"><i class="fab fa-linkedin fa-lg"></i></a>
                <a href="#" class="text-white mx-2"><i class="fab fa-twitter fa-lg"></i></a>
                <a href="#" class="text-white mx-2"><i class="fab fa-facebook fa-lg"></i></a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>