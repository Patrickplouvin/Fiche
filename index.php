<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <!-- importer le fichier de style -->
    <link rel="stylesheet" href="css/index.css" media="screen" type="text/css" />
    <title>Fiche Sécurité</title>
</head>
<body>
<header><img src="asset/logo.png" width="8%"></header>
<div id="container">

    <!-- zone de connexion -->

    <form action='verification.php' method="POST">
        <h1>Connexion</h1>

        <label><b>Id utilisateur</b>
        <input type="text" id='identifiant2' placeholder="Entrer votre id" name="id" required>
        </label>
        <br>
        <br>
        <label><b>Mot de passe</b>
        <input type="password" id='identifiant2' placeholder="Entrer votre mot de passe" name="password" required >
        </label>
        <br>
        <br>
        <input type="submit" id='submit' value='Se connecter'>
        <?php
        if(isset($_GET['erreur'])){
            $err = $_GET['erreur'];
            if($err==1 || $err==2)
                echo ("<p style='color:red'>Utilisateur ou mot de passe incorrect</p>");
        }
        ?>
    </form>
</div>
</body>
</html>
