<?php
session_start();
if(isset($_POST['id']) && isset($_POST['password']))
{
    // connexion à la base de données
    $db_username = 'root';
    $db_password = '';
    $db_name     = 'fiche_securite';
    $db_host     = 'localhost';
    $db = mysqli_connect($db_host, $db_username, $db_password,$db_name)
    or die('could not connect to database');

    // on applique les deux fonctions mysqli_real_escape_string et htmlspecialchars
    // pour éliminer toute attaque de type injection SQL et XSS
    $id = mysqli_real_escape_string($db,htmlspecialchars($_POST['id']));
    $password = mysqli_real_escape_string($db,htmlspecialchars($_POST['password']));

    if($id !== "" && $password !== "")
    {
        $requete = "SELECT count(*) 
                    FROM utilisateur 
                    WHERE id = '".$id."' 
                    AND mot_de_passe = '".$password."' ";
        $exec_requete = mysqli_query($db,$requete);
        $reponse      = mysqli_fetch_array($exec_requete);
        $count = $reponse['count(*)'];
        if($count!=0) // nom d'utilisateur et mot de passe corrects
        {
            $requete2 = "SELECT Type,Nom,Prenom,id 
                        FROM utilisateur 
                        WHERE id = '".$id."' 
                        AND mot_de_passe = '".$password."' ";
            $exec_requete = mysqli_query($db,$requete2);
            $reponse      = mysqli_fetch_array($exec_requete);

            $_SESSION['type']        = $reponse['Type'];
            $_SESSION['id']          = $reponse['id'];
            $_SESSION['prenom']      = $reponse['Prenom'];
            $_SESSION['nom']         = $reponse['Nom'];

            $_SESSION['userbd']      ='root';
            $_SESSION['passwordbd']  ='';
            $_SESSION['db_name']     ='fiche_securite';
            $_SESSION['db_host']     ='localhost';

            if($_SESSION['type']==="AMS") {
                header("Location: AMS.php?msg=0");
            }else{
                header("Location: GS.php");
            }
        }
        else
        {
            header('Location: index.php?err=1'); // utilisateur ou mot de passe incorrect
        }
    }
    else
    {
        header('Location: index.php?err=2'); // utilisateur ou mot de passe vide
    }
}
else
{
    header('Location: index.php?err=3');
}
mysqli_close($db); // fermer la connexion
?>
