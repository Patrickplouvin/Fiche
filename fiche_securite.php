<html>
<head>
  <meta charset="utf-8">
  <!-- importer le fichier de style -->
  <link rel="stylesheet" href="" media="screen" type="text/css" />
  <title>Fiche Sécurité</title>
</head>
<?php
  session_start();


  $id=$_SESSION['id'];
  $id_montee=$_GET['id'];

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

  $sql="insert into ficheentete values (default,CURDATE(),$id,$id_montee,CURTIME(),null)";
  if (mysqli_query($db, $sql)) {
    echo "Nouveau enregistrement créé avec succès <br>";
  }   else {
    echo "Erreur : " . $sql . "<br>" . mysqli_error($db);
  }

  $date=(date('d-m-y'));

$requete = "SELECT t_zone.id,nom_zone 
            FROM zone 
                inner join t_zone on zone.id_zone=t_zone.id 
            WHERE zone.id_montee=$id_montee";
$exec_requete = mysqli_query($db,$requete);
$zones     = mysqli_fetch_all($exec_requete);
foreach ($zones as $zone) {
    echo("<h1>$zone[1]</h1>");
    echo ("<FORM action='enregistrement.php' method='post'>");
    $requete="SELECT id,Libelle 
                FROM t_type";
    $exec_requete = mysqli_query($db,$requete);
    $types=mysqli_fetch_all($exec_requete);
    foreach($types as $type) {
        $requete = "SELECT  tq.id,tq.Libelle 
                    FROM zone z 
                        inner join t_question tq on z.id = tq.id_zone 
                    WHERE tq.id_zone=$zone[0] 
                      AND tq.id_type=$type[0]";
        $exec_requete = mysqli_query($db, $requete);
        $questions = mysqli_fetch_all($exec_requete);
        if(count($questions)!=0){
            echo ("<h2>$type[1]</h2>");
        }
        foreach ($questions as $question) {
            echo("<br><div id='question'> $question[0] $question[1]<br>");
            $requete = "SELECT Libelle,Anomalie 
                        FROM t_reponse 
                        WHERE id_question=$question[0]";
            $exec_requete = mysqli_query($db, $requete);
            $reponses = mysqli_fetch_all($exec_requete);
            foreach ($reponses as $reponse) {
                if ($reponse[1] != 1) {
                    echo("<input type='checkbox' name=$question[0] checked='checked'>$reponse[0]" . "<br>");
                } else {
                    echo("<input type='checkbox' name=$question[0] >$reponse[0]" . "<br>");
                }
            }
            echo ("</div>");
        }
    }
    echo ("<button type='submit'> confirmer</button>");
    echo("</FORM>");
}
echo("<button type='submit'> terminer</button>");
?>
</html>