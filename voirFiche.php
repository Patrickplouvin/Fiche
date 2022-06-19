<html>
<head>
    <title>Fiche séurité</title>
    <meta charset="utf-8">
    <!-- importer le fichier de style -->
    <link rel="stylesheet" href="css/voirFiche.css" media="screen" type="text/css" />
</head>
<body>

<?php

session_start();
$id=$_SESSION['id'];
$prenom=$_SESSION['prenom'];
$nom=$_SESSION['nom'];


$db_username = $_SESSION['userbd'];
$db_password = $_SESSION['passwordbd'];
$db_name     = $_SESSION['db_name'];
$db_host     = $_SESSION['db_host'];
$db = mysqli_connect($db_host, $db_username, $db_password,$db_name)
or die('could not connect to database');

$idf=$_GET['idf'];
echo ("<div id='container'>");
echo("<FORM action='voirFiche.php?idf=$idf' method='post'>");
if(isset($_POST['choix'])){
    $choix=$_POST['choix'];
    echo("<select name='choix'><br>");
    for($i = 1; $i <= 2; $i++){
        if($i==$choix){
            if($i==1){
                echo("<option value='$i' selected style='width: auto'>Toute la fiche <br>");
            }else{
                echo("<option value='$i' selected style='width: auto'>Que les anomalies <br>");
            }
        }else {
            if($i==1){
                echo("<option value='$i' selected style='width: auto'>Toute la fiche <br>");
            }else {
                echo("<option value='$i' style='width: auto'>Que les anomalies <br>");
            }
        }
    }
}else {
    echo("<select name='choix'><br> >");
    for ($i = 1; $i <= 2; $i++) {
        $choix=1;
        if($i==1){
            echo("<option value='$i' selected style='width: auto'>Toute la fiche <br>");
        }else {
            echo("<option value='$i' style='width: auto'>Que les anomalies <br>");
        }
    }
}
echo("</select>");
echo("<button id='identifiant4' type='submit' > confirmer</button></form><br>");
echo ("</div>");
$requete="SELECT id_montee,Validation
          FROM ficheentete
          WHERE id=$idf";
$exec_requete=mysqli_query($db,$requete);
$reponses=mysqli_fetch_array($exec_requete);
$idm=$reponses['id_montee'];
$validation=$reponses['Validation'];

// savoir le nombre de zone associé à la montée
$requete = "SELECT max(Etage) 
            FROM fichequestion 
            WHERE id_fiche=$idf";
$exec_requete = mysqli_query($db,$requete);
$zones        = mysqli_fetch_array($exec_requete);
$cpt=$zones['max(Etage)'];
$occ=0;



