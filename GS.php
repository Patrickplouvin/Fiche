<html lang="fr">
<head>
    <title>Fiche séurité</title>
    <meta charset="utf-8">
    <!-- importer le fichier de style -->
    <link rel="stylesheet" href="css/GS.css" media="screen" type="text/css" />
</head>
<header>
    <img src="asset/logo.png" width="9%">
    <FORM class="decoo">
        <input class="deco" type='submit' id='submit' value='Déconnexion' FORMACTION='Deconnexion.php' formmethod='post' width='20px'>
    </FORM>
</header>
<body>
<div id="container">
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

    //connexion à la base de données
    $db_username = $_SESSION['userbd'];
    $db_password = $_SESSION['passwordbd'];
    $db_name     = $_SESSION['db_name'];
    $db_host     = $_SESSION['db_host'];
    $db = mysqli_connect($db_host, $db_username, $db_password,$db_name)
    or die('could not connect to database');

    //on vérifie si l'utilisateur se connecte pour la première fois si oui on lui fais changer de mot de passe.;
    $requete = "SELECT Premiere_connexion 
                FROM utilisateur 
                WHERE id = $id";
    $exec_requete = mysqli_query($db,$requete);
    $reponses      = mysqli_fetch_array($exec_requete);
    if($reponses['Premiere_connexion']==0){
        echo("<form action='nouveauMotDePasse.php' method='POST'>
                <p>Veuillez rentrer un nouveau mot de passe différent de 0000/1111 ect</p>
                    <label>
                        <b> Mot de passe </b>
                            <input type='password' placeholder='Entrer votre mot de passe ' name='password' size='65px' required><br>
                            <input type='submit' id='submit' value='Valider' height='65px'>
                    </label>
              </form>");
    }else {

        //requete pour obtenir les secteurs
        $requete      ="SELECT id,Libelle 
                        FROM t_secteur";
        $exec_requete =mysqli_query($db,$requete);
        $secteurs     =mysqli_fetch_all($exec_requete);
        foreach ($secteurs as $secteur){
            echo("<form action='secteur.php?ids=$secteur[0]' method='POST'>
                    <label>
                        <input type='submit' id='identifiant3' value='$secteur[1]'>
                    </label>
                  </form><br>");
        }
    }
    ?>

</div>
</body>
</html>