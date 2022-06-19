<?php
if(isset($_POST['password'])) {
    session_start();
    $id = $_SESSION['id'];
    $type=$_SESSION['userType'];
    if ($id == null) {
        header("Location: index.php?id=$id");
    }

    // connexion à la base de données
    $db_username = $_SESSION['userbd'];
    $db_password = $_SESSION['passwordbd'];
    $db_name     = $_SESSION['db_name'];
    $db_host     = $_SESSION['db_host'];
    $db = mysqli_connect($db_host, $db_username, $db_password, $db_name)
    or die('could not connect to database');

    // on applique les deux fonctions mysqli_real_escape_string et htmlspecialchars
    // pour éliminer toute attaque de type injection SQL et XSS
    $password = mysqli_real_escape_string($db, htmlspecialchars($_POST['password']));
    if ($password !== "" && $password !== "0000" && $password !== "1111" && $password !== "2222" && $password !== "3333" && $password !== "4444" && $password !== "5555" && $password !== "6666" && $password !== "7777" && $password !== "8888" && $password !== "9999" && $password !== "1234") {
        $sql = "update utilisateur 
                set Mot_De_Passe=$password,Premiere_connexion=1 
                where id=$id";
        if (mysqli_query($db, $sql)) {
            echo "Nouveau enregistrement créé avec succès <br>";
            header('Location: index.php');
        } else {
            echo "Erreur : " . $sql . "<br>" . mysqli_error($db);
        }
    }
    elseif($type==="GS") {
        header('Location: GS.php?err=1');
    }else{
        header('Location: AMS.php?err=1');
    }
}