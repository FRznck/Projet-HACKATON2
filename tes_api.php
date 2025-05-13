<?php
/**
 * Intégration API France Travail - Offres d'emploi
 */
class FranceTravailAPI {
    const TOKEN_URL = 'https://entreprise.francetravail.fr/connexion/oauth2/access_token?realm=%2Fpartenaire';
    const API_URL = 'https://api.emploi-store.fr/partenaire/offresdemploi/v2/offres/search';
    
    private $client_id;
    private $client_secret;
    private $access_token;
    
    public function __construct(string $client_id, string $client_secret) {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->authenticate();
    }
    
    private function authenticate(): void {
        $ch = curl_init(self::TOKEN_URL);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'grant_type' => 'client_credentials',
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'scope' => 'api_offresdemploiv2 o2dsoffre'
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded'
            ]
        ]);
        
        $response = curl_exec($ch);
        $data = json_decode($response, true);
        
        if (isset($data['access_token'])) {
            $this->access_token = $data['access_token'];
        } else {
            throw new RuntimeException("Erreur d'authentification: " . ($data['error'] ?? 'Unknown error'));
        }
    }
    
    public function searchOffers(string $keyword, string $location, int $limit = 10): array {
        $params = [
            'motsCles' => $keyword,
            'lieux' => $location,
            'range' => "0-$limit",
            'communaute' => 'metiers-numerique' // Filtre pour les métiers du numérique
        ];
        
        $ch = curl_init(self::API_URL . '?' . http_build_query($params));
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->access_token,
                'Accept: application/json'
            ]
        ]);
        
        $response = curl_exec($ch);
        $data = json_decode($response, true);
        
        return $data['resultats'] ?? [];
    }
}

try {
    
    $api = new FranceTravailAPI(
        'PAR_projethackathon_0a222e2d7071024ed5712a7e2ddd26e97a01b802673caaaae84e098423458e18',
        '7ca2d76bb144f9193ba85e025bb487b6f95f104d333938cc10cb2a6277a36ad1'
    );
    
    // Recherche d'offres
    $offres = $api->searchOffers('développeur', 'Paris', 5);
    
} catch (Exception $e) {
    die("<div style='color:red;padding:20px;border:1px solid red;'>
        <h3>Erreur API France Travail</h3>
        <p>" . htmlspecialchars($e->getMessage()) . "</p>
        </div>");
}


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Projet Hackathon - Offres France Travail</title>
    <style>
        .offer-card {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .offer-title {
            color: #1e88e5;
            margin-top: 0;
        }
        .company {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Offres pour développeurs à Paris</h1>
    
    <?php if (!empty($offres)): ?>
        <?php foreach ($offres as $offre): ?>
            <div class="offer-card">
                <h2 class="offer-title"><?= htmlspecialchars($offre['intitule'] ?? '') ?></h2>
                <p class="company"><?= htmlspecialchars($offre['entreprise']['nom'] ?? '') ?></p>
                <p>📍 <?= htmlspecialchars($offre['lieuTravail']['libelle'] ?? '') ?></p>
                <p>Type: <?= htmlspecialchars($offre['typeContrat'] ?? '') ?></p>
                <a href="<?= htmlspecialchars($offre['origineOffre']['urlOrigine'] ?? '#') ?>" target="_blank">
                    Voir l'offre complète
                </a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucune offre disponible pour le moment.</p>
    <?php endif; ?>
</body>
</html>