<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connexion à la base de données
$conn = mysqli_connect("localhost", "root", "root", "hackathon");
if (!$conn) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

// Configuration des erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Liste des statuts autorisés (doit correspondre à votre ENUM en base)
$allowed_statuts = ['En attente', 'Acceptée', 'Refusée', 'Entretien planifié'];

// Vérification des données POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation des entrées
    $required_fields = ['id_utilisateur', 'id_offre', 'statut'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field])) {
            $_SESSION['error'] = "Champ manquant : $field";
            header("Location: dashboard-recruteur.php");
            exit();
        }
    }

    // Nettoyage des données
    $id_utilisateur = (int)$_POST['id_utilisateur'];
    $id_offre = (int)$_POST['id_offre'];
    $statut = trim($_POST['statut']);

    // Validation du statut
    if (!in_array($statut, $allowed_statuts)) {
        $_SESSION['error'] = "Statut invalide : '$statut'";
        header("Location: dashboard-recruteur.php");
        exit();
    }

    // Vérification de l'existence de la candidature
    $check_sql = "SELECT 1 FROM candidatures 
                 WHERE id_utilisateur = ? AND id_offre = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "ii", $id_utilisateur, $id_offre);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) === 0) {
        $_SESSION['error'] = "Candidature introuvable";
        header("Location: dashboard-recruteur.php");
        exit();
    }

    // Mise à jour avec requête préparée
    $update_sql = "UPDATE candidatures 
                  SET statut = ? 
                  WHERE id_utilisateur = ? AND id_offre = ?";
    $stmt = mysqli_prepare($conn, $update_sql);
    
    if (!$stmt) {
        $_SESSION['error'] = "Erreur de préparation : " . mysqli_error($conn);
        header("Location: dashboard-recruteur.php");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "sii", $statut, $id_utilisateur, $id_offre);
    $success = mysqli_stmt_execute($stmt);

    // Gestion du résultat
    if ($success && mysqli_stmt_affected_rows($stmt) > 0) {
        $_SESSION['success'] = "Statut mis à jour avec succès";

        $sql_o = "SELECT titre FROM offres WHERE id_offre = ?";
    $tstmt = mysqli_prepare($conn, $sql_o);
    mysqli_stmt_bind_param($tstmt, "i", $id_offre);
    mysqli_stmt_execute($tstmt);
    $result = mysqli_stmt_get_result($tstmt);
    $titre  = $result->fetch_assoc()['titre'] ?? 'votre offre';

    $msg = sprintf(
        "Le statut de votre candidature pour «%s» est désormais : %s",
        $titre,
        $statut
      );

      $sql_n = "INSERT INTO notifications (user_id, message) VALUES (?, ?)";
    $nstmt = mysqli_prepare($conn, $sql_n);
    mysqli_stmt_bind_param($nstmt, "is", $id_utilisateur, $msg);
    mysqli_stmt_execute($nstmt);
    mysqli_stmt_close($tstmt);
    mysqli_stmt_close($nstmt);
    } else {
        $_SESSION['error'] = "Échec de la mise à jour : " . mysqli_stmt_error($stmt);
    }

    // Nettoyage
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    // Redirection
    header("Location: dashboard-recruteur.php");
    exit();

} else {
    // Accès direct non autorisé
    $_SESSION['error'] = "Accès non autorisé";
    header("Location: dashboard-recruteur.php");
    exit();
}
?>