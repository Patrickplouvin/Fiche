<html>
<head>
    <meta charset="utf-8">
    <!-- importer le fichier de style -->
    <link rel="stylesheet" href="css/ficheEnTete.css" media="screen" type="text/css" />
</head>
<div id="content">
<?php
session_start();

$id=$_SESSION['id'];
if ($id == null) {
    header("Location: index.php?id=$id");
}
// connexion à la base de données
$db_username = $_SESSION['userbd'];
$db_password = $_SESSION['passwordbd'];
$db_name     = $_SESSION['db_name'];
$db_host     = $_SESSION['db_host'];
$db = mysqli_connect($db_host, $db_username, $db_password,$db_name)
or die('could not connect to database');

// Recherche de toutes les montées associées à notre AMS
$requete      = "SELECT id,Adresse1,Commune,Quartier 
                FROM montee 
                where id_utilisateur=$id";
$exec_requete = mysqli_query($db,$requete);
$montees      = mysqli_fetch_all($exec_requete);

// Initialisation de la date
$mois=date('m');
$annee=date('Y');

foreach ($montees as $montee){
    // Requete pour savoir si la fiche a déjà été faite
    $requete = "SELECT count(*)
                FROM ficheentete
                WHERE id_montee=$montee[0]
                AND Date between '$annee-$mois-01' AND '$annee-$mois-31'
                AND Heure_fin is not null";
    $exec_requete = mysqli_query($db,$requete);
    $reponse = mysqli_fetch_array($exec_requete);
    $cpt = $reponse['count(*)'];

    if($cpt>0) {
        echo("<form action='creationFicheEnTete.php?idm=$montee[0]' method='POST'>
            <label>
                <img src='asset/circle-green.png' alt='' width='15px'>
                <input type='submit' id='identifiant3' value='$montee[0], $montee[1]'>
            </label>
          </form><br>");
    }else{
        //requete pour savoir si la fiche n'a pas été fini
        $requete = "SELECT count(*)
                FROM ficheentete
                WHERE id_montee=$montee[0]
                AND Date between '$annee-$mois-01' AND '$annee-$mois-31'
                AND Heure_fin is null";
        $exec_requete = mysqli_query($db,$requete);
        $reponse = mysqli_fetch_array($exec_requete);
        $cpt = $reponse['count(*)'];
        if($cpt>0){
            echo("<form action='creationFicheEnTete.php?idm=$montee[0]' method='POST'>
            <label>
                <img src='asset/circle-orange.png' alt='' width='15px'>
                <input type='submit' id='identifiant3' value='$montee[0], $montee[1]'>
                </label>
          </form><br>");
        }else {
            echo("<form action='creationFicheEnTete.php?idm=$montee[0]' method='POST'>
            <label>
                <img src='asset/circle-red.png' alt='' width='15px'>
                <input type='submit' id='identifiant3' value='$montee[0], $montee[1]'>
                </label>
          </form><br>");
        }
    }
}
?>
    <FORM class="decoo">
        <input class="deco" type='submit' id='submit' value='Retour' FORMACTION='AMS.php?msg=0' formmethod='post' width='20px'>
    </FORM>
</div>
</html>



