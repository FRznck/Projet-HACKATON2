<?php
// 1. Identifiants API France Travail
$client_id = "PAR_projethackathon_0a222e2d7071024ed5712a7e2ddd26e97a01b802673caaaae84e098423458e18";
$client_secret = "7ca2d76bb144f9193ba85e025bb487b6f95f104d333938cc10cb2a6277a36ad1";

// 2. Fonction pour récupérer le token avec debug
function getAccessToken($client_id, $client_secret) {
    $url = "https://entreprise.pole-emploi.fr/connexion/oauth2/access_token?realm=/partenaire";
    $data = [
        'grant_type' => 'client_credentials',
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'scope' => 'api_offresdemploiv2 o2dsoffre'
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_SSL_VERIFYPEER => false, 
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_VERBOSE => true
    ]);
    
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($error) {
        file_put_contents('api_error.log', "Erreur token: ".$error.PHP_EOL, FILE_APPEND);
        return null;
    }

    $res = json_decode($response, true);
    return $res['access_token'] ?? null;
}

// 3. Fonction pour récupérer les offres avec debug
function getOffresFranceTravail($token) {
    // Paramètres plus larges pour maximiser les résultats
    $url = "https://api.pole-emploi.io/partenaire/offresdemploi/v2/offres/search?motsCles=informatique&departement=75&range=0-49";

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $token",
            "Content-Type: application/json"
        ],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_VERBOSE => true
    ]);
    
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($error) {
        file_put_contents('api_error.log', "Erreur API: ".$error.PHP_EOL, FILE_APPEND);
        return ['resultats' => []];
    }

    return json_decode($response, true) ?? ['resultats' => []];
}

// 4. Récupération des données avec logs
$token = getAccessToken($client_id, $client_secret);
file_put_contents('api_debug.log', "Token: ".print_r($token, true).PHP_EOL, FILE_APPEND);

if ($token) {
    $offres_api = getOffresFranceTravail($token);
    file_put_contents('api_debug.log', "Réponse API: ".print_r($offres_api, true).PHP_EOL, FILE_APPEND);
} else {
    $offres_api = ['resultats' => []];
    file_put_contents('api_error.log', "Échec d'obtention du token".PHP_EOL, FILE_APPEND);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Offres France Travail</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="offres_france_travail.css">
</head>
<body>
<div class="container py-5">
<h1 class="text-center mb-4">Offres d'emploi Externe</h1>
<div class="text-center mb-5">
    <a href="accueil.php" class="btn btn-outline-secondary">← Retour à l'accueil</a>
</div>

    
    <?php if (!empty($offres_api['resultats'])): ?>
        <div class="row">
            <?php foreach ($offres_api['resultats'] as $offre): ?>
                <div class="col-md-4">
                    <div class="job-card">
                        <h3><?= htmlspecialchars($offre['intitule'] ?? 'Offre sans titre') ?></h3>
                        <p><?= htmlspecialchars(substr($offre['description'] ?? 'Description non disponible', 0, 100)) ?>...</p>
                        <p><strong>Type:</strong> <?= htmlspecialchars($offre['typeContratLibelle'] ?? 'CDD') ?></p>
                        <p><strong>Lieu:</strong> <?= htmlspecialchars($offre['lieuTravail']['libelle'] ?? 'Paris') ?></p>
                        <p><strong>Entreprise:</strong> <?= htmlspecialchars($offre['entreprise']['nom'] ?? 'Non communiqué') ?></p>
                        <a href="<?= htmlspecialchars($offre['origineOffre']['urlOrigine'] ?? '#') ?>" class="btn btn-primary">Voir l'offre</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            <h4>Aucune offre disponible actuellement</h4>
            <p>Veuillez réessayer plus tard ou vérifier les paramètres de connexion à l'API.</p>
            
            <!-- Debug (à masquer en production) -->
            <div class="debug-info mt-3">
                <h5>Informations de debug :</h5>
                <p>Token: <?= isset($token) ? 'Obtention réussie' : 'Échec d\'obtention' ?></p>
                <?php if (isset($offres_api) && array_key_exists('resultats', $offres_api)): ?>
                    <p>Nombre de résultats: <?= count($offres_api['resultats']) ?></p>
                <?php endif; ?>
                <p>Vérifiez les fichiers api_debug.log et api_error.log pour plus d'informations</p>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>