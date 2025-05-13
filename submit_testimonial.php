<?php

$id = mysqli_connect("localhost", "root", "root", "hackathon");
if (!$id) {
    die("Connection failed: " . mysqli_connect_error());
}

// Vérification si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titre = mysqli_real_escape_string($id, $_POST['titre']);
    $contenu = mysqli_real_escape_string($id, $_POST['contenu']);

    // Gestion du téléchargement de l'image
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);

    // S'assurer que l'image est bien téléchargée
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // Insertion dans la base de données
        $image_url = mysqli_real_escape_string($id, $target_file);
        $sql = "INSERT INTO temoignages (titre, contenu, image_url) VALUES ('$titre', '$contenu', '$image_url')";

        if ($id->query($sql) === TRUE) {
            echo "Nouveau témoignage ajouté avec succès!";
            
            echo "<img src='" . $target_file . "' alt='Image du témoignage' style='max-width: 800px; height: auto;'>";
            header("Location: accueil.php"); 
            exit; // Important !!! pour s'assurer que la redirection se fait après l'affichage
        }
    }
}


$id->close();
?>
