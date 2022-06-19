<?php
session_start();

// voit avec antoine la config du serveur WAMP

$db_username = $_SESSION['userbd'];
$db_password = $_SESSION['passwordbd'];
$db_name     = $_SESSION['db_name'];
$db_host     = $_SESSION['db_host'];
$db = mysqli_connect($db_host, $db_username, $db_password,$db_name)
or die('could not connect to database');

$idams=$_GET['idams'];
$requete="SELECT Email FROM utilisateur WHERE id=$idams";
$exec_requete=mysqli_query($db,$requete);
$emails=mysqli_fetch_array($exec_requete);
$email=$emails['Email'];

$id=$_SESSION['id'];
$prenom=$_SESSION['prenom'];
$nom=$_SESSION['nom'];
$type=$_SESSION['type'];
//on vérifie que l'utilisateur est bien connecté sinon on le renvoit se connecter
if ($id == null or $type != 'GS') {
    header("Location: index.php?id=$id");
}

$to      = $email;
$subject = 'le sujet';
$message = 'Bonjour !';
$headers = array(
    'From' => 'patrick62110@hotmail.com',
    'X-Mailer' => 'PHP/' . phpversion()
);



mail('patrick.plouvinpro@gmail.com', 'Mon Sujet', 'tentative',$headers);

//header('Location: GS.php');