<?php
session_start();

// Vérifier si l'utilisateur est connecté
//if (!isset($_SESSION['user'])) {
   // header("Location: connexion.php");
   // exit();
//}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement - Plateforme de Recrutement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index-CSS/styles.css">
</head>
<body>

    <!-- Navbar (optionnel) -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Plateforme de Recrutement</a>
            <div class="d-flex">
                <?php if (isset($_SESSION['user'])): ?>
                    <a class="btn btn-outline-dark" href="deconnexion.php">Se Déconnecter</a>
                <?php else: ?>
                    <a class="btn btn-outline-dark" href="connexion.php">Se connecter</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <h1 class="text-center mb-4">Souscrire à l'Abonnement Premium</h1>
        <p class="lead text-center">Débloquez toutes les fonctionnalités exclusives de notre plateforme de recrutement. Passez à la version premium pour un accès illimité aux fonctionnalités avancées.</p>

        <!-- Section avec détails de l'abonnement -->
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="premium-card border rounded shadow-lg overflow-hidden">
                    <div class="p-4 bg-light text-dark">
                        <h3 class="mb-3">Abonnement Premium</h3>
                        <div class="badge bg-primary mb-3 fs-6">À partir de 9,99 €/mois</div>
                        <p class="text-muted mb-3">Débloquez toutes les fonctionnalités : visibilité accrue, messagerie illimitée, gestion avancée des candidatures, et plus encore.</p>
                        <div class="fs-1 mb-3">💎</div>

                        <!-- Formulaire de paiement -->
                        <form action="traitement-paiement.php" method="POST">
                            <div class="form-group">
                                <label for="card-number">Numéro de carte</label>
                                <input type="text" class="form-control" id="card-number" name="card-number" placeholder="Entrez le numéro de votre carte" required>
                            </div>
                            <div class="form-group mt-3">
                                <label for="expiry-date">Date d'expiration</label>
                                <input type="text" class="form-control" id="expiry-date" name="expiry-date" placeholder="MM/AAAA" required>
                            </div>
                            <div class="form-group mt-3">
                                <label for="cvv">Code de sécurité (CVV)</label>
                                <input type="text" class="form-control" id="cvv" name="cvv" placeholder="Entrez le code CVV" required>
                            </div>
                            <button type="submit" class="btn btn-success btn-lg w-100 mt-4">Confirmer le paiement</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-dark text-white text-center py-4">
        <p>&copy; 2025 Plateforme de Recrutement - Tous droits réservés</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
