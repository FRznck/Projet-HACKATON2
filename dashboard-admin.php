<?php
session_start();
$conn = mysqli_connect("localhost", "root", "root", "hackathon");
if (!$conn) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

if (!isset($_SESSION['email'])) {
    header("Location: connexion.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tableau de bord Admin - Plateforme de recrutement intelligente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="dashboard-admin-CSS/styles-admin.css">
    <style>
   
    </style>
    <!-- Ajout de Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <nav class="navbar navbar-expand-lg fixed-top bg-light shadow-sm">
    <div class="container">
        <div class="rounded-circle bg-opacity-10 bg-dark" style="width: 40px; height: 40px"></div>
        <h1 class="h3 mb-0 mx-3">Tableau de bord Admin</h1>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a href="admin/liste-offres.php" class="btn btn-outline-dark" tabindex="-1" >Gestion offres</a></li>
                <li class="nav-item"><a href="admin/liste-user.php" class="btn btn-outline-dark">Gestion utilisateurs</a></li>
                <li class="nav-item"><a href="#" class="btn btn-outline-dark disabled" tabindex="-1" aria-disabled="true">Gestion recruteurs</a></li>
                <li class="nav-item"><a href="#" class="btn btn-outline-dark disabled" aria-disabled="true">Signalements</a></li>
                <li class="nav-item"><a href="../frontend2/deconnexion.php" class="btn btn-outline-dark">Déconnexion</a></li>
            </ul>
        </div>
    </div>
</nav>


    <main class="container-fluid" style="margin-top: 80px;">
        <section class="custom-section">
            <div class="container text-center">
                <h2 class="display-4 mb-4">Super <strong>admin</strong></h2>
                <p class="fs-5 mb-4">Attentif au moindre détails !</p>
                
            </div>
        </section>


        <section class="py-5">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-md-3 text-center">
                        <div class="avatar-admin rounded-circle mx-auto"></div>
                    </div>
                    <div class="col-md-6">
                        <h3 class="h2 mb-3">Admin</h3>
                        <span class="badge bg-secondary mb-3">Super Admin</span>
                        <p class="mb-0">Gérer les utilisateurs et les offres d'emploi</p>
                    </div>
                    <div class="col-md-3 text-end">
                        <button class="btn btn-dark" disabled>Ajouter un utilisateur</button>
                    </div>
                </div>
            </div>
        </section>

       
        

        

        <!-- Statistiques -->
        <section class="py-5">
            <div class="container">
                <h2 class="h1 text-center mb-5">Performance du recrutement</h2>
                <!-- Graphiques dynamiques -->
                <div class="row mb-5">
                    <div class="col-md-6">
                        <canvas id="inscriptionsChart"></canvas>
                    </div>
                    <div class="col-md-6">
                        <canvas id="offresChart"></canvas>
                    </div>
                </div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="metric-card">
                            <h4>Nombre total d'utilisateurs</h4>
                            <p class="display-6">45</p>
                            <small>+3</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="metric-card">
                            <h4>Offres d'emploi actives</h4>
                            <p class="display-6">25</p>
                            <small>-1</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="metric-card">
                            <h4>Fonctionnalités premium</h4>
                            <p class="display-6">7</p>
                            <small>+2</small>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-light py-5">
        <div class="container text-center">
            <p class="mb-0">Plateforme de recrutement intelligente | © 2021 Tous droits réservés</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Script pour les graphiques dynamiques -->
    <script>
    // Ces données sont des exemples, il faudra les remplacer par des données PHP dynamiques
    const inscriptionsData = {
        labels: ["Jan", "Fév", "Mar", "Avr", "Mai", "Juin"],
        datasets: [{
            label: "Inscriptions",
            data: [12, 19, 3, 5, 2, 3],
            borderColor: "rgba(54, 162, 235, 1)",
            backgroundColor: "rgba(54, 162, 235, 0.2)",
            tension: 0.4
        }]
    };
    const offresData = {
        labels: ["Jan", "Fév", "Mar", "Avr", "Mai", "Juin"],
        datasets: [{
            label: "Offres publiées",
            data: [2, 9, 13, 15, 12, 8],
            backgroundColor: "rgba(255, 99, 132, 0.5)"
        }]
    };
    new Chart(document.getElementById('inscriptionsChart'), {
        type: 'line',
        data: inscriptionsData
    });
    new Chart(document.getElementById('offresChart'), {
        type: 'bar',
        data: offresData
    });
    </script>
</body>
</html>
