<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- importer le fichier de style -->
    <link rel="stylesheet" href="css/ficheQuestion.css" media="screen" type="text/css" />
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


$requete="SELECT ft.Date,ft.id_montee,ft.Heure_debut,m.Adresse1 
        FROM ficheentete ft 
            inner join montee m on m.id=ft.id_montee 
        where ft.id_utilisateur=$id 
        and ft.Heure_fin is null 
        ORDER BY ft.Heure_debut";
$exec_requete = mysqli_query($db,$requete);
$reponses      = mysqli_fetch_all($exec_requete);
echo ("<p id='try'> Voici vos fiches que vous avez à remplir: </p><br><br>");
foreach ($reponses as $reponse){
    echo("<form action='Questionnaire.php?idm=$reponse[1]' method='POST'>
            <label>
                <input id='identifiant4' type='submit' value='$reponse[0] $reponse[1] $reponse[2] $reponse[3]'>
            </label>
          </form><br>");
}
?>
    <FORM class="decoo">
        <input class="deco" type='submit' id='submit' value='Retour' FORMACTION='AMS.php?msg=0' formmethod='post' width='20px'>
    </FORM>
</div>
</html>


