<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$id = mysqli_connect("localhost", "root", "root", "hackathon");
if (!$id) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

// Ajout récupération de la photo de profil
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $query = "SELECT avatar FROM utilisateurs WHERE email = ?";
    $stmt = mysqli_prepare($id, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $resultAvatar = mysqli_stmt_get_result($stmt);

    if ($resultAvatar && mysqli_num_rows($resultAvatar) > 0) {
        $row = mysqli_fetch_assoc($resultAvatar);
        $photoProfil = !empty($row['avatar']) ? $row['avatar'] : 'default.png';
    } else {
        $photoProfil = 'default.png';
    }
    mysqli_stmt_close($stmt);
} else {
    $photoProfil = 'default.png';
}

if (!isset($_SESSION['email'])) {
    header("Location: ../connexion.php");
    exit();
}

// Recup l'ID du candidat
$email = $_SESSION['email'];
$query = "SELECT id_utilisateur FROM utilisateurs WHERE email = ?";
$stmt = $id->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$candidatId = $user['id_utilisateur'] ?? null;

$result = $id->query("SELECT titre FROM offres");
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page IA & Matching</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">

</head>

<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="../parametres-candidat.php">


      <div class="bg-secondary bg-opacity-10 rounded-circle me-3" style="width: 40px; height: 40px; overflow: hidden;">
                    <img src="../avatars/<?php echo htmlspecialchars($photoProfil); ?>" alt="Photo de profil" style="width: 100%; height: 100%; object-fit: cover;">
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
          $resultNom = mysqli_query($conn, $query);

          if ($resultNom && mysqli_num_rows($resultNom) > 0) {
            $user = mysqli_fetch_assoc($resultNom);
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
            <a class="nav-link" href="../accueil.php">Accueil</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../ia/Page-ai.php">Découvrez l'IA</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../dashboard-candidat.php">Mes suivis</a>
          </li>


        </ul>



       


        <form method="POST" action="../deconnexion.php" class="d-inline">
          <button type="submit" class="btn btn-link nav-link" style="text-decoration: none;">Déconnexion</button>
        </form>

      </div>
    </div>
  </nav>


    <main class="container-fluid p-0">
        <!-- Section Hero -->
        <section class="py-5 bg-white">
            <div class="container text-center py-5">
                <h2 class="display-5 fw-bold mb-4">AI & Matching Page</h2>
                <p class="lead mb-5">Interface de mise en relation intelligente entre offres et candidats.</p>
                <div class="vector-line"></div>
            </div>
        </section>

        <div class="container py-5">
            <h1 class="mb-4 text-center">Analyse intelligente de CV 📄🤖</h1>

            <form id="analyzeForm">
                <label for="cv">Téléchargez votre CV :</label>
                <input type="file" id="cv" name="cv" required>

                <label for="offre">Choisissez une offre :</label>
                <select id="offre" name="offre">
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['titre']}'>{$row['titre']}</option>";
                    }
                    ?>
                </select>

                <button type="submit" id="submit">Analyser le CV</button>
            </form>

            <div id="analyzeResults"></div>

        </div>

        <!-- Section Génération Lettre -->
        <section class="py-5 bg-white">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-md-6 order-md-2">
                        <div class="feature-image"></div>
                    </div>
                    <div class="col-md-6 order-md-1">
                        <h3 class="display-6 fw-bold mb-4">Générateur IA</h3>
                        <p class="fs-5 text-muted">Création de lettres de motivation personnalisées</p>

                        <form id="letterForm" class="custom-card border mt-4">
                            <div class="card-body">
                                <div class="row g-3 mb-3">
                                    <div class="col-12">
                                        <input type="text" class="form-control" placeholder="Nom complet" name="nom" required>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" placeholder="Poste" name="poste" required>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" placeholder="Entreprise" name="entreprise" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <textarea class="form-control" rows="3" name="motivations" placeholder="Motivations principales..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <span class="loading-spinner"></span>
                                    Générer la lettre
                                </button>
                            </div>
                        </form>
                        <div id="letterOutput" class="mt-4"></div>
                    </div>
                </div>
                <div class="vector-line my-5"></div>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Passer l'ID du candidat au JavaScript
        <?php if (isset($candidatId) && $candidatId): ?>
            window.candidatId = <?php echo json_encode($candidatId); ?>;
        <?php else: ?>
            window.candidatId = null;
        <?php endif; ?>
    </script>
    <script src="script.js"></script>
</body>
</html>