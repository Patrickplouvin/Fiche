<html>
<head>
    <meta charset="utf-8">
    <!-- importer le fichier de style -->
    <link rel="stylesheet" href="css/AMS.css" media="screen" type="text/css" />
</head>
<div id="content">
    <?php
    session_start();

    $msg=$_GET['msg'];
    $id=$_SESSION['id'];
    $prenom=$_SESSION['prenom'];
    $nom=$_SESSION['nom'];

    // connexion à la base de données
    $db_username = $_SESSION['userbd'];
    $db_password = $_SESSION['passwordbd'];
    $db_name     = $_SESSION['db_name'];
    $db_host     = $_SESSION['db_host'];
    $db = mysqli_connect($db_host, $db_username, $db_password,$db_name)
    or die('could not connect to database');

    //on vérifie que l'utilisateur est bien connecté sinon on le renvoit se connecter
    if ($id == null) {
        header("Location: index.php?id=$id");
    }

    //on vérifie si l'utilisateur se connecte pour la première fois si oui on lui fait changer de mot de passe
    $requete = "SELECT Premiere_connexion FROM utilisateur where id = $id";
    $exec_requete = mysqli_query($db,$requete);
    $reponses      = mysqli_fetch_array($exec_requete);
    if($reponses['Premiere_connexion']==0){
        echo("<form action='nouveauMotDePasse.php' method='POST'>
                <p>Veuillez rentrer un nouveau mot de passe différent de 0000/1111 ect</p>
                    <label>
                        <b> Mot de passe </b>
                    </label>
                    <input type='password' placeholder='Entrer votre mot de passe ' name='password' size='25' required><br>
                    <input type='submit' id='submit' value='Valider' height='65px'></form>");
    }else {
        echo("<h1>" . $prenom . " " . $nom . "<h1><br>");
        if($msg==="1"){
            echo("<p>Nouveau enregistrement créé avec succès !</p><br>");
        }elseif ($msg==="2"){
            echo("<p>Fiche complétée et enregistrée avec succès !</p><br>");
        }elseif ($msg==="3"){
            echo ("<p>La Fiche existe déjà !</p><br>");
        }
        //Création des deux boutons // modifier le deuxième boutton
        echo("<div id='deux'>
                    <form action='ficheEnTete.php' method='POST'>
                           <label>
                                <input id='identifiant2' type='submit' value='Créer une fiche de sécurité'>
                           </label>
                    </form>
                    <form action='ficheQuestion.php' method='POST'>
                        <label>
                                <input id='identifiant2' type='submit' value='Accedez à vos fiches en cours'>
                        </label>
                    </form>
               </div>");
        mysqli_close($db);
    }
    ?>
    <FORM class="decoo">
        <input class="deco" type='submit' id='submit' value='Déconnexion' FORMACTION='Deconnexion.php' formmethod='post' width='20px'>
    </FORM>

</div>
</body>
</html>
