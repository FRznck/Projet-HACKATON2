<?
$conn = mysqli_connect("localhost", "root", "root", "hackathon");
if (!$conn) {
    die("Erreur de connexion : " . mysqli_connect_error());
}
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Réinitialisation du mot de passe</h1>

        <form action="reset_password.php" method="post">
            <div id="forgot-password-form">
            <label for="email">Adresse email :</label>
            <input type="email" id="reset-email" name="email" placeholder="Votre email" required>
            <button type="submit" id="send-reset-email">Réinitialiser le mot de passe</button>
            </div>
        </form>

        <div class="back-link">
            <a href="connexion.php">Retour à la connexion</a>
        </div>
    </div>
</body>
</html>





