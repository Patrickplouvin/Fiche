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
//on vérifie que l'utilisateur est bien connecté sinon on le renvoit se connecter
if ($id == null or $type != 'GS') {
    header("Location: index.php?id=$id");
}

$idm=$_GET['idf'];
$ids=$_SESSION['session'];

$requete="SELECT *
            FROM montee
            WHERE id=$idm";
$exec_requete=mysqli_query($db,$requete);
$immeuble=mysqli_fetch_array($exec_requete);
$rue=$immeuble['Adresse1'];
$commune=$immeuble['Commune'];

$requete="SELECT id_zone,nom_zone,count(id_montee)
            from zone 
                inner join t_zone on zone.id_zone=t_zone.id
            WHERE id_montee=$idm
            GROUP BY id_zone";
$exec_requete=mysqli_query($db,$requete);
$zones=mysqli_fetch_all($exec_requete);

$requete="SELECT *
            FROM t_zone 
            where id not in (
                select id_zone
                from zone
                where id_montee=$idm
            ) 
            ORDER BY t_zone.id";
$exec_requete=mysqli_query($db,$requete);
$pzones=mysqli_fetch_all($exec_requete);
?>
    <table>
    <tr>
        <th scope="col">Nom de la zone</th>
        <th scope="col">Quantité</th>
        <th scope="col">Rajouter/Enlever</th>
    </tr>
<?php

echo("<caption> L'immeuble rue: $rue <br> commune: $commune </caption>");
foreach ($zones as $item){
    echo("<tr>
            <th scope='row' align='left'>$item[1] 
                <form>
                    <input type='image' formaction='modifZone.php?idz=$item[0]&idm=$idm' formmethod='post' src='asset/outil_logo.png' width='30px' >
                </form>
            </th>
                <td align='center'>
                    $item[2]
                </td>
                <td align='center'>
                    <FORM action='modifFicheValiderPlus.php?idz=$item[0]&idm=$idm' method='post'>
                        <input type='image' id='image' alt='Login' src='asset/croix_verte.png' width='20px'>
                        <input type='image' formaction='modifFicheValiderMoins.php?idz=$item[0]&idm=$idm' id='image' alt='Login' src='asset/croix_rouge.png' width='15px'>
                    </FORM>  
                </td>
          </tr>");
}


foreach ($pzones as $item) {
    echo("<tr>
            <th scope='row' align='left'>$item[1]
            </th>
                <td align='center'>
                    0
                </td>
                <td align='center'>
                    <FORM action='modifFicheValiderPlus.php?idz=$item[0]&idm=$idm' method='post'>
                        <input type='image' id='image' alt='Login' src='asset/croix_verte.png' width='20px'>
                    </FORM>  
                </td>
          </tr>");
}
echo "</table>";
echo("$ids");
echo("
    <FORM>
        <input type='submit' id='submit' value='Retour' FORMACTION='secteur.php?ids=$ids' formmethod='post' width='20px'>
    </FORM>");
?>

