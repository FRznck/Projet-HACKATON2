<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Connexion à la base de données avec MySQLi
$id = mysqli_connect("localhost", "root", "root", "hackathon");
if (!$id) {
  die("Erreur de connexion : " . mysqli_connect_error());
}

if (isset($_SESSION['email'])) {
  $email = $_SESSION['email'];
  $query = "SELECT avatar FROM utilisateurs WHERE email = ?";
  $stmt = mysqli_prepare($id, $query);
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

// Recherche
$recherche = isset($_GET['q']) ? trim(htmlspecialchars($_GET['q'])) : '';

// Construction de la requête de base
$sql = "SELECT * FROM offres";
$conditions = [];
$params = [];

if (!empty($recherche)) {
  $terms = explode(' ', $recherche);
  $searchConditions = [];
  foreach ($terms as $term) {
    $term = trim($term);
    if (!empty($term)) {
      $searchConditions[] = "(titre LIKE ? OR description LIKE ? OR competences_requises LIKE ? OR type_contrat LIKE ? OR lieu LIKE ?)";
      $searchTerm = '%' . $term . '%';
      // Add five parameters for each term (one for each field)
      $params = array_merge($params, array_fill(0, 5, $searchTerm));
    }
  }
  if (!empty($searchConditions)) {
    $conditions[] = "(" . implode(" OR ", $searchConditions) . ")";
  }
}

// Ajout des conditions si elles existent
if (!empty($conditions)) {
  $sql .= " WHERE " . implode(" AND ", $conditions);
}

// Préparation et exécution de la requête avec MySQLi
$stmt = mysqli_prepare($id, $sql);
if ($stmt === false) {
  die("Erreur de préparation de la requête: " . mysqli_error($id));
}

// Liaison des paramètres si nécessaire
if (!empty($params)) {
  $types = str_repeat('s', count($params)); // 's' pour string
  mysqli_stmt_bind_param($stmt, $types, ...$params);
}

// Exécution de la requête
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Récupération des offres
$offres = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Exécutez la requête SQL pour récupérer les témoignages
$result_temoignages = mysqli_query($id, "SELECT * FROM temoignages");

// Vérifiez si la requête a échoué
if (!$result_temoignages) {
  die('Erreur de requête : ' . mysqli_error($id));
}

// 1. Identifiants API France Travail
$client_id = "PAR_projethackathon_0a222e2d7071024ed5712a7e2ddd26e97a01b802673caaaae84e098423458e18";
$client_secret = "7ca2d76bb144f9193ba85e025bb487b6f95f104d333938cc10cb2a6277a36ad1";

// 2. Fonction pour récupérer le token avec debug
function getAccessToken($client_id, $client_secret)
{
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
    file_put_contents('api_error.log', "Erreur token: " . $error . PHP_EOL, FILE_APPEND);
    return null;
  }

  $res = json_decode($response, true);
  return $res['access_token'] ?? null;
}

// 3. Fonction pour récupérer les offres avec debug
function getOffresFranceTravail($token)
{
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
    file_put_contents('api_error.log', "Erreur API: " . $error . PHP_EOL, FILE_APPEND);
    return ['resultats' => []];
  }

  return json_decode($response, true) ?? ['resultats' => []];
}


$token = getAccessToken($client_id, $client_secret);
file_put_contents('api_debug.log', "Token: " . print_r($token, true) . PHP_EOL, FILE_APPEND);

if ($token) {
  $offres_api = getOffresFranceTravail($token);
  file_put_contents('api_debug.log', "Réponse API: " . print_r($offres_api, true) . PHP_EOL, FILE_APPEND);
} else {
  $offres_api = ['resultats' => []];
  file_put_contents('api_error.log', "Échec d'obtention du token" . PHP_EOL, FILE_APPEND);
}



mysqli_stmt_close($stmt);

?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Plateforme de Recrutement Intelligente</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <link rel="stylesheet" href="accueil-CSS/styles-accueil.css" />
  <link rel="stylesheet" href="chatbot-CSS/chatbot-styles.css" />
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC3l_Df3HNP8LWJRcfymEO_mSez5NS1QeM&libraries=places"></script>
  <script src="js/maps.js"></script>



</head>


