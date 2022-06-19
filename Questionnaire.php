<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- importer le fichier de style -->
    <link rel="stylesheet" href="css/questionnaire.css" media="screen" type="text/css" />
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

$id_montee=$_GET['idm'];
// savoir le nombre de zone associé à la montée
$requete = "SELECT count(*) 
            FROM zone 
            WHERE id_montee=$id_montee";
$exec_requete = mysqli_query($db,$requete);
$zones        = mysqli_fetch_array($exec_requete);
$cpt=$zones['count(*)'];

// savoir dans quelle zone nous sommes actuellement

$requete="SELECT ZoneActuelle 
            FROM ficheentete 
            WHERE id_montee=$id_montee 
              and Heure_fin is null";
$exec_requete = mysqli_query($db,$requete);
$zoneac       = mysqli_fetch_array($exec_requete);
$id_zone=$_SESSION['zoneac']=$zoneac['ZoneActuelle'];


// savoir si il y a un historique
$requete="SELECT count(*) 
            FROM ficheentete 
            WHERE id_montee=$id_montee 
              and Heure_fin is not null";
$exec_requete = mysqli_query($db,$requete);
$anciennes   = mysqli_fetch_array($exec_requete);
$ancienne=$anciennes['count(*)'];


if($ancienne=="0") {
    //Si première fiche
    if ($_SESSION['zoneac'] >= $cpt) {
        header("Location: enregistrement.php?idm=$id_montee");
    } else {
        //Requete pour récuperer l'id de la zone ainsi que son id
        $requete = "SELECT t_zone.id,nom_zone,zone.occurence_zone
                    FROM zone 
                        inner join t_zone on zone.id_zone=t_zone.id 
                    WHERE zone.id_montee=$id_montee 
                    ORDER BY  id_zone , Etage";
        $exec_requete = mysqli_query($db, $requete);
        $zones = mysqli_fetch_all($exec_requete);

        //c'est dans ce tableau que nous recuperons la zone actuel
        $zone = $zones[$id_zone];

        //regarder à quel niveau nous sommes
        $requete = "SELECT occurence_zone 
                    FROM zone 
                    WHERE id_montee=$id_montee 
                    ORDER BY id_zone ASC, occurence_zone DESC";
        $exec_requete = mysqli_query($db, $requete);
        $reponse = mysqli_fetch_all($exec_requete);
        $niv = $reponse[$id_zone][0];

        //on regarde si la zone est un étage
        if ($zone[0] === "1") {
            $requete = "SELECT Nombre_etage 
                        FROM montee 
                        WHERE id=$id_montee";
            $exec_requete = mysqli_query($db, $requete);
            $etage = mysqli_fetch_array($exec_requete);
            $nbEtage = $etage['Nombre_etage'];
            echo("<h1>$zone[1] ETAGE : ".$niv ."</h1>");
        } else {
            echo("<h1>$zone[1] $niv</h1>");
            }
            $_SESSION['zone'] = $zone[0];
            echo("<FORM action='validation.php?idm=$id_montee&niv=$niv' method='post'>");

            //requete pour récuperer l'id et le nom d'un type
            $requete = "SELECT id,Libelle 
                        FROM t_type";
            $exec_requete = mysqli_query($db, $requete);
            $types = mysqli_fetch_all($exec_requete);
            foreach ($types as $type) {

                //requete pour récuperer les id des questions et leurs libelle
                $requete = "SELECT  distinct tq.id, tq.Libelle 
                        FROM t_question tq 
                        WHERE tq.id_zone=$zone[0] 
                        AND tq.id_type=$type[0]";
                $exec_requete = mysqli_query($db, $requete);
                $questions = mysqli_fetch_all($exec_requete);
                if (count($questions) != 0) {
                    echo("<h2>$type[1]</h2>");
                    echo("<div id='identifiant5' style='width: auto'>");
                }
                foreach ($questions as $question) {
                    $requete = "SELECT OCC
                        FROM zone_question
                        WHERE id_question=$question[0]
                        AND id_zone=$zone[0]
                        AND id_montee=$id_montee
                        AND occurence_zone=$niv";
                    $exec_requete = mysqli_query($db, $requete);
                    $occurences = mysqli_fetch_array($exec_requete);
                    if (isset($occurences)) {
                        $occ = (int)$occurences['OCC'];
                        for ($i = 0; $i < $occ; $i++) {
                            echo("<label for='$question[0] $question[1]'>$question[0] $question[1]:</label>");
                            echo("<br><select name='$question[0]?$i'><br>");
                            $requete = "SELECT Libelle,Anomalie 
                            FROM t_reponse 
                            WHERE id_question=$question[0] 
                            ORDER BY Anomalie ASC";
                            $exec_requete = mysqli_query($db, $requete);
                            $reponses = mysqli_fetch_all($exec_requete);
                            foreach ($reponses as $reponse) {
                                echo("<option value='$reponse[0]'>$reponse[0] <br>");
                            }
                            echo("</select>
                    <br>
                  <br>");
                        }
                    } else {
                        echo("<label for='$question[0] $question[1]'>$question[0] $question[1]:</label>");
                        echo("<br><select name='$question[0]'><br>");
                        $requete = "SELECT Libelle,Anomalie 
                            FROM t_reponse 
                            WHERE id_question=$question[0] 
                            ORDER BY Anomalie ASC";
                        $exec_requete = mysqli_query($db, $requete);
                        $reponses = mysqli_fetch_all($exec_requete);
                        foreach ($reponses as $reponse) {
                            echo("<option value='$reponse[0]' >$reponse[0] <br>");
                        }
                        echo("</select>
                    <br>
                  <br>");
                    }
                }
                if (count($questions) != 0) {
                    echo("</div>");
                }
            }
            echo("<button id='identifiant4' type='submit'> confirmer</button>");
            echo("</FORM>");
    }
}
else
{
    //Sinon on récupère les anciennes réponses
    if ($_SESSION['zoneac'] >= $cpt) {
        header("Location: enregistrement.php?idm=$id_montee");
    } else {
        //requete pour l'id de la derniere fiche remplit
        $requete = " SELECT id 
                    FROM ficheentete 
                    WHERE id_montee=$id_montee
                    AND Heure_fin is not null
                    ORDER BY date DESC
                    LIMIT 1";
        $exec_requete = mysqli_query($db,$requete);
        $reponse=mysqli_fetch_array($exec_requete);
        $ancienne_fiche=$reponse['id'];

        //Requete pour récuperer l'id de la zone ainsi que son id
        $requete = "SELECT t_zone.id,nom_zone,zone.occurence_zone
                    FROM zone 
                        inner join t_zone on zone.id_zone=t_zone.id 
                    WHERE zone.id_montee=$id_montee 
                    ORDER BY  id_zone , Etage";
        $exec_requete = mysqli_query($db, $requete);
        $zones = mysqli_fetch_all($exec_requete);

        //c'est dans ce tableau que nous recuperons la zone actuel
        $zone = $zones[$id_zone];

        //regarder à quel niveau nous sommes
        $requete = "SELECT occurence_zone 
                    FROM zone 
                    WHERE id_montee=$id_montee 
                    ORDER BY id_zone ASC, occurence_zone DESC";
        $exec_requete = mysqli_query($db, $requete);
        $reponse = mysqli_fetch_all($exec_requete);
        $niv = $reponse[$id_zone][0];

        //on regarde si la zone est un étage
        if ($zone[0] === "1") {
            $requete = "SELECT Nombre_etage 
                        FROM montee 
                        WHERE id=$id_montee";
            $exec_requete = mysqli_query($db, $requete);
            $etage = mysqli_fetch_array($exec_requete);
            $nbEtage = $etage['Nombre_etage'];
            echo("<h1>$zone[1] ETAGE : ".$niv ."</h1>");
        } else {
            echo("<h1>$zone[1] $niv</h1>");
        }
        $_SESSION['zone'] = $zone[0];
        echo("<FORM action='validation.php?idm=$id_montee&niv=$niv' method='post'>");

        //requete pour récuperer l'id et le nom d'un type
        $requete = "SELECT id,Libelle 
                        FROM t_type";
        $exec_requete = mysqli_query($db, $requete);
        $types = mysqli_fetch_all($exec_requete);
        foreach ($types as $type) {

            //requete pour récuperer les id des questions et leurs libelle
            $requete = "SELECT  distinct tq.id, tq.Libelle 
                        FROM t_question tq 
                        WHERE tq.id_zone=$zone[0] 
                        AND tq.id_type=$type[0]";
            $exec_requete = mysqli_query($db, $requete);
            $questions = mysqli_fetch_all($exec_requete);
            if (count($questions) != 0) {
                echo("<h2>$type[1]</h2>");
                echo("<div id='identifiant5'>");
            }
            foreach ($questions as $question) {
                $requete = "SELECT OCC
                        FROM zone_question
                        WHERE id_question=$question[0]
                        AND id_zone=$zone[0]
                        AND id_montee=$id_montee
                        AND occurence_zone=$niv";
                $exec_requete = mysqli_query($db, $requete);
                $occurences = mysqli_fetch_array($exec_requete);
                if (isset($occurences)) {
                    $occ = (int)$occurences['OCC'];
                    for ($i = 0; $i < $occ; $i++) {

                        //requete pour récuperer toute les réponses
                        $requete = "SELECT Libelle,Anomalie,id
                            FROM t_reponse tr
                            WHERE id_question=$question[0]";
                        $exec_requete = mysqli_query($db, $requete);
                        $reponses = mysqli_fetch_all($exec_requete);

                        //requete pour récupérer l'ancienne réponse
                        $requete = " SELECT Libelle,Anomalie,commentaire,tr.id 
                                    FROM fichequestion fq
                                        inner join t_reponse tr on tr.id=fq.id_reponse
                                    WHERE fq.id_question=$question[0] 
                                    AND fq.occ=$id_zone
                                    AND fq.id_fiche=$ancienne_fiche
                                    ORDER BY fq.id ASC";
                        $exec_requete = mysqli_query($db,$requete);
                        $ancienne_reponses = mysqli_fetch_all($exec_requete);

                        if(isset($ancienne_reponses[$i])) {
                            $com=$ancienne_reponses[$i][2];
                            echo("<label for='$question[0] $question[1]'>$question[0] $question[1] - $com -:</label>");
                        }else{
                            echo("<label for='$question[0] $question[1]'>$question[0] $question[1]:</label>");
                        }
                        echo("<br><select name='$question[0]?$i'><br>");

                        foreach ($reponses as $reponse) {
                            if ($reponse[2]==$ancienne_reponses[$i][3]) {
                                echo("<option value='$reponse[0]' selected>$reponse[0]<br>");
                            }else{
                                echo("<option value='$reponse[0]'>$reponse[0]<br>");
                            }
                        }
                        echo("</select>
                    <br>
                  <br>");
                    }
                } else {
                    //requete pour récuperer les réponses
                    $requete = "SELECT Libelle,Anomalie,id 
                            FROM t_reponse 
                            WHERE id_question=$question[0] 
                            ORDER BY Anomalie ASC";
                    $exec_requete = mysqli_query($db, $requete);
                    $reponses = mysqli_fetch_all($exec_requete);

                    //requete pour récuperer l'ancienne réponsee si elle existe
                    $requete = "SELECT Libelle,Anomalie,commentaire,tr.id
                            FROM t_reponse tr 
                                inner join fichequestion fq on tr.id=fq.id_reponse
                            WHERE tr.id_question=$question[0] 
                            AND fq.occ=$id_zone
                            AND fq.id_fiche=$ancienne_fiche
                            ORDER BY Anomalie ASC";
                    $exec_requete = mysqli_query($db, $requete);
                    $ancienne_reponses = mysqli_fetch_array($exec_requete);
                    if(isset($ancienne_reponses)) {
                        $com=$ancienne_reponses['commentaire'];
                        $questionp=utf8_decode($question[1]);
                        echo("<label for='$question[0] $question[1]'>$question[0] $question[1] $com:</label>");
                    }else{
                        echo("<label for='$question[0] $question[1]'>$question[0] $question[1]:</label>");
                    }
                    echo("<br><select name='$question[0]'><br>");

                    foreach ($reponses as $reponse) {
                        if ($reponse[2]==$ancienne_reponses['id']) {
                            echo("<option value='$reponse[0]' selected>$reponse[0]<br>");
                        }else{
                            echo("<option value='$reponse[0]'>$reponse[0]<br>");
                        }
                    }
                    echo("</select>
                    <br>
                  <br>");
                }
            }
            if (count($questions) != 0) {
                echo("</div>");
            }
        }
        echo("<button id='identifiant4' type='submit'> confirmer</button>");
        echo("</FORM>");
    }
}
    ?>
</div>
</html>
