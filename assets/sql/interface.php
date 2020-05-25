<?php

include('sql.php');

function createPDO()
{
    
    try {
        $pdo = new PDO("mysql:host=localhost; dbname=scjcBridge", "root", "root");
    } catch (PDOException $e) {
        die();
    }
        
    return $pdo;

}

if (isset($_POST['function']))
{

		switch(true)
		{

			case $_POST['function'] == 'login': 
				return connexion(createPDO(), $_POST['licenseId'], $_POST['pass']);
			break;
            
        }

}



function connexion($PDO, $licenseId, $pass)
{
    $pass = md5($pass);
    $requete = "SELECT id
                FROM adherent
                WHERE numeroLicense = $licenseId && password = '$pass'";
                    
    $curseur = $PDO->prepare($requete);
    $curseur ->execute();
    
    $unClient = $curseur->fetch();
    
    echo $unClient[0];
    $curseur = null;
    
}


?>