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

//on récupère l'id du secteur à travers deux requetes
$requete = " SELECT id_montee 
            FROM ficheentete 
            WHERE id=$idf";
$exec_requete = mysqli_query($db,$requete);
$reponse = mysqli_fetch_array($exec_requete);
$id_montee=$reponse['id_montee'];


$requete = " SELECT id_secteur 
                FROM montee 
                where id=$id_montee ";
$exec_requete = mysqli_query($db,$requete);
$reponse = mysqli_fetch_array($exec_requete);
$id_secteur=$reponse['id_secteur'];


$requete="SELECT id 
        FROM fichequestion 
        WHERE id_fiche=$idf";
$exec_requete=mysqli_query($db,$requete);
$questions=mysqli_fetch_all($exec_requete);
foreach ($questions as $question){
    $option = isset($_POST["$question[0]"]);
    if ($option) {
        $commentaire=$_POST["$question[0]"];
        $sql = "UPDATE ficheQuestion 
                SET commentaire='$commentaire', 
                    date_derniere_modif=CURDATE(),
                    id_utilisateur_derniere_modif=$id                 
                where id_fiche=$idf and id=$question[0]";
        if(mysqli_query($db,$sql)){
            echo("Pas d'erreur");
        }else{
            echo "Erreur : " . $sql . "<br>" . mysqli_error($db);
        }
    }
}

$requete="SELECT Validation 
        FROM ficheentete 
        WHERE id=$idf";
$exec_requete=mysqli_query($db,$requete);
$validations=mysqli_fetch_array($exec_requete);
$validation=$validations['Validation'];

if($validation=1) {
    //requete pour avoir le nombre d'anomalie de cette fiche
    $requete="SELECT count(*) 
            FROM fichequestion 
                inner join t_reponse on t_reponse.id=fichequestion.id_reponse 
            where id_fiche=$idf 
              and Anomalie=1";
    $exec_requete=mysqli_query($db,$requete);
    $nbAnomalies=mysqli_fetch_array($exec_requete);
    $nbAnomalie=$nbAnomalies['count(*)'];

    //requete pour avoir l'id de la montée à modifier
    $requete="SELECT id_montee,Date
            FROM ficheentete
            WHERE id=$idf";
    $exec_requete=mysqli_query($db,$requete);
    $reponse=mysqli_fetch_array($exec_requete);
    $id_montee=$reponse['id_montee'];
    $date=$reponse['Date'];


    // Si la fiche ne contient pas d'anomalie
    if($nbAnomalie!="0") {
        $sql = "UPDATE ficheentete 
                SET Validation=2 
                WHERE id=$idf ";
        if (mysqli_query($db, $sql)) {
            header("Location: secteur.php?ids=$id_secteur");
        } else {
            echo "Erreur : " . $sql . "<br>" . mysqli_error($db);
        }
    }else{
        $sql = "UPDATE ficheentete 
                SET Validation=3 
                WHERE id=$idf 
                   or (date<curdate() and id_montee=$id_montee) ";
        if (mysqli_query($db, $sql)) {
            header("Location: secteur.php?ids=$id_secteur");
            exit;
        }
        else {
            echo "Erreur : " . $sql . "<br>" . mysqli_error($db);
        }
    }
    $requete = "SELECT count(*)
            FROM ficheentete 
            WHERE id_montee=$id_montee 
              and date < '$date'
              and Validation!=3";
    $exec_requete = mysqli_query($db, $requete);
    $reponse = mysqli_fetch_array($exec_requete);
    $cpt = $reponse['count(*)'];
    if(0==$cpt){
        header("Location: secteur.php?ids=$id_secteur");
        exit;
    }else{
        $requete="SELECT id,Date
                    FROM ficheentete
                    WHERE Date <'$date'
                    AND id_montee=$id_montee
                    AND Validation!=3
                    ORDER BY Date ASC";
        $exec_requete=mysqli_query($db,$requete);
        $reponses=mysqli_fetch_all($exec_requete);
        foreach ($reponses as $reponse){
            $requete = "SELECT fq.id_question,fq.id_zone,fq.Etage,fq.occ
                        FROM fichequestion fq
                            inner join t_reponse tr on fq.id_reponse = tr.id
                        WHERE fq.id_fiche=$reponse[0]
                        AND tr.Anomalie=1";
            $exec_requete = mysqli_query($db, $requete);
            $anomalies = mysqli_fetch_all($exec_requete);
            $total=0;
            foreach($anomalies as $anomalie) {
                $requete="SELECT id,Date
                    FROM ficheentete
                    WHERE Date <= '$date' 
                    AND Date > '$reponse[1]'
                    AND id_montee=$id_montee
                    ORDER BY Date ASC";
                $exec_requete=mysqli_query($db,$requete);
                $reps=mysqli_fetch_all($exec_requete);
                foreach ($reps as $rep){
                    if($anomalie[1]=="1") {
                        $requete = "SELECT count(*)
                            FROM fichequestion fq 
                                inner join t_reponse tr on fq.id_reponse=tr.id
                            WHERE fq.id_fiche=$rep[0]
                            AND fq.id_question=$anomalie[0]
                            AND fq.id_zone=$anomalie[1]
                            AND fq.Etage=$anomalie[2]
                            AND tr.Anomalie=0";
                    }else{
                        $requete = "SELECT count(*)
                            FROM fichequestion fq 
                                inner join t_reponse tr on fq.id_reponse=tr.id
                            WHERE fq.id_fiche=$rep[0]
                            AND fq.id_question=$anomalie[0]
                            AND fq.id_zone=$anomalie[1]
                            AND fq.occ=$anomalie[3]
                            AND tr.Anomalie=0";
                    }
                    $exec_requete=mysqli_query($db,$requete);
                    $compteurs=mysqli_fetch_array($exec_requete);
                    $compteur=$compteurs['count(*)'];
                    if($compteur=="0"){
                }else{
                    $total++;
                    }
                }
                if($total==count($anomalies)){
                    $sql = "UPDATE ficheentete 
                            SET Validation=3 
                            WHERE id=$reponse[0]";
                    if (mysqli_query($db, $sql)) {
                        echo ("pas d'erreur");
                    } else {
                        echo "Erreur : " . $sql . "<br>" . mysqli_error($db). "<br>";
                    }
                }
            }
        }

    }
    header("Location: secteur.php?ids=$id_secteur");
}



