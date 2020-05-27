<?php

include('sql.php');
include('../php/utils.php');

if (isset($_POST['function']))
{

		switch(true)
		{

			case $_POST['function'] == 'login': 
				return connexion(createPDO(), $_POST['licenseId'], $_POST['pass']);
            break;
            case $_POST['function'] == 'importEvents': 
				return importEvents(createPDO(), $_POST['events'], $_POST['type']);
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

function importEvents($PDO, $evenements, $type)
{
    $req = "";
    $curr= "";
    $secondStep = "";

    // We skip one cause it's the header
    for ($i=1; $i < sizeof($evenements['data']) -1 ; $i++) { 
        
        // Skip ",,,,"
            if ($evenements['data'][$i][0] == ",,,,,") continue;

        // Pour chaque ligne on coupe à chaque virgule
            $curr = explode(',', $evenements['data'][$i][0]);

        /* 

            Common:  
                Dte début    [0] => 1/6/2020
                Her début    [1] => 9:00
                Dte fin      [2] => 1/6/2020
                Her fin      [3] => 21:00
                Lieu         [4] => Poitiers
                Paires       [5] => 2

            Tournoi / Compétition: 
                Niveau       [6] => B27
                Apero        [7] => FALSE
                Repas        [8] => FALSE
                IMP          [9] => FALSE

            Compétition: 
                Catégorie    [10] =>  Open
                Division     [11] =>  Couipe du Comite
                Stade        [12] => Finale
                Public       [13] =>  Seniors
                        
            #type 
                0 : évenement
                1 : tournoi
                2 : partie libre
                3 : compétition
        */
        
        // 1. Ajoute l'évenement 


            // Checking whether or not the time format, format should be: HH:MM:SS
                $curr[1] = correctTimeFormat($curr[1]);
                $curr[3] = correctTimeFormat($curr[3]);

            // Parse the date to MySQL format switching dd/mm/yyyy to yyyy/mm/dd
                $startDate = correctDateTimeFormat($curr[0], $curr[1]);
                $endDate = correctDateTimeFormat($curr[2], $curr[3]);

            // Build the title
                $title = makeEventTile($curr, $type);

            // Parse each items and add to the request

                $req = "INSERT INTO `evenement` 
                        (`id`, `titre`, `prix`, `dteDebut`, `dteFin`, `lieu`, `type`) VALUES 
                        (NULL, '$title', NULL, '$startDate', '$endDate', '1', $type);";

            // Getting the evenement id

                    $curseur = $PDO->prepare($req);
                    $curseur ->execute();

                    $eventId = $PDO->lastInsertId();
            
        // 2. Décide de si l'évenement est un tournoi, une compétition ou bien une partie libre et agis en fonction

            switch ($type)
            {

                case 1:
                    // tournoi
                    $secondStep .= "INSERT INTO `tournoi` 
                                    (`id`, `evenementId`, `repas`, `apero`, `imp`, `niveauRequis`) 
                                    VALUES (NULL, '$eventId', '$curr[7]', '$curr[6]', '$curr[9]', '$curr[6]');";
                break; 

                case 2:
                    // partie libre
                    $secondStep .= "INSERT INTO `partieLibre` 
                                    (`id`, `idEvenement`, `niveauRequis`)
                                    VALUES (NULL, '$eventId', '$curr[6]');";
                break; 
                
                case 3:
                    // compétition

                    $secondStep .= "INSERT INTO `competition` 
                                    (`id`, `evenementId`, `catComp`, `division`, `stade`, `public`) 
                                    VALUES (NULL, '$eventId', 1, 1, 1, 1);";
                    echo $req . "\n";
                break;

            }

        }



    $curseur = $PDO->prepare($secondStep);
    $curseur ->execute();

    $curseur->fetch();
    // In case, respons with error message, it should be empty
    echo $curseur->errorInfo()[2];
    $curseur = null;


}

function makeEventTile($data, $type)
{

    /*
        Syntaxe pour les compétitions:
            [DIVISION][PUBLIC][CATEGORIE] par [PAIRES][STADE]
    
        Syntaxe tournoi:
            Tournoi en [IMP] par [PAIRES]
            Si niveau < 4T = débutant
                Tournoi débutant

        Syntaxe partie libre:
            Partie Libre

        Syntaxe données commentées
            Donnéees commentées

    */


    $title = "Partie Libre";

    switch ($type)
    {

        case 1:
            // tournoi

            $title = "Tournoi Débutant";
            if ($data[6] != "4T")
            {
                $title = "Tournoi en ";
                if ($data[9]) $title .= "IMP"; else $title .= "PTT";
                $title .= " par " . $data[5];
            }

        break; 
        
        case 3:
            // compétition
            $title = "$data[11] $data[13] $data[10] par $data[5] $data[12]";
            
        break;

    }


    return $title;
}

/*
function getEvents($PDO)
{

    $data = array();

    $query = "SELECT * FROM evenement ORDER BY id";

    $statement = $PDO->prepare($query);

    $statement->execute();

    $result = $statement->fetchAll();

    foreach($result as $row)
    {
    $data[] = array(
    'id'   => $row["id"],
    'title'   => $row["title"],
    'start'   => $row["start_event"],
    'end'   => $row["end_event"]
    );
    }

    echo json_encode($data);
        
}
*/
?>