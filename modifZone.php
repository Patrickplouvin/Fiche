<html>
<head>
    <meta charset="utf-8">
    <!-- importer le fichier de style -->
    <link rel="stylesheet" href="css/modifFiche.css" media="screen" type="text/css" />
</head>
<div id="content">
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


//requete pour savoir le nombre de zones présente dans le batiment
$requete="SELECT count(*)
            FROM zone 
            WHERE id_montee=$id_montee
            AND id_zone=$id_zone";
$exec_requete=mysqli_query($db,$requete);
$reponse=mysqli_fetch_array($exec_requete);
$nombreZone=$reponse['count(*)'];

//test requete
//echo($nombreZone);

//requete pour récuperer le nom de la zone
$requete="SELECT nom_zone 
            FROM t_zone
            WHERE id=$id_zone";
$exec_requete=mysqli_query($db,$requete);
$reponse=mysqli_fetch_array($exec_requete);
$nom_zone=$reponse['nom_zone'];

//Création d'autant de table que la zone est présente
for($i=$nombreZone;$i>0;$i--){

    //début de la table
    echo("<caption>$nom_zone $i</caption>
           <table>
            <tr>
                <th>Question</th>
                <th>Quantité</th>
                <th>Rajouter/Enlever</th>
            </tr>");

    //contenu de la table
    //requete pour récuperer le nom de la question et la quantitée
    $requete="SELECT Libelle,zq.id_question,zq.id_zone
            FROM t_question tq 
                join zone_question zq on tq.id=zq.id_question 
            WHERE id_montee=$id_montee
            AND zq.id_zone=$id_zone
            AND zq.occurence_zone=$i";
    $exec_requete=mysqli_query($db,$requete);
    $reponse=mysqli_fetch_all($exec_requete);
    foreach ($reponse as $item){
        //requete pour récuperer le nombre de question déjà présente
        $requete="SELECT OCC
            FROM zone_question zq
            WHERE id_montee=$id_montee
            AND zq.id_zone=$id_zone
            AND zq.occurence_zone=$i
            AND zq.id_question=$item[1]";
        $exec_requete=mysqli_query($db,$requete);
        $reponse=mysqli_fetch_array($exec_requete);
        $nbrQuestion=$reponse["OCC"];
        echo("<tr>
                <td>$item[0]</td>
                <td>$nbrQuestion</td>
                ");
        //Si il la question est posée plus d'une fois l'utilisateur peut en supprimer une ou en rajouter une
        if($nbrQuestion>1) {
            echo("
                <td>
                    <FORM action='modifZoneValiderPlus.php?idz=$item[2]&idm=$id_montee&occ=$i&idq=$item[1]' method='post'>
                        <input type='image' id='image' alt='Login' src='asset/croix_verte.png' width='20px'>
                        <input type='image' formaction='modifZoneValiderMoins.php?idz=$item[2]&idm=$id_montee&occ=$i&idq=$item[1]' id='image' alt='Login' src='asset/croix_rouge.png' width='15px'>
                    </FORM>  
                </td>
              </tr>");
            //Sinon l'utilisateur ne peut que en rajouter
        }else{
            echo("
                <td>
                    <FORM action='modifZoneValiderPlus.php?idz=$item[2]&idm=$id_montee&occ=$i&idq=$item[1]' method='post'>
                        <input type='image' id='image' alt='Login' src='asset/croix_verte.png' width='20px'>
                    </FORM>  
                </td>
              </tr>");
        }
    }

    //fin de la table
    echo ("</table>");
}

echo("
    <FORM>
        <input type='submit' id='submit' value='Retour' FORMACTION='modifFiche.php?idf=$id_montee' formmethod='post' width='20px'>
    </FORM>");
?>