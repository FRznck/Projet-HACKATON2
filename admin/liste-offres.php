<?php
session_start();
$conn = mysqli_connect("localhost", "root", "root", "hackathon");
if (!$conn) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

if (!isset($_SESSION['email'])) {
    header("Location: ../connexion.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gestion des offres - Plateforme de recrutement intelligente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .avatar-admin {
            width: 120px;
            height: 120px;
            background-color: #f0f0f0;
            margin-bottom: 20px;
        }
        .metric-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg fixed-top bg-light shadow-sm">
        <div class="container">
            <div class="rounded-circle bg-opacity-10 bg-dark" style="width: 40px; height: 40px"></div>
            <h1 class="h3 mb-0 mx-3">Gestion des offres</h1>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a href="../dashboard-admin.php" class="btn btn-outline-dark">Retour au tableau</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container-fluid" style="margin-top: 80px;">
        <section class="py-5">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-md-3 text-center">
                        <div class="avatar-admin rounded-circle mx-auto"></div>
                    </div>
                    <div class="col-md-6">
                        <h3 class="h2 mb-3">Admin</h3>
                        <span class="badge bg-secondary mb-3">Super Admin</span>
                        <p class="mb-0">Gérer les offres de la plateforme</p>
                    </div>
                    <div class="col-md-3 text-end">
                        <a href="add_offre.php" class="btn btn-dark">Ajouter une offre</a>
                    </div>
                </div>
            </div>
        </section>

        <section id="gestion-offres" class="py-5">
            <div class="container">
                <h2 class="h1 text-center mb-5">Liste des offres</h2>
                
                <?php
                $query = "SELECT * FROM offres";
                $result = mysqli_query($conn, $query);
                $offres = mysqli_fetch_all($result, MYSQLI_ASSOC);
                ?>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Titre</th>
                                <th>Entreprise</th>
                                <th>Type contrat</th>
                                <th>Lieu</th>
                                <th>Salaire</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($offres as $offre): ?>
                            <tr>
                                <td><?= $offre['id_offre'] ?></td>
                                <td><?= htmlspecialchars($offre['titre']) ?></td>
                                <td><?= htmlspecialchars($offre['nom_entreprise'] ?? '') ?></td>
                                <td><?= htmlspecialchars($offre['type_contrat']) ?></td>
                                <td><?= htmlspecialchars($offre['lieu']) ?></td>
                                <td><?= htmlspecialchars($offre['salaire']) ?> €</td>
                                <td>
                                    
                                    <a href="delete_offre.php?id=<?= $offre['id_offre'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette offre ?')">Supprimer</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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
</body>
</html>