<?php
session_start();

$db_username = $_SESSION['userbd'];
$db_password = $_SESSION['passwordbd'];
$db_name = $_SESSION['db_name'];
$db_host = $_SESSION['db_host'];
$db = mysqli_connect($db_host, $db_username, $db_password, $db_name)
or die('could not connect to database');

$id=$_SESSION['id'];
$prenom=$_SESSION['prenom'];
$nom=$_SESSION['nom'];
$type=$_SESSION['type'];
//on vérifie que l'utilisateur est bien connecté sinon on le renvoit se connecter
if ($id == null or $type != 'GS') {
    header("Location: index.php?id=$id");
}

$id_montee = $_GET['idm'];
$id_zone = $_GET['idz'];

if($id_zone=="1"){
    $requete="SELECT Nombre_etage 
            FROM montee 
            where id=$id_montee ";
    $exec_requete=mysqli_query($db,$requete);
    $reponse=mysqli_fetch_array($exec_requete);
    $nb_etage=$reponse['Nombre_etage'];
    if($nb_etage<=0){
        header("Location: modifFiche.php?idf=$id_montee");
        exit();
    }else {
        $sql = "DELETE FROM zone 
                WHERE id_montee=$id_montee 
                  AND id_zone=$id_zone 
                  AND Etage=$nb_etage";
        mysqli_query($db, $sql);
        $nb_etage--;
        $sql = "UPDATE montee 
                SET Nombre_etage=$nb_etage 
                WHERE id=$id_montee";
        mysqli_query($db, $sql);
    }
}else{
    $requete="SELECT max(id)
                FROM zone 
                WHERE id_zone=$id_zone
                  AND id_montee=$id_montee";
    $exec_requete=mysqli_query($db,$requete);
    $reponse=mysqli_fetch_array($exec_requete);
    $idsupp=$reponse['max(id)'];

    $sql="DELETE FROM zone 
          WHERE id=$idsupp";
    mysqli_query($db,$sql);
}

//requete pour sa
$requete="SELECT max(occurence_zone)
                FROM zone_question 
                WHERE id_montee=$id_montee
                AND id_zone=$id_zone";
$exec_requete=mysqli_query($db,$requete);
$reponse=mysqli_fetch_array($exec_requete);
$occurence_zone=$reponse['max(occurence_zone)'];

$sql = "DELETE FROM zone_question
                WHERE id_montee=$id_montee 
                  AND id_zone=$id_zone
                  AND occurence_zone=$occurence_zone";
mysqli_query($db,$sql);

header("Location: modifFiche.php?idf=$id_montee");
exit();