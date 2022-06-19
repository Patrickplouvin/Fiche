<?php
session_start();
$id=$_SESSION['id'];
$idm=$_GET['idm'];

if ($id == null) {
    header("Location: index.php?id=$id");
}

$db_username = $_SESSION['userbd'];
$db_password = $_SESSION['passwordbd'];
$db_name     = $_SESSION['db_name'];
$db_host     = $_SESSION['db_host'];
$db = mysqli_connect($db_host, $db_username, $db_password,$db_name)
or die('could not connect to database');
$mois=date('m');
$annee=date('Y');

$requete="select count(*) 
        from ficheentete 
        where id_montee=$idm 
        and ((Heure_fin is null) 
        or (Heure_fin is not null 
                and Date between '$annee-$mois-01' AND '$annee-$mois-31' ))";
$exec_requete = mysqli_query($db,$requete);
$reponse      = mysqli_fetch_array($exec_requete);
$count = $reponse['count(*)'];

if($count!=0){
    header("Location: AMS.php?msg=3");
}else{
    $sql = "insert into ficheentete values (default,CURDATE(),$id,$idm,CURTIME(),0,null,null)";
    if (mysqli_query($db, $sql)) {
        ;
        header("Location: ficheQuestion.php");
    } else {
        echo "Erreur : " . $sql . "<br>" . mysqli_error($db);
    }
}?>
