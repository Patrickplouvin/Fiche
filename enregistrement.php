<html>
<head>
    <meta charset="utf-8">
    <!-- importer le fichier de style -->
    <link rel="stylesheet" href="css/enregistrement.css" media="screen" type="text/css" />
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

//savoir si l'utilisateur est connecté
if ($id == null) {
    header("Location: index.php?id=$id");
}

//savoir quelle fiche entete nous allons modifier
$requete="SELECT id 
          FROM ficheentete 
          WHERE id_montee=$id_montee 
          and Heure_fin is null";
$exec_requete = mysqli_query($db,$requete);
$zoneac     = mysqli_fetch_array($exec_requete);
$idf=$zoneac['id'];

//récuperer toute les réponses considérer comme anomalie qui correspondent à la fiche
$requete="SELECT tz.nom_zone,tt.Libelle,fq.Etage,tq.Libelle,tr.Libelle,fq.id_zone ,fq.occ
          FROM fichequestion fq 
            inner join t_reponse tr on fq.id_reponse=tr.id 
              inner join t_zone tz on tz.id=fq.id_zone 
                inner join t_question tq on tq.id=fq.id_question 
                  inner join t_type tt on tt.id=tq.id_type 
          where fq.id_fiche=$idf 
          and tr.Anomalie=1
          ORDER BY fq.id";
$exec_requete = mysqli_query($db, $requete);
$anomalies = mysqli_fetch_all($exec_requete);

//si le nombre d'anomalie est supp à 0 on les affiche
if(count($anomalies)>0){
    echo("<h1>Voici un récapitulatif de toutes les erreurs que vous avez relevées</h1>");
    $temp="";
    $occ=(-1);
    $etage=-1;
    foreach ($anomalies as $anomalie){
        if($temp!=$anomalie[5]){
            $temp=$anomalie[5];
            if($temp==1) {
                $etage=$anomalie[2];
                echo("<p class='zone'> $anomalie[0] $etage </p>");
                echo ("<p class='question'> $anomalie[3] $anomalie[4]   </p>");
            }else{
                $niv=1;
                $occ=$anomalie[6];
                echo("<p class='zone'> $anomalie[0] </p>");
                echo ("<p class='question'> $anomalie[3] $anomalie[4] </p>");
            }

        }else{
            if($anomalie[5]==1) {
                if($etage!=$anomalie[2]) {
                    $etage=$anomalie[2];
                    echo("<p class='zone'> $anomalie[0] $etage</p>");
                }
                echo ("<p class='question'> $anomalie[3] $anomalie[4]  </p>");
            }else{
                if($occ!=$anomalie[6]){
                    $occ=$anomalie[6];
                    $niv++;
                    echo ("<p class='zone'> $anomalie[0] $niv </p>");
                }
                echo ("<p class='question'> $anomalie[3] $anomalie[4] </p>");
            }

        }

    }
}else{
    echo("vous n'avez pas relevé d'anomalie");
}
echo("<FORM action='enregistrementValider.php?idm=$id_montee' method='post'>
        <button id='identifiant4' type='submit'> confirmer</button>
    </FORM>");

?>
</div>
</html>