<body>

  <style>
    /* Style général de la navbar */
    .navbar {
      padding: 0.8rem 1rem;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .navbar-brand {
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .navbar-brand:hover {
      opacity: 0.8;
    }

    /* Photo de profil */
    .bg-secondary.bg-opacity-10 {
      transition: transform 0.3s ease;
    }


    .bg-secondary.bg-opacity-10:hover {
      transform: scale(1.05);
    }

    /* Liens de navigation */
    .nav-link {
      font-weight: 500;
      padding: 0.5rem 1rem;
      margin: 0 0.2rem;
      border-radius: 4px;
      transition: all 0.3s ease;
      color: #495057 !important;
    }

    .nav-link:hover {
      background-color: #f8f9fa;
      color: #3498db !important;
    }

    /* Champ de recherche */
    .form-control {
      border-radius: 4px;
      border: 1.5px solidrgb(151, 198, 245);
      min-width: 200px;
    }

    .btn-outline-primary {
      border-radius: 6px;
      padding: 0.375rem 1rem;
    }

    .btn-link.nav-link {
      color: rgb(222, 44, 44) !important;
      padding: 0.3rem 0.75rem;
      margin-left: 0.5rem;
      font-size: 0.875rem;
      line-height: 1.2;
      transition: all 0.3s ease;
      position: relative;
      border-radius: 6px;
      border: 1.5px solid rgb(222, 44, 44);
      background-color: transparent;
      width: 100px fit-content;
      min-width: 90px;
      text-align: center;
    }

    .btn-link.nav-link:hover {
      color: #dc3545 !important;
      background-color: rgba(220, 53, 69, 0.1);
    }

    .btn-link.nav-link:active,
    .btn-link.nav-link:focus {
      color: #dc3545 !important;
      background-color: rgba(220, 53, 69, 0.2);
    }

    /* Animation pour le bouton de déconnexion */
    .btn-link.nav-link::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      width: 0;
      height: 2px;
      background-color: #dc3545;
      transition: all 0.3s ease;
      transform: translateX(-50%);
    }

    .btn-link.nav-link:hover::after {
      width: 80%;
    }

    /* Texte de bienvenue */
    .navbar-text span {
      font-weight: 500;
      color: #495057;
      padding: 0.5rem;
    }

    /* Responsive */
    @media (max-width: 991.98px) {
      .navbar-collapse {
        padding-top: 1rem;
      }

      .nav-link,
      .btn-link.nav-link {
        margin: 0.2rem 0;
        padding: 0.5rem 1rem;
      }


      .form-control {
        margin: 0.5rem 0;
        width: 100%;
      }

      .btn-outline-primary {
        width: 100%;
        margin: 0.5rem 0;
      }
    }

    /* Style spécifique pour la barre de recherche */
    .navbar-search-container {
      min-width: 250px;
      /* Largeur réduite */
      max-width: 350px;
      /* Largeur maximale */
      margin: 0 1.5rem;
      /* Espacement latéral */
    }

    .navbar-search-input {
      border-radius: 20px !important;
      /* Bords arrondis */
      padding: 0.35rem 1rem !important;
      /* Padding réduit */
      font-size: 0.9rem;
      /* Taille de police légèrement réduite */
      height: 38px;
      /* Hauteur fixe */
    }

    .navbar-search-btn {
      border-radius: 20px !important;
      padding: 0 1rem !important;
      height: 38px;
      /* Même hauteur que l'input */
      margin-left: 0.5rem !important;
    }

    /* Version mobile */
    @media (max-width: 991.98px) {
      .navbar-search-container {
        width: 100%;
        max-width: 100%;
        margin: 0.5rem 0 !important;
      }

      .navbar-search-input {
        flex-grow: 1;
      }
    }
  </style>

  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="parametres-candidat.php">
        <div class="bg-secondary bg-opacity-10 rounded-circle me-3" style="width: 40px; height: 40px; overflow: hidden;">
          <img src="avatars/<?php echo htmlspecialchars($photoProfil); ?>" alt="Photo de profil" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        <span class="h4 mb-0">Plateforme de Recrutement Intelligente</span>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
        aria-controls="mainNavbar" aria-expanded="false" aria-label="Basculer la navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="navbar-text">
        <?php
        if (isset($_SESSION['email'])) {
          $conn = mysqli_connect("localhost", "root", "root", "hackathon");
          if (!$conn) {
            die("Erreur de connexion : " . mysqli_connect_error());
          }

          $email = mysqli_real_escape_string($conn, $_SESSION['email']);
          $query = "SELECT nom FROM utilisateurs WHERE email = '$email'";
          $result = mysqli_query($conn, $query);

          if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            $nom = htmlspecialchars($user['nom']);
            echo "<span class='fw-normal'>Bonjour , $nom !</span>";
          } else {
            echo "<span class='fw-normal'>Bonjour , " . htmlspecialchars($_SESSION['email']) . "</span>";
          }

          mysqli_close($conn);
        } else {
          echo "<span class='fw-normal'>Bienvenue, invité</span>";
        }
        ?>
      </div>

      <div class="collapse navbar-collapse" id="mainNavbar">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link" href="accueil.php"><i class="fas fa-home me-1"></i> Accueil</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../frontend2/ia/Page-ai.php"><i class="fas fa-robot me-1"></i> Découvrez l'IA</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="dashboard-candidat.php"><i class="fas fa-chart-line me-1"></i> Mes suivis</a>
          </li>
          




        </ul>

        
        <form class="d-flex navbar-search-container" method="GET" action="accueil.php">
          <input class="form-control navbar-search-input" type="search" name="q"
            placeholder="Recherche..."
            aria-label="Search"
            value="<?= htmlspecialchars($recherche) ?>">
          <button class="btn btn-outline-primary navbar-search-btn" type="submit">
            <i class="fas fa-search"></i>
          </button>
        </form>

        <form method="POST" action="deconnexion.php" class="d-inline">
          <button type="submit" class="btn btn-link nav-link" style="text-decoration: none;">
            <i class="fas fa-sign-out-alt me-1"></i> Déconnexion
          </button>
        </form>
        
      </div>
    </div>
  </nav>
  <main class="mt-5 pt-5">
    <section class="custom-hero text-white py-5">
      <div class=container text-center py-5">
        <h1 class="display-4 mb-4 fw-bold">Trouvez l'emploi idéal en un clic!</h1>
        <div class="row justify-content-center">
          <div class="col-md-8 col-lg-6">
            <form method="GET" action="accueil.php">
              <div class="input-group mb-3">
                <input
                  type="text"
                  class="form-control"
                  name="q"
                  placeholder="Recherchez par mots-clés, type de contrat"
                  aria-label="Recherche d'emploi"
                  value="<?= htmlspecialchars($recherche) ?>">
                <button class="btn btn-secondary" type="submit">Rechercher</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>

    <!-- Offres d'emploi -->
    <style>
      .job-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        background: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        height: 100%;
      }

      .job-card h5 {
        font-size: 20px;
        margin-bottom: 10px;
        color: #333;
      }

      .job-card p {
        font-size: 14px;
        color: #555;
        margin-bottom: 8px;
      }

      .job-card .btn {
        margin-top: 10px;
      }
    </style>

    <!-- Offres d'emploi -->
    <section class="container mt-5">
      <h2 class="text-center mb-4">Offres d'emplois interne</h2>
      <div class="row row-cols-1 row-cols-md-3 g-4">

        <?php if (count($offres) > 0): ?>
          <?php foreach ($offres as $offre): ?>
            <div class="col">
              <div class="card h-100 shadow-sm">
                <div class="card-body">
                  <h5 class="card-title"><i class="fas fa-briefcase me-2"></i><?= htmlspecialchars($offre['titre']) ?></h5>
                  <p class="card-text"><i class="fas fa-align-left me-2"></i><?= htmlspecialchars($offre['description']) ?></p>

                  <div class="mb-3">
                    <div class="d-flex align-items-center mb-2">
                      <i class="fas fa-map-marker-alt me-2 text-muted"></i>
                      <span><strong>Lieu :</strong> <?= htmlspecialchars($offre['lieu']) ?></span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                      <i class="fas fa-file-contract me-2 text-muted"></i>
                      <span><strong>Type :</strong> <?= htmlspecialchars($offre['type_contrat']) ?></span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                      <i class="far fa-calendar-alt me-2 text-muted"></i>
                      <span><strong>Date :</strong> <?= htmlspecialchars($offre['date_publication']) ?></span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                      <i class="fas fa-tools me-2 text-muted"></i>
                      <span><strong>Compétences :</strong> <?= htmlspecialchars($offre['competences_requises']) ?></span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                      <i class="fas fa-building me-2 text-muted"></i>
                      <span><strong>Entreprise :</strong> <?= isset($offre['nom_entreprise']) ? htmlspecialchars($offre['nom_entreprise']) : 'Entreprise non renseignée' ?></span>
                    </div>
                    <div class="d-flex align-items-center">
                      <i class="fas fa-euro-sign me-2 text-muted"></i>
                      <span><strong>Salaire :</strong> <?= htmlspecialchars($offre['salaire']) ?> €</span>
                    </div>
                  </div>

                  <div class="text-center mt-3">
                    <a href="details_offre.php?id=<?= $offre['id_offre'] ?>" class="btn btn-primary">
                      <i class="far fa-eye me-1"></i> Voir l'offre
                    </a>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-12">
            <div class="alert alert-info text-center" role="alert">
              <i class="fas fa-info-circle me-2"></i>Aucune offre trouvée pour cette recherche.
            </div>
          </div>
        <?php endif; ?>
      </div>

      <div class="text-center mt-5">
        <img src="https://c.animaapp.com/m8wq0iljLnvmI1/img/vector-200.svg" alt="Décoration" class="img-fluid">
      </div>
    </section>




    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    

    <section class="custom-section bg-light">
      <div class="container py-5">
        <h1 class="text-center mb-5"><i class="fas fa-briefcase me-2"></i>Offres d'emploi Externe</h1>

        <?php if (!empty($offres_api['resultats'])): ?>
          <div class="row" id="jobList">
            <?php foreach ($offres_api['resultats'] as $index => $offre): ?>
              <div class="col-md-4 <?= $index >= 3 ? 'extra-offer d-none' : '' ?>">
                <div class="job-card">
                  <h3><i class="fas fa-file-alt me-2"></i><?= htmlspecialchars($offre['intitule'] ?? 'Offre sans titre') ?></h3>
                  <div class="job-info-item">
                    <i class="fas fa-align-left"></i>
                    <p><?= htmlspecialchars(substr($offre['description'] ?? 'Description non disponible', 0, 100)) ?>...</p>
                  </div>
                  <div class="job-info-item">
                    <i class="fas fa-file-contract"></i>
                    <p><strong>Type:</strong> <?= htmlspecialchars($offre['typeContratLibelle'] ?? 'CDD') ?></p>
                  </div>
                  <div class="job-info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <p><strong>Lieu:</strong> <?= htmlspecialchars($offre['lieuTravail']['libelle'] ?? 'Paris') ?></p>
                  </div>
                  <div class="job-info-item">
                    <i class="fas fa-building"></i>
                    <p><strong>Entreprise:</strong> <?= htmlspecialchars($offre['entreprise']['nom'] ?? 'Non communiqué') ?></p>
                  </div>
                  <a href="<?= htmlspecialchars($offre['origineOffre']['urlOrigine'] ?? '#') ?>" class="btn btn-primary">
                    <i class="fas fa-external-link-alt me-1"></i> Voir l'offre
                  </a>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <?php if (count($offres_api['resultats']) > 3): ?>
            <div class="text-center mt-4">
              <a href="offres_france_travail.php" class="btn btn-secondary">
                <i class="fas fa-plus-circle me-1"></i> Voir plus
              </a>
            </div>
          <?php endif; ?>
        <?php else: ?>
          <div class="alert alert-warning">
            <h4><i class="fas fa-exclamation-triangle me-2"></i>Aucune offre disponible actuellement</h4>
            <p>Veuillez réessayer plus tard ou vérifier les paramètres de connexion à l'API.</p>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <div class="text-center mt-5">
      <img src="https://c.animaapp.com/m8wq0iljLnvmI1/img/vector-200.svg" alt="Décoration" class="img-fluid">
    </div>




    <section class="chatbot">

      <!-- Chatbot Widget -->
      <div id="chatbot-container" style="position: fixed; bottom: 30px; right: 30px; z-index: 9999;">
        <div id="chatbot-box" style="width: 350px; height: 450px; background: #fff; border-radius: 12px; box-shadow: 0 2px 16px rgba(0,0,0,0.2); display: flex; flex-direction: column; overflow: hidden;">
          <div style="background: #0d6efd; color: #fff; padding: 12px; font-weight: bold; display: flex; justify-content: space-between; align-items: center;">
            <span>🤖 Chatbot </span>
            <button id="chatbot-close" style="background: none; border: none; color: #fff; font-size: 18px; cursor: pointer;">&times;</button>
          </div>
          <div id="chatbot-messages" style="flex: 1; padding: 12px; overflow-y: auto; font-size: 15px; background: #f9f9f9;"></div>
          <form id="chatbot-form" style="display: flex; border-top: 1px solid #eee;">
            <input id="chatbot-input" type="text" class="form-control" placeholder="Posez votre question..." autocomplete="off" style="border: none; border-radius: 0; flex: 1;">
            <button type="submit" class="btn btn-primary" style="border-radius: 0;">Envoyer</button>
          </form>
        </div>
      </div>


    </section>
  </main>

  <footer class="bg-white py-4">
    <div class="container">
      <div class="row text-center">
        <div class="col">
          <a href="#" class="d-block mb-2">À propos</a>
        </div>
        <div class="col">
          <a href="#" class="d-block mb-2">Contact</a>
        </div>
        <div class="col">
          <a href="#" class="d-block mb-2">FAQ</a>
        </div>
        <div class="col">
          <a href="#" class="d-block mb-2">Mentions légales</a>
        </div>
        <div class="col">
          <a href="#" class="d-block mb-2">Politique de confidentialité</a>
        </div>
      </div>
    </div>
    <!-- Chatbot Widget -->
    <div id="chatbot-container">
      <div id="chatbot-box">
        <div id="chatbot-header">
          <span>🤖 Chatbot</span>
          <button id="chatbot-close">&times;</button>
        </div>
        <div id="chatbot-messages"></div>
        <form id="chatbot-form">
          <input id="chatbot-input" type="text" placeholder="Posez votre question..." autocomplete="off" required>
          <button type="submit">Envoyer</button>
        </form>
      </div>
      <button id="chatbot-toggle">🤖</button>
    </div>


  </footer>
  <script src="../frontend/js/chatbot.js"></script>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


  <script>
    document.getElementById('loadGlassdoorJobs').addEventListener('click', function() {
      const container = document.getElementById('glassdoorJobsContainer');
      container.innerHTML = '<div class="col-12 text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';

      fetch(`get_glassdoor_jobs.php?company=${encodeURIComponent(entrepriseSelectionnee)}`)
        .then(response => response.json())
        .then(data => {
          if (data.error) {
            container.innerHTML = `<div class="col-12 text-center text-danger">${data.error}</div>`;
            return;
          }

          if (data.jobs && data.jobs.length > 0) {
            let html = '';
            data.jobs.forEach(job => {
              const logo = job.logo ?
                `<img src="${job.logo}" alt="${job.company}" class="img-fluid mb-2" style="max-height: 50px;">` :
                '';

              html += `
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-body">
                                ${logo}
                                <h5 class="card-title">${job.title}</h5>
                                <h6 class="card-subtitle mb-2 text-muted">${job.company}</h6>
                                <p class="card-text"><small>${job.location}</small></p>
                                <p class="card-text small text-truncate">${job.description.substring(0, 100)}...</p>
                                <a href="${job.url}" target="_blank" class="btn btn-sm btn-primary">Voir l'offre</a>
                            </div>
                        </div>
                    </div>`;
            });
            container.innerHTML = html;
          } else {
            container.innerHTML = '<div class="col-12 text-center"><p>Aucune offre trouvée</p></div>';
          }
        })
        .catch(error => {
          container.innerHTML = `<div class="col-12 text-center text-danger">Erreur: ${error.message}</div>`;
        });
    });
  </script>
  </script>


  <script src="../js/chatbot.js"></script>




</body>

</html>
<!-- SUPPRIMEZ TOUTES LES BALISES <style> ... </style> ICI -->