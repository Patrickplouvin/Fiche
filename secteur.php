<html>
<head>
    <title>Fiche séurité</title>
    <meta charset="utf-8">
    <!-- importer le fichier de style -->
    <link rel="stylesheet" href="css/secteur.css" media="screen" type="text/css" />
</head>
<body>

<?php

session_start();
$id=$_SESSION['id'];
$prenom=$_SESSION['prenom'];
$nom=$_SESSION['nom'];
$type=$_SESSION['type'];
//on vérifie que l'utilisateur est bien connecté sinon on le renvoit se connecter
if ($id == null or $type != 'GS') {
    header("Location: index.php?id=$id");
}

$id_secteur=$_GET['ids'];
$_SESSION['session']=$id_secteur;

$db_username = $_SESSION['userbd'];
$db_password = $_SESSION['passwordbd'];
$db_name     = $_SESSION['db_name'];
$db_host     = $_SESSION['db_host'];
$db = mysqli_connect($db_host, $db_username, $db_password,$db_name)
or die('could not connect to database');
$lmois=["Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Aout","Septembre","Octobre","Novembre","Décembre"];
echo ("<div id='container'>");
echo("<FORM action='secteur.php?ids=$id_secteur' method='post'>");
echo("<select name='mois'><br>  \n");

/*Regarde si les variables de formulaires sont initialisées.
    Si oui alors on va dans les menus déroulant correspondant on va déjà sélectionner celle qui est "actif"
    Si non alors on selectionne les valeurs de la date courante
  */
if(isset($_POST['mois']) && isset($_POST['annee'])){
    $moisactuel=$_POST['mois'];
    for ($i = 0; $i < 12; $i++) {
        $value = $i + 1;
        if ($value == $moisactuel) {
            echo("<option value='$value' selected >$lmois[$i] </option><br> \n");
        } else {
            echo("<option value='$value'>$lmois[$i] </option><br> \n");
        }
    }
    $anneac=$_POST['annee'];
    echo("</select><select name='annee'></option><br>");
    for($i=2022;$i<=date('Y');$i++){
        if($i==$anneac){
            echo("<option value='$i' selected>$i </option><br>\n");
        }else {
            echo("<option value='$i'>$i </option><br>\n");
        }
    }
}else {
    for ($i = 0; $i < 12; $i++) {
        $moisactuel = date('m');
        $value = $i + 1;
        if ($value == $moisactuel) {
            echo("<option value='$value' selected>$lmois[$i]</option><br>\n");
        } else {
            echo("<option value='$value'>$lmois[$i]</option><br>\n");
        }
    }
    echo("</select><select name='annee'></option><br>\n");

    for ($i = 2022; $i <= date('Y'); $i++) {
        $anneac = date('Y');
        if($i==$anneac){
            echo("<option value='$i' selected>$i </option><br>\n");
        }else {
            echo("<option value='$i'>$i </option><br>\n");
        }
    }
}
    echo("</select>");
    echo("<input id='submit' type='submit' value='Changer'>
            </form><br>");
    echo ("</div>");




    /*Ici on va selectionner les AMS qui correspondent a leur */
    $requete = "SELECT id,Nom,Prenom 
                FROM utilisateur 
                WHERE id_secteur=$id_secteur 
                  and id_superieur is not null 
                ORDER BY Nom asc";
    $exec_requete = mysqli_query($db, $requete);
    $amss = mysqli_fetch_all($exec_requete);
    foreach ($amss as $ams) {
        echo ("<div id='centrer'>");
        if (isset($_POST['mois']) && isset($_POST['annee'])) {
            $mois = date($_POST['mois']);
            $annee = $_POST['annee'];
            $requete = "SELECT ft.Date,ft.id_montee,ft.Heure_fin,m.Adresse1,ft.id,ft.Validation,min(ft.Validation) 
                        FROM ficheentete ft 
                            inner join montee m on m.id=ft.id_montee 
                        WHERE ft.id_utilisateur=$ams[0] 
                          and ft.Heure_fin is not null 
                          and Date between '$annee-$mois-01' AND '$annee-$mois-31' 
                        GROUP BY ft.id 
                        ORDER BY Validation asc, Heure_fin asc ";
            $exec_requete = mysqli_query($db, $requete);
            $fiches = mysqli_fetch_all($exec_requete);
        } else {
            $annee = date('Y');
            $mois = date('m');
            $requete = "SELECT ft.Date,ft.id_montee,ft.Heure_fin,m.Adresse1,ft.id,ft.Validation,min(ft.Validation) 
                        FROM ficheentete ft 
                            inner join montee m on m.id=ft.id_montee 
                        where ft.id_utilisateur=$ams[0] 
                          and ft.Heure_fin is not null 
                          and Date between '$annee-$mois-01' AND '$annee-$mois-31' 
                        GROUP BY ft.id 
                        ORDER BY Validation asc, Heure_fin asc";
            $exec_requete = mysqli_query($db, $requete);
            $fiches = mysqli_fetch_all($exec_requete);
        }
        $requete = "SELECT id, NumGroupe, Adresse1, Adresse2, CodePostal, Commune, Quartier, id_secteur, Nombre_etage, id_utilisateur 
                    FROM montee m 
                    where m.id_utilisateur=$ams[0] 
                      and id not IN (
                          SELECT ft.id_montee 
                          FROM ficheentete ft 
                              inner join montee m on m.id=ft.id_montee 
                          where ft.id_utilisateur=$ams[0] 
                            and ft.Heure_fin is not null 
                            and Date between '$annee-$mois-01' AND '$annee-$mois-31')";
        $exec_requete = mysqli_query($db, $requete);
        $fichesv = mysqli_fetch_all($exec_requete);
        if(count($fichesv)>0) {
            echo(count($fiches)." / ".(count($fichesv)+count($fiches))." ");
            echo("<button class='button' onclick='visibilite($ams[0])'>$ams[1] $ams[2] </button>
                  <img src='asset/circle-red.png' width='15px'>
                  </div>
                  <div id='$ams[0]' style='display: none'>
                <ul>");
        }elseif (count($fiches)>0 && $fiches[0][6]==1){
            echo(count($fiches)." / ".(count($fichesv)+count($fiches))." ");
            echo("<button class='button' onclick='visibilite($ams[0])'>$ams[1] $ams[2]</button>
                    <img src='asset/circle-orange.png' width='15px'>
                    </div>
                    <div id='$ams[0]' style='display: none''>
                  <ul>");
        }elseif (count($fiches)>0 && $fiches[0][6]==2){
            echo(count($fiches)." / ".(count($fichesv)+count($fiches))." ");
            echo("<button class='button' onclick='visibilite($ams[0])'>$ams[1] $ams[2]</button>
                    <img src='asset/circle-yellow.png' width='15px'>
                    </div>
                        <div id='$ams[0]' style='display: none''>
                   <ul>");
        }elseif (count($fiches)>0 && $fiches[0][6]==3){
            echo(count($fiches)." / ".(count($fichesv)+count($fiches))." ");
            echo("<button class='button' onclick='visibilite($ams[0])'>$ams[1] $ams[2]</button>
                    <img src='asset/circle-green.png' width='15px'>
                    </div>
                        <div id='$ams[0]' style='display: none''>
                    <ul>");
        }

        foreach ($fichesv as $fichev) {
            echo("<li>
                    <form action='notif.php?idams=$ams[0]' method='POST'>
                        <label>
                                <input type = 'image' src='asset/circle-red.png' alt='' width='30px'>
                                <input id='identifiant4' type='submit' value='$fichev[0] $fichev[2] $fichev[4]'>
                                <input type='image' formaction='modifFiche.php?idf=$fichev[0]' src='asset/outil_logo.png' width='30px'>
                        </label>
                    </form>
                  </li>");
        }

        foreach ($fiches as $fiche) {
                if ($fiche[5] == 1) {
                    echo("<li>
                            <form action='voirFiche.php?idf=$fiche[4]' method='POST'>
                                    <label>
                                        <input type = 'image' src='asset/circle-orange.png' alt='' width='30px'>
                                        <input id='identifiant4' type='submit' value='$fiche[0] $fiche[1] $fiche[2] $fiche[3]'>
                                        <input type='image' formaction='modifFiche.php?idf=$fiche[1]' src='asset/outil_logo.png' width='30px'>
                                    </label>
                            </form>
                          </li>");
                }
                if ($fiche[5] == 2) {
                    echo("<li>
                            <form action='voirFiche.php?idf=$fiche[4]' method='POST'>
                                    <label>
                                        <input type = 'image' src='asset/circle-yellow.png' alt='' width='30px'>
                                        <input id='identifiant4' type='submit' value='$fiche[0] $fiche[1] $fiche[2] $fiche[3]'>
                                        <input type='image' formaction='modifFiche.php?idf=$fiche[1]' src='asset/outil_logo.png' width='30px'>
                                    </label>
                            </form>
                          </li>");
                }
                if ($fiche[5] == 3) {
                    echo("<li>
                            <form action='voirFiche.php?idf=$fiche[4]' method='POST'>
                                    <label>
                                        <input type = 'image' src='asset/circle-green.png' alt='' width='30px'>
                                        <input id='identifiant4' type='submit' value='$fiche[0] $fiche[1] $fiche[2] $fiche[3]'>
                                        <input type='image' formaction='modifFiche.php?idf=$fiche[1]' src='asset/outil_logo.png' width='30px'>
                                    </label>
                            </form>
                          </li>");
                }
        }
        echo("</ul></div><br>");
    }


echo("
    <FORM>
        <input type='submit' id='submit' value='Retour' FORMACTION='GS.php' formmethod='post' width='20px'>
    </FORM>");
?>
<script>
    function visibilite(thingId)
    {
        var targetElement;
        targetElement = document.getElementById(thingId) ;
        if (targetElement.style.display === "none")
        {
            targetElement.style.display = "" ;
        } else {
            targetElement.style.display = "none" ;
        }
    }
</script>
</body>
</html>