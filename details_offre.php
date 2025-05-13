<?php
session_start();
$id = mysqli_connect("localhost", "root", "root", "hackathon");

if (!$id) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

$offre_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($offre_id > 0) {
    $sql = "SELECT o.*, e.nom_entreprise 
            FROM offres o
            LEFT JOIN entreprise e ON o.id_entreprise = e.id
            WHERE o.id_offre = ?";
    $stmt = mysqli_prepare($id, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $offre_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $offre = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
} else {
    $offre = null;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'offre | Plateforme Recrutement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="details_offre.css">
</head>
<body>
    <div class="container mt-4">
        <a href="javascript:history.back()" class="btn btn-back">
            <i class="fas fa-arrow-left me-2"></i> Retour aux offres
        </a>
    </div>

    <div class="offer-container">
        <?php if ($offre): ?>
            <div class="offer-card shadow-lg">
                <div class="offer-header text-center">
                    <h1 class="offer-title">Détails de l'offre "<?= htmlspecialchars($offre['titre']) ?>"</h1>
                    <?php if (!empty($offre['nom_entreprise'])): ?>
                        <div class="offer-company justify-content-center">
                            <i class="fas fa-building me-2"></i><?= htmlspecialchars($offre['nom_entreprise']) ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="offer-body">
                    <div class="offer-details">
                        <?php if (!empty($offre['type_contrat'])): ?>
                            <div class="detail-item">
                                <div class="detail-label">Type de contrat</div>
                                <div class="detail-value">
                                    <i class="fas fa-file-contract me-2"></i><?= htmlspecialchars($offre['type_contrat']) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($offre['lieu'])): ?>
                            <div class="detail-item">
                                <div class="detail-label">Localisation</div>
                                <div class="detail-value">
                                    <i class="fas fa-map-marker-alt me-2"></i><?= htmlspecialchars($offre['lieu']) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($offre['salaire'])): ?>
                            <div class="detail-item">
                                <div class="detail-label">Salaire</div>
                                <div class="detail-value">
                                    <i class="fas fa-euro-sign me-2"></i><?= htmlspecialchars($offre['salaire']) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($offre['date_publication'])): ?>
                            <div class="detail-item">
                                <div class="detail-label">Date de publication</div>
                                <div class="detail-value">
                                    <i class="fas fa-calendar-alt me-2"></i><?= htmlspecialchars($offre['date_publication']) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($offre['description'])): ?>
                        <div class="offer-section">
                            <h3 class="section-title">
                                <i class="fas fa-align-left"></i>Description du poste
                            </h3>
                            <p><?= nl2br(htmlspecialchars($offre['description'])) ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($offre['competences_requises'])): ?>
                        <div class="offer-section">
                            <h3 class="section-title">
                                <i class="fas fa-tasks"></i>Compétences requises
                            </h3>
                            <div class="skills-container">
                                <?php 
                                $competences = explode(',', $offre['competences_requises']);
                                foreach ($competences as $competence): 
                                    if (trim($competence)): ?>
                                        <span class="skill-badge"><?= htmlspecialchars(trim($competence)) ?></span>
                                    <?php endif;
                                endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="text-center mt-5">
                        <a href="postuler.php?id=<?= $offre['id_offre'] ?>" class="btn btn-apply">
                            <i class="fas fa-paper-plane"></i> Postuler maintenant
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger alert-offer text-center">
                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                <h3>Offre introuvable</h3>
                <p class="mb-0">L'offre que vous recherchez n'existe pas ou a été supprimée.</p>
                <a href="offres.php" class="btn btn-outline-primary mt-3">
                    <i class="fas fa-arrow-left me-2"></i> Retour aux offres
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>