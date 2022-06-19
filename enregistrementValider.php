<html>
<head>
    <meta charset="utf-8">
    <!-- importer le fichier de style -->
    <link rel="stylesheet" href="" media="screen" type="text/css" />
</head>
<div id="content">
    <?php
    session_start();

    // connexion à la base de données
    $db_username = $_SESSION['userbd'];
    $db_password = $_SESSION['passwordbd'];
    $db_name     = $_SESSION['db_name'];
    $db_host     = $_SESSION['db_host'];
    $db = mysqli_connect($db_host, $db_username, $db_password, $db_name)
    or die('could not connect to database');

    $id_montee = $_GET['idm'];
    $id = $_SESSION['id'];
    $type = $_SESSION['type'];
    if ($id == null ) {
        header("Location: index.php?msg=3");
    }else {
        //modifie la ligne dans ficheentete correspondant à la fiche qui vient d'être fini.
        $sql = "update ficheEnTete 
                SET Heure_fin=CURTIME(),Validation=1 
                WHERE id_montee=$id_montee 
                  AND id_utilisateur=$id 
                  AND (Heure_fin is null)";
        if (mysqli_query($db, $sql)) {
            header('Location: AMS.php?msg=2');
        } else {
            echo "Erreur : " . $sql . "<br>" . mysqli_error($db);
        }

    }
    ?>
</div>
</html><?php
