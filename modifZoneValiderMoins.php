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
$ids=$_SESSION['session'];

//on vérifie que l'utilisateur est bien connecté sinon on le renvoit se connecter
if ($id == null or $type != 'GS') {
    header("Location: index.php?id=$id");
}

$id_montee=$_GET['idm'];
$id_zone=$_GET['idz'];
$niveau=$_GET['occ'];
$id_question=$_GET['idq'];

//Update -1 de la question
$sql="UPDATE zone_question 
    SET OCC=OCC-1 
    WHERE id_question=$id_question 
      AND id_zone=$id_zone
      AND id_montee=$id_montee
      AND occurence_zone=$niveau";
if(mysqli_query($db,$sql)){

}else{
    echo "Erreur : " . $sql . "<br>" . mysqli_error($db). "<br>";
};

Header("Location: modifZone.php?idz=$id_zone&idm=$id_montee");
exit();