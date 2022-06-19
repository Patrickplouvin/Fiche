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
    $nb_etage++;
    $requete="SELECT max(occurence_zone)
                FROM zone
                WHERE id_montee=$id_montee
                AND id_zone=$id_zone
            ";
    $exec_requete=mysqli_query($db,$requete);
    $reponse=mysqli_fetch_array($exec_requete);
    $occ=(int)$reponse['max(occurence_zone)'] +1;
    $sql="INSERT INTO zone VALUES 
            (default,$id_montee,$id_zone,$occ,$nb_etage)";
    mysqli_query($db,$sql);
    $sql="UPDATE montee 
            SET Nombre_etage=$nb_etage 
            WHERE id=$id_montee";
    mysqli_query($db,$sql);
}else{
    $requete="SELECT max(occurence_zone)
                FROM zone
                WHERE id_montee=$id_montee
                AND id_zone=$id_zone
            ";
    $exec_requete=mysqli_query($db,$requete);
    $reponse=mysqli_fetch_array($exec_requete);
    $occ=(int)$reponse['max(occurence_zone)'] +1;
    $sql="INSERT into zone values (default,$id_montee,$id_zone,$occ,null)";
    mysqli_query($db,$sql);
}

//requete pour pouvoir recupérer le nombre de fois qu'une zone est repete
$requete="SELECT max(occurence_zone)
                FROM zone_question 
                WHERE id_montee=$id_montee
                AND id_zone=$id_zone";
$exec_requete=mysqli_query($db,$requete);
$reponse=mysqli_fetch_array($exec_requete);
$occurence_zone=$reponse['max(occurence_zone)'];
if(!isset($occurence_zone)){
    $occurence_zone=1;
}else{
    $occurence_zone++;
}
//requete pour savoir les question qui sont à ajouter dans la table
$requete="SELECT distinct (id_question)
                FROM zone_question
                WHERE id_zone=$id_zone";
$exec_requete=mysqli_query($db,$requete);
$reponse=mysqli_fetch_all($exec_requete);
foreach ( $reponse as $item ) {
    $sql="INSERT INTO zone_question VALUES
            (default,$item[0],$id_montee,$id_zone,$occurence_zone,1)";
    if(mysqli_query($db,$sql)){

    }else{
        echo "Erreur : " . $sql . "<br>" . mysqli_error($db);
    }
}
header("Location: modifFiche.php?idf=$id_montee");
exit();
