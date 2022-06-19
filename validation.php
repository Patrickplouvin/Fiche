<?php
session_start();
$db_username = $_SESSION['userbd'];
$db_password = $_SESSION['passwordbd'];
$db_name     = $_SESSION['db_name'];
$db_host     = $_SESSION['db_host'];
$db = mysqli_connect($db_host, $db_username, $db_password,$db_name)
or die('could not connect to database');

$id_montee=$_GET['idm'];
$niv=$_GET['niv'];
$id=$_SESSION['id'];
if ($id == null) {
    //header("Location: index.php?id=$id");
}
// savoir le nombre de zone associé à la montée
$requete = "SELECT count(*) 
            FROM zone 
            WHERE id_montee=$id_montee";
$exec_requete = mysqli_query($db,$requete);
$zones     = mysqli_fetch_array($exec_requete);
$cpt=$zones['count(*)'];

$requete="SELECT ZoneActuelle,id 
            FROM ficheentete 
            WHERE id_montee=$id_montee 
              and Heure_fin is null";
$exec_requete = mysqli_query($db,$requete);
$zoneac     = mysqli_fetch_array($exec_requete);
$id_zone=$zoneac['ZoneActuelle'];
$idf=$zoneac['id'];

$zone=$_SESSION['zone'];

$requete = "SELECT  tq.id,tq.id_type 
            FROM t_question tq 
            WHERE tq.id_zone=$zone";
$exec_requete = mysqli_query($db, $requete);
$questions = mysqli_fetch_all($exec_requete);
foreach ($questions as $question) {
    $requete = "SELECT OCC
                FROM zone_question
                WHERE id_question=$question[0]
                AND id_montee=$id_montee
                AND id_zone=$zone
                AND occurence_zone=$niv";
    $exec_requete = mysqli_query($db, $requete);
    $occurences = mysqli_fetch_array($exec_requete);
    if (isset($occurences)) {
        $occ = (int)$occurences['OCC'];
        echo (" et $question[0] et  $zone ");
        for($i=0;$i<$occ;$i++) {
            $reponse = $_POST["$question[0]?$i"];
            $requete = "SELECT id 
                    FROM t_reponse 
                    WHERE Libelle='$reponse' 
                      AND id_question=$question[0]";
            $exec_requete = mysqli_query($db, $requete);
            $reponses = mysqli_fetch_all($exec_requete);
            $repid = $reponses[0];
            if ($zone === "1") {
                $requete = "SELECT Nombre_etage 
                        FROM montee 
                        WHERE id=$id_montee";
                $exec_requete = mysqli_query($db, $requete);
                $etage = mysqli_fetch_array($exec_requete);
                $nbEtage = $etage['Nombre_etage'];
                $sql = "insert into fichequestion values 
                    (default,$idf,$question[0],$nbEtage-$id_zone,$id_zone,$zone,$question[1],$repid[0],null,null,null,null)";
                if (mysqli_query($db, $sql)) {
                } else {
                    echo "Erreur : " . $sql . "<br>" . mysqli_error($db);
                }
            } else {
                $sql = "insert into fichequestion values 
                    (default,$idf,$question[0],null,$id_zone,$zone,$question[1],$repid[0],null,null,null,null)";
                if (mysqli_query($db, $sql)) {
                    echo("all");
                } else {
                    echo "Erreur : " . $sql . "<br>" . mysqli_error($db);
                }
            }
        }
    } else{
        $option = isset($_POST["$question[0]"]);
    if ($option) {
        $reponse = $_POST["$question[0]"];
        $requete = "SELECT id 
                    FROM t_reponse 
                    WHERE Libelle='$reponse' 
                      AND id_question=$question[0]";
        $exec_requete = mysqli_query($db, $requete);
        $reponses = mysqli_fetch_all($exec_requete);
        $repid = $reponses[0];
        if ($zone === "1") {
            $requete = "SELECT Nombre_etage 
                        FROM montee 
                        WHERE id=$id_montee";
            $exec_requete = mysqli_query($db, $requete);
            $etage = mysqli_fetch_array($exec_requete);
            $nbEtage = $etage['Nombre_etage'];
            $sql = "insert into fichequestion values 
                    (default,$idf,$question[0],$nbEtage-$id_zone,$id_zone,$zone,$question[1],$repid[0],null,null,null,null)";
            if (mysqli_query($db, $sql)) {
            } else {
                echo "Erreur : " . $sql . "<br>" . mysqli_error($db);
            }
        } else {
            $sql = "insert into fichequestion values 
                    (default,$idf,$question[0],null,$id_zone,$zone,$question[1],$repid[0],null,null,null,null)";
            if (mysqli_query($db, $sql)) {
            } else {
                echo "Erreur : " . $sql . "<br>" . mysqli_error($db);
            }
        }
    } else {
        echo "Pas de questionnaire <br>";
    }
}

}




$sql = "update ficheEnTete
        SET ZoneActuelle=ZoneActuelle+1
        WHERE id_montee=$id_montee
          AND id=$idf";
if (mysqli_query($db, $sql)) {
    header("Location: Questionnaire.php?idm=$id_montee");
}else{
    echo "Erreur : " . $sql . "<br>" . mysqli_error($db);
}
