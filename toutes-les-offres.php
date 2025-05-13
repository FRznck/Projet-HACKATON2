<?php
// Configuration
$client_id = 'PAR_monsitejob_e4254fc9971450fe893d671b208c2ba0eda70f596ddc9e35edc9d2e6136a6e95';
$client_secret = 'd1b93e4e0adc29da862d7ed16ec7d938e242c613dbee5436c727c37590bb8fbb';
$token_url = 'https://entreprise.pole-emploi.fr/connexion/oauth2/access_token?realm=%2Fpartenaire';
$api_url = "https://api.pole-emploi.io/partenaire/offresdemploi/v2/offres/search?motsCles=developpeur&departement=75&range=0-19";


function makeApiRequest($url, $postData = null, $headers = []) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    // Configuration SSL - À adapter selon votre environnement
    $isProduction = false; // Mettre à true en production
    if ($isProduction) {
        // Configuration PRODUCTION sécurisée
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_CAINFO, '/chemin/vers/cacert.pem');
    } else {
        // Configuration DEVELOPPEMENT
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    }
    
    if ($postData) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    }
    
    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($error) {
        return ['error' => "Erreur cURL : $error"];
    }
    
    return json_decode($response, true) ?? $response;
}

// 1. Obtenir le token OAuth2
$token_data = [
    'grant_type' => 'client_credentials',
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'scope' => 'api_offresdemploiv2'  // Utilisation d'un seul scope
];


$token_response = makeApiRequest($token_url, $token_data, [
    'Content-Type: application/x-www-form-urlencoded'
]);

if (isset($token_response['error'])) {
    die('Erreur lors de l\'obtention du token: ' . $token_response['error']);
}

$access_token = $token_response['access_token'] ?? null;
if (!$access_token) {
    die('Erreur: Aucun token reçu. Réponse: ' . print_r($token_response, true));
}

// 2. Récupérer les offres d'emploi
$offres = makeApiRequest($api_url, null, [
    "Authorization: Bearer $access_token",
    "Accept: application/json"
]);

if (isset($offres['error'])) {
    die('Erreur API: ' . $offres['error']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offres d'emploi - Développeur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card { transition: transform 0.3s; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .badge-contract { background-color: #6c757d; color: white; }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="display-4">Offres pour Développeurs</h1>
            <p class="lead">Dernières offres disponibles via l'API Pôle Emploi</p>
        </div>

        <?php if (isset($offres['error'])): ?>
            <div class="alert alert-danger">
                <h4 class="alert-heading">Erreur</h4>
                <p><?= htmlspecialchars($offres['error']) ?></p>
                <?php if (isset($offres['error_description'])): ?>
                    <hr>
                    <p class="mb-0"><?= htmlspecialchars($offres['error_description']) ?></p>
                <?php endif; ?>
            </div>
        
        <?php elseif (empty($offres['resultats'])): ?>
            <div class="alert alert-info text-center">
                <h4 class="alert-heading">Aucune offre disponible</h4>
                <p>Il n'y a actuellement aucune offre correspondant à votre recherche.</p>
            </div>
        
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($offres['resultats'] as $offre): ?>
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0"><?= htmlspecialchars($offre['intitule']) ?></h5>
                            </div>
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">
                                    <?= htmlspecialchars($offre['entreprise']['nom'] ?? 'Entreprise non précisée') ?>
                                </h6>
                                 
                                <p class="card-text">
                                    <i class="bi bi-geo-alt"></i> 
                                    <?= htmlspecialchars($offre['lieuTravail']['libelle'] ?? 'Lieu non précisé') ?>
                                </p>
                                 
                                <div class="mb-2">
                                    <span class="badge badge-contract">
                                        <?= htmlspecialchars($offre['typeContrat'] ?? 'CDI') ?>
                                    </span>
                                    <?php if (!empty($offre['salaire']['libelle'])): ?>
                                        <span class="badge bg-success ms-1">
                                            <?= htmlspecialchars($offre['salaire']['libelle']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                 
                                <p class="card-text">
                                    <?= nl2br(htmlspecialchars(substr(strip_tags($offre['description']), 0, 200))) ?>...
                                </p>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        Publié le <?= date('d/m/Y', strtotime($offre['dateCreation'])) ?>
                                    </small>
                                    <a href="<?= htmlspecialchars($offre['origineOffre']['url'] ?? '#') ?>" 
                                       target="_blank" 
                                       class="btn btn-sm btn-primary">
                                        Voir l'offre
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>Données fournies par l'API Pôle Emploi</p>
            <p class="mb-0">Dernière actualisation : <?= date('d/m/Y H:i') ?></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