if($validation==1) {
    if($choix==1) {
        $requete = "SELECT DISTINCT occ,z.id,nom_zone
                    FROM fichequestion fq 
                        inner join t_zone z on z.id=fq.id_zone
                    WHERE fq.id_fiche=$idf
                    ORDER BY z.id";
        $exec_requete = mysqli_query($db, $requete);
        $zones = mysqli_fetch_all($exec_requete);
        foreach ($zones as $zone) {
            $nbetage=$cpt;
            if($zone[1]==1){
                $requete = "SELECT tq.Libelle,tr.Libelle,fq.id_zone,fq.id_type,fq.Etage,tr.Anomalie,fq.id_question,fq.id
                        FROM fichequestion fq 
                            inner join t_zone tz on fq.id_type=tz.id 
                            inner join t_question tq on fq.id_question = tq.id 
                            inner join t_reponse tr on tr.id=fq.id_reponse
                        WHERE (id_fiche=$idf and fq.id_zone=$zone[1] and Etage=$nbetage )";
                $exec_requete = mysqli_query($db, $requete);
                $reponses = mysqli_fetch_all($exec_requete);
            }else {
                $requete = "SELECT tq.Libelle,tr.Libelle,fq.id_zone,fq.id_type,fq.Etage,tr.Anomalie,fq.id_question,fq.id
                        FROM fichequestion fq 
                            inner join t_zone tz on fq.id_type=tz.id 
                            inner join t_question tq on fq.id_question = tq.id 
                            inner join t_reponse tr on tr.id=fq.id_reponse
                        WHERE (id_fiche=$idf and fq.id_zone=$zone[1] and fq.occ=$occ)";
                $exec_requete = mysqli_query($db, $requete);
                $reponses = mysqli_fetch_all($exec_requete);
            }
            if ($zone[1] === "1") {
                $requete = "SELECT Nombre_etage 
                            FROM montee 
                            WHERE id=$idm";
                $exec_requete = mysqli_query($db, $requete);
                $etage = mysqli_fetch_array($exec_requete);
                $nbEtage = $etage['Nombre_etage'];
                echo("<h1>$zone[2] Etage: " . ($cpt--) . "</h1>");
            } else {
                echo("<h1>$zone[2]</h1>");
            }
            foreach ($reponses as $reponse) {
                echo ("<form method='post' action='modification.php?idf=$idf'>");
                if ($reponse[5] == 1) {
                    echo("<p>-$reponse[0] $reponse[1]: </p><textarea name='$reponse[7]' rows='10' cols='50' required></textarea>");
                } else {
                    echo("<p>-$reponse[0] $reponse[1]</p>");
                }
            }
            $nbetage--;
            $occ++;
        }
        echo("<button id='identifiant4' type='submit' style='font-size: xx-large; height: 65px; margin-top=15px;'>confirmer</button>");
        echo("</form>");
    }else{
        $requete = "SELECT DISTINCT occ,z.id,nom_zone
                    FROM fichequestion fq 
                        inner join t_zone z on z.id=fq.id_zone
                    WHERE fq.id_fiche=$idf
                    ORDER BY z.id";
        $exec_requete = mysqli_query($db, $requete);
        $zones = mysqli_fetch_all($exec_requete);
        foreach ($zones as $zone) {
            $nbetage=$cpt;
            if($zone[1]==1){
                $requete = "SELECT tq.Libelle,tr.Libelle,fq.id_zone,fq.id_type,fq.Etage,tr.Anomalie,fq.id_question,fq.id,fq.commentaire
                        FROM fichequestion fq 
                            inner join t_zone tz on fq.id_type=tz.id 
                            inner join t_question tq on fq.id_question = tq.id 
                            inner join t_reponse tr on tr.id=fq.id_reponse
                        WHERE (id_fiche=$idf and fq.id_zone=$zone[1] and Etage=$nbetage )
                          and tr.Anomalie=1";
                $exec_requete = mysqli_query($db, $requete);
                $reponses = mysqli_fetch_all($exec_requete);
            }else {
                $requete = "SELECT tq.Libelle,tr.Libelle,fq.id_zone,fq.id_type,fq.Etage,tr.Anomalie,fq.id_question,fq.id,fq.commentaire
                        FROM fichequestion fq 
                            inner join t_zone tz on fq.id_type=tz.id 
                            inner join t_question tq on fq.id_question = tq.id 
                            inner join t_reponse tr on tr.id=fq.id_reponse
                        WHERE (id_fiche=$idf and fq.id_zone=$zone[1] and fq.occ=$occ)
                          and tr.Anomalie=1";
                $exec_requete = mysqli_query($db, $requete);
                $reponses = mysqli_fetch_all($exec_requete);
            }
            if ($zone[1] === "1") {
                $requete = "SELECT Nombre_etage 
                            FROM montee 
                            WHERE id=$idm";
                $exec_requete = mysqli_query($db, $requete);
                $etage = mysqli_fetch_array($exec_requete);
                $nbEtage = $etage['Nombre_etage'];
                if(count($reponses)>0) {
                    echo("<h1>$zone[2] Etage: " . ($cpt--) . "</h1>");
                }
            } else {
                if(count($reponses)>0) {
                    echo("<h1>$zone[2]</h1>");
                }
            }
            if(count($reponses)==0){
                echo ("<form method='post' action='modification.php?idf=$idf'>");
            }
            foreach ($reponses as $reponse) {
                echo ("<form method='post' action='modification.php?idf=$idf'>");
                if ($reponse[5] == 1) {
                    echo("<p>-$reponse[0] $reponse[1]: </p><textarea name='$reponse[7]' rows='10' cols='50' required>$reponse[8]</textarea>");

                } else {
                    echo("<p>-$reponse[0] $reponse[1]</p>");
                }
            }
            $nbetage--;
            $occ++;
        }
        echo("<button id='identifiant4' type='submit' style='font-size: xx-large; height: 65px; margin-top=15px;'>confirmer</button>");
        echo("</form>");
    }





}
elseif($validation==2)
{
    if($choix==1) {
        $requete = "SELECT DISTINCT occ,z.id,nom_zone
                    FROM fichequestion fq 
                        inner join t_zone z on z.id=fq.id_zone
                    WHERE fq.id_fiche=$idf
                    ORDER BY z.id";
        $exec_requete = mysqli_query($db, $requete);
        $zones = mysqli_fetch_all($exec_requete);
        foreach ($zones as $zone) {
            $nbetage=$cpt;
            if($zone[1]==1){
                $requete = "SELECT tq.Libelle,tr.Libelle,fq.id_zone,fq.id_type,fq.Etage,tr.Anomalie,fq.id_question,fq.id,fq.commentaire
                        FROM fichequestion fq 
                            inner join t_zone tz on fq.id_type=tz.id 
                            inner join t_question tq on fq.id_question = tq.id 
                            inner join t_reponse tr on tr.id=fq.id_reponse
                        WHERE (id_fiche=$idf and fq.id_zone=$zone[1] and Etage=$nbetage )";
                $exec_requete = mysqli_query($db, $requete);
                $reponses = mysqli_fetch_all($exec_requete);
            }else {
                $requete = "SELECT tq.Libelle,tr.Libelle,fq.id_zone,fq.id_type,fq.Etage,tr.Anomalie,fq.id_question,fq.id,fq.commentaire
                        FROM fichequestion fq 
                            inner join t_zone tz on fq.id_type=tz.id 
                            inner join t_question tq on fq.id_question = tq.id 
                            inner join t_reponse tr on tr.id=fq.id_reponse
                        WHERE (id_fiche=$idf and fq.id_zone=$zone[1] and fq.occ=$occ)";
                $exec_requete = mysqli_query($db, $requete);
                $reponses = mysqli_fetch_all($exec_requete);
            }
            if ($zone[1] === "1") {
                $requete = "SELECT Nombre_etage 
                            FROM montee 
                            WHERE id=$idm";
                $exec_requete = mysqli_query($db, $requete);
                $etage = mysqli_fetch_array($exec_requete);
                $nbEtage = $etage['Nombre_etage'];
                echo("<h1>$zone[2] Etage: " . ($cpt--) . "</h1>");
            } else {
                echo("<h1>$zone[2]</h1>");
            }
            foreach ($reponses as $reponse) {
                echo ("<form method='post' action='modification.php?idf=$idf'>");
                if ($reponse[5] == 1) {
                    echo("<p>-$reponse[0] $reponse[1]: </p><textarea name='$reponse[7]' rows='10' cols='50' required>$reponse[8]</textarea>");
                } else {
                    echo("<p>-$reponse[0] $reponse[1]</p>");
                }
            }
            $nbetage--;
            $occ++;
        }
        echo("<button id='identifiant4' type='submit' style='font-size: xx-large; height: 65px; margin-top=15px;'>confirmer</button>");
        echo("</form>");
    }else{
        $requete = "SELECT DISTINCT occ,z.id,nom_zone
                    FROM fichequestion fq 
                        inner join t_zone z on z.id=fq.id_zone
                    WHERE fq.id_fiche=$idf
                    ORDER BY z.id";
        $exec_requete = mysqli_query($db, $requete);
        $zones = mysqli_fetch_all($exec_requete);
        foreach ($zones as $zone) {
            $nbetage=$cpt;
            if($zone[1]==1){
                $requete = "SELECT tq.Libelle,tr.Libelle,fq.id_zone,fq.id_type,fq.Etage,tr.Anomalie,fq.id_question,fq.id,fq.commentaire
                        FROM fichequestion fq 
                            inner join t_zone tz on fq.id_type=tz.id 
                            inner join t_question tq on fq.id_question = tq.id 
                            inner join t_reponse tr on tr.id=fq.id_reponse
                        WHERE (id_fiche=$idf and fq.id_zone=$zone[1] and Etage=$nbetage )
                          and tr.Anomalie=1";
                $exec_requete = mysqli_query($db, $requete);
                $reponses = mysqli_fetch_all($exec_requete);
            }else {
                $requete = "SELECT tq.Libelle,tr.Libelle,fq.id_zone,fq.id_type,fq.Etage,tr.Anomalie,fq.id_question,fq.id,fq.commentaire
                        FROM fichequestion fq 
                            inner join t_zone tz on fq.id_type=tz.id 
                            inner join t_question tq on fq.id_question = tq.id 
                            inner join t_reponse tr on tr.id=fq.id_reponse
                        WHERE (id_fiche=$idf and fq.id_zone=$zone[1] and fq.occ=$occ)
                          and tr.Anomalie=1";
                $exec_requete = mysqli_query($db, $requete);
                $reponses = mysqli_fetch_all($exec_requete);
            }
            if ($zone[1] === "1") {
                $requete = "SELECT Nombre_etage 
                            FROM montee 
                            WHERE id=$idm";
                $exec_requete = mysqli_query($db, $requete);
                $etage = mysqli_fetch_array($exec_requete);
                $nbEtage = $etage['Nombre_etage'];
                if(count($reponses)>0) {
                    echo("<h1>$zone[2] Etage: " . ($cpt--) . "</h1>");
                }
            } else {
                if(count($reponses)>0) {
                    echo("<h1>$zone[2]</h1>");
                }
            }
            if(count($reponses)==0){
                echo ("<form method='post' action='modification.php?idf=$idf'>");
            }
            foreach ($reponses as $reponse) {
                echo ("<form method='post' action='modification.php?idf=$idf'>");
                if ($reponse[5] == 1) {
                    echo("<p>-$reponse[0] $reponse[1]: </p><textarea name='$reponse[7]' rows='10' cols='50' required>$reponse[8]</textarea>");

                } else {
                    echo("<p>-$reponse[0] $reponse[1]</p>");
                }
            }
            $nbetage--;
            $occ++;
        }
        echo("<button id='identifiant4' type='submit' style='font-size: xx-large; height: 65px; margin-top=15px;'>confirmer</button>");
        echo("</form>");
    }

}
else //validation=3
{
    if($choix==1) {
        $requete = "SELECT DISTINCT occ,z.id,nom_zone
                    FROM fichequestion fq 
                        inner join t_zone z on z.id=fq.id_zone
                    WHERE fq.id_fiche=$idf
                    ORDER BY z.id";
        $exec_requete = mysqli_query($db, $requete);
        $zones = mysqli_fetch_all($exec_requete);
        foreach ($zones as $zone) {
            $nbetage=$cpt;
            if($zone[1]==1){
                $requete = "SELECT tq.Libelle,tr.Libelle,fq.id_zone,fq.id_type,fq.Etage,tr.Anomalie,fq.id_question,fq.id,fq.commentaire
                        FROM fichequestion fq 
                            inner join t_zone tz on fq.id_type=tz.id 
                            inner join t_question tq on fq.id_question = tq.id 
                            inner join t_reponse tr on tr.id=fq.id_reponse
                        WHERE (id_fiche=$idf and fq.id_zone=$zone[1] and Etage=$nbetage )";
                $exec_requete = mysqli_query($db, $requete);
                $reponses = mysqli_fetch_all($exec_requete);
            }else {
                $requete = "SELECT tq.Libelle,tr.Libelle,fq.id_zone,fq.id_type,fq.Etage,tr.Anomalie,fq.id_question,fq.id,fq.commentaire
                        FROM fichequestion fq 
                            inner join t_zone tz on fq.id_type=tz.id 
                            inner join t_question tq on fq.id_question = tq.id 
                            inner join t_reponse tr on tr.id=fq.id_reponse
                        WHERE (id_fiche=$idf and fq.id_zone=$zone[1] and fq.occ=$occ)";
                $exec_requete = mysqli_query($db, $requete);
                $reponses = mysqli_fetch_all($exec_requete);
            }
            if ($zone[1] === "1") {
                $requete = "SELECT Nombre_etage 
                            FROM montee 
                            WHERE id=$idm";
                $exec_requete = mysqli_query($db, $requete);
                $etage = mysqli_fetch_array($exec_requete);
                $nbEtage = $etage['Nombre_etage'];
                echo("<h1>$zone[2] Etage: " . ($cpt--) . "</h1>");
            } else {
                echo("<h1>$zone[2]</h1>");
            }
            foreach ($reponses as $reponse) {
                echo ("<form method='post' action='modification.php?idf=$idf'>");
                if ($reponse[5] == 1) {
                    echo("<p>-$reponse[0] $reponse[1]: </p><textarea readonly name='$reponse[7]' rows='10' cols='50' required>$reponse[8]</textarea>");
                } else {
                    echo("<p>-$reponse[0] $reponse[1]</p>");
                }
            }
            $nbetage--;
            $occ++;
        }
        echo("<button id='identifiant4' type='submit' style='font-size: xx-large; height: 65px; margin-top=15px;'>confirmer</button>");
        echo("</form>");
    }else{
        $requete = "SELECT DISTINCT occ,z.id,nom_zone
                    FROM fichequestion fq 
                        inner join t_zone z on z.id=fq.id_zone
                    WHERE fq.id_fiche=$idf
                    ORDER BY z.id";
        $exec_requete = mysqli_query($db, $requete);
        $zones = mysqli_fetch_all($exec_requete);
        foreach ($zones as $zone) {
            $nbetage=$cpt;
            if($zone[1]==1){
                $requete = "SELECT tq.Libelle,tr.Libelle,fq.id_zone,fq.id_type,fq.Etage,tr.Anomalie,fq.id_question,fq.id,fq.commentaire
                        FROM fichequestion fq 
                            inner join t_zone tz on fq.id_type=tz.id 
                            inner join t_question tq on fq.id_question = tq.id 
                            inner join t_reponse tr on tr.id=fq.id_reponse
                        WHERE (id_fiche=$idf and fq.id_zone=$zone[1] and Etage=$nbetage )
                          and tr.Anomalie=1";
                $exec_requete = mysqli_query($db, $requete);
                $reponses = mysqli_fetch_all($exec_requete);
            }else {
                $requete = "SELECT tq.Libelle,tr.Libelle,fq.id_zone,fq.id_type,fq.Etage,tr.Anomalie,fq.id_question,fq.id,fq.commentaire
                        FROM fichequestion fq 
                            inner join t_zone tz on fq.id_type=tz.id 
                            inner join t_question tq on fq.id_question = tq.id 
                            inner join t_reponse tr on tr.id=fq.id_reponse
                        WHERE (id_fiche=$idf and fq.id_zone=$zone[1] and fq.occ=$occ)
                          and tr.Anomalie=1";
                $exec_requete = mysqli_query($db, $requete);
                $reponses = mysqli_fetch_all($exec_requete);
            }
            if ($zone[1] === "1") {
                $requete = "SELECT Nombre_etage 
                            FROM montee 
                            WHERE id=$idm";
                $exec_requete = mysqli_query($db, $requete);
                $etage = mysqli_fetch_array($exec_requete);
                $nbEtage = $etage['Nombre_etage'];
                if(count($reponses)>0) {
                    echo("<h1>$zone[2] Etage: " . ($cpt--) . "</h1>");
                }
            } else {
                if(count($reponses)>0) {
                    echo("<h1>$zone[2]</h1>");
                }
            }
            if(count($reponses)==0){
                echo ("<form method='post' action='modification.php?idf=$idf'>");
            }
            foreach ($reponses as $reponse) {
                echo ("<form method='post' action='modification.php?idf=$idf'>");
                if ($reponse[5] == 1) {
                    echo("<p>-$reponse[0] $reponse[1]: </p><textarea readonly name='$reponse[7]' rows='10' cols='50' required>$reponse[8]</textarea>");

                } else {
                    echo("<p>-$reponse[0] $reponse[1]</p>");
                }
            }
            $nbetage--;
            $occ++;
        }
        echo("<button id='identifiant4' type='submit' style='font-size: xx-large; height: 65px; margin-top=15px;'>confirmer</button>");
        echo("</form>");
    }
}