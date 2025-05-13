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
    <title>Gestion des utilisateurs - Plateforme de recrutement intelligente</title>
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
            <h1 class="h3 mb-0 mx-3">Gestion des utilisateurs</h1>
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
                        <p class="mb-0">Gérer les utilisateurs de la plateforme</p>
                    </div>
                    <div class="col-md-3 text-end">
                        <a href="add_user.php" class="btn btn-dark">Ajouter un utilisateur</a>
                    </div>
                </div>
            </div>
        </section>

        <section id="gestion-utilisateurs" class="py-5">
            <div class="container">
                <h2 class="h1 text-center mb-5">Liste des utilisateurs</h2>
                
                <?php
                $query = "SELECT * FROM utilisateurs";
                $result = mysqli_query($conn, $query);
                $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
                ?>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Rôle</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id_utilisateur'] ?></td>
                                <td><?= htmlspecialchars($user['nom'] . ' ' . $user['prenom']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= $user['role'] ?: 'candidat' ?></td>
                                <td>
                                    <a href="delete_user.php?id=<?= $user['id_utilisateur'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer</a>
                                    <?php if (isset($user['is_blocked']) && $user['is_blocked'] == 1): ?>
                                        <a href="unblock_user.php?id=<?= $user['id_utilisateur'] ?>" class="btn btn-sm btn-success" onclick="return confirm('Voulez-vous vraiment débloquer cet utilisateur ?')">Débloquer</a>
                                    <?php else: ?>
                                        <a href="block_user.php?id=<?= $user['id_utilisateur'] ?>" class="btn btn-sm btn-warning" onclick="return confirm('Voulez-vous vraiment bloquer cet utilisateur ?')">Bloquer</a>
                                    <?php endif; ?>
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