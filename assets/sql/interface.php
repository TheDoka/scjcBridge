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
            case $_POST['function'] == 'importEvents': 
				return importEvents(createPDO(), $_POST['events']);
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

function importEvents($PDO, $evenements)
{

    $req = "";
    $curr= "";

    // We skip one cause it's the header
    for ($i=1; $i < sizeof($evenements['data']) -1 ; $i++) { 
        
        // Skip ",,,,"
            if ($evenements['data'][$i][0] == ",,,,,") continue;

        // Pour chaque ligne on coupe à chaque virgule
            $curr = explode(',', $evenements['data'][$i][0]);
            
        /*

            Titre                   [0] => Coupe du comité Royan
            Date début              [1] => 27/6/2020
            Heure de début          [2] => 13:30
            Date fin                [3] => 28/6/2020
            Heure de fin            [4] => 21:00
            Type                    [5] => 1
            Lieu                    [6] => 1
            Apero                   [7] => False
            Repas                   [8] => False
            Données commentées      [9] => FALSE
            IMP                     [10] => TRUE
            Paires                  [11] => 1
            Niveau Requis           [12] => B27
            Catégorie compétition   [13] => 1
            Serie compétition       [14] => 
            Stade compétition       [15] => Finale
            Public compétition      [16] => Open
                            
            #type 
            0 : évenement
            1 : tournoi
            2 : partie libre
            3 : compétition
        */
        
        // 1. Ajoute l'évenement 


            // Checking whether or not the time format, format should be: HH:MM:SS
                $curr[2] = correctTimeFormat($curr[2]);
                $curr[4] = correctTimeFormat($curr[4]);

            // Parse the date to MySQL format switching dd/mm/yyyy to yyyy/mm/dd
                $startDate = correctDateTimeFormat($curr[1], $curr[2]);
                $endDate = correctDateTimeFormat($curr[3], $curr[4]);
                
            // Parse each items and add to the request

                $req .= "INSERT INTO `evenement` 
                        (`id`, `titre`, `prix`, `dteDebut`, `dteFin`, `lieu`) VALUES 
                        (NULL, '$curr[0]', NULL, '$startDate', '$endDate', '$curr[6]');";
      
        // 2. Décide de si l'évenement est un tournoi, une compétition ou bien une partie libre

            switch ($curr[5])
            {
                case 0:
                    // evenement
                break;

                case 1:
                    // tournoi
                break; 

                case 2:
                    // partie libre
                break; 
                
                case 3:
                    // compétition
                break;

            }
            
    
        }

    // Exec req
    $curseur = $PDO->prepare($req);
    $curseur ->execute();
    
    $result = $curseur->fetch();
    
    // In case, respons with error message, it should be empty
    echo $curseur->errorInfo()[2];

    $curseur = null;


}

function correctTimeFormat($data)
{

    // Checking whether or not the time format, format should be: HH:MM:SS
        if (sizeof(explode(':', $data)) < 3) // If string does not match any ss:ss:ss
        {
            return $data . ":00";
        }

}

function correctDateTimeFormat($date, $time)
{
    // Parse the date to MySQL format switching dd/mm/yyyy to yyyy/mm/dd
        $tmp = explode('/', $date);
        return "$tmp[2]-$tmp[1]-$tmp[0] $time";
}


?>