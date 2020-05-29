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
            case $_POST['function'] == 'getEvents': 
				return getEvents(createPDO());
            break;
            case $_POST['function'] == 'getEventInfo': 
				return getEventInfo(createPDO(), $_POST['eid']);
            break;
            case $_POST['function'] == 'getAvailablePlayers': 
				return getAvailablePlayers(createPDO(), $_POST['eid']);
            break;
            case $_POST['function'] == 'getPlayersRegisteredForEvent': 
				return getPlayersRegisteredForEvent(createPDO(), $_POST['eid']);
            break;
            case $_POST['function'] == 'unregisterFromEvent': 
				return unregisterFromEvent(createPDO(), $_POST['iid']);
			break;
            case $_POST['function'] == 'getPlayerFavorite': 
				return getPlayerFavorite(createPDO(), $_POST['aid']);
            break;
            case $_POST['function'] == 'unsetFromFavorite': 
				return unsetFromFavorite(createPDO(), $_POST['aid'], $_POST['fid']);
            break;
            case $_POST['function'] == 'getEveryMembers': 
				return getEveryMembers(createPDO(), $_POST['except']);
            break;
            case $_POST['function'] == 'addToFavorite': 
				return addToFavorite(createPDO(), $_POST['aid'], $_POST['fid']);
			break;
        }

}



function connexion($PDO, $licenseId, $pass)
{
    $pass = md5($pass);
    $requete = "SELECT *
                FROM adherent
                WHERE numeroLicense = $licenseId && password = '$pass'";
                    
    $curseur = $PDO->prepare($requete);
    $curseur ->execute();
    
    $unClient = $curseur->fetch();
    
    echo json_encode($unClient);
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
            print_r($curr);
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
                IMP          [7] => FALSE

            Tournoi:
                Apero        [8] => FALSE
                Repas        [9] => FALSE
                DC           [10] => FALSE

            Compétition: 
                Catégorie    [8] =>  Open
                Division     [9] =>  Couipe du Comite
                Stade        [10] => Finale
                Public       [11] =>  Seniors

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
                        (`id`, `titre`, `prix`, `dteDebut`, `dteFin`, `lieu`, `type`, `paires`) VALUES 
                        (NULL, '$title', NULL, '$startDate', '$endDate', '1', $type, $curr[5]);";

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
                                    (`id`, `evenementId`, `repas`, `apero`, `imp`, `niveauRequis`, `DC`) 
                                    VALUES (NULL, '$eventId', '$curr[9]', '$curr[8]', '$curr[7]', '$curr[6]', '$curr[10]');";
                break; 

                case 2:
                    // partie libre
                    $secondStep .= "INSERT INTO `partieLibre` 
                                    (`id`, `evenementId`, `niveauRequis`)
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
                if ($data[7]) $title .= "IMP"; else $title .= "PTT";
                $title .= " par " . $data[5];
            }

        break; 

        case 3:
            // compétition
            $title = "$data[9] $data[11] $data[8] par $data[5] $data[10]";
            
        break;

    }


    return $title;
}

function getEvents($PDO)
{

    $data = array();

    $query = "SELECT * FROM evenement 
              ORDER BY id";

    $statement = $PDO->prepare($query);
    $statement->execute();

    $result = $statement->fetchAll();
    
    /*

            [id] => 27
            [titre] => Tournoi en IMP par 2
            [prix] => 
            [dteDebut] => 2020-06-01 09:00:00
            [dteFin] => 2020-06-01 21:00:00
            [lieu] => 1
            [type] => 1
            [paires] => 0

    */

    foreach($result as $row)
    {
        $data[] = array(
            'id'   => $row["id"],
            'title'   => $row["titre"],
            'start'   => $row["dteDebut"],
            'end'   => $row["dteFin"]
        );
    }

    echo json_encode($data);
    
}

function getEventInfo($PDO, $eid)
{

    $req = "SELECT * , L.adresse, L.commune 
            FROM `evenement` E 

            INNER JOIN lieu L
             ON L.id = E.lieu 
             
             WHERE E.`id` = $eid";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();
    
    $common = $curseur->fetch();

    switch ($common['type'])
    {
        case 1:
            // Tournoi
            $req = "SELECT * 
                    FROM `tournoi` 
                    WHERE `evenementId` = " . $common[0];
        break;

        case 2:
            // Parties Libres
            $req = "SELECT * 
                    FROM `partieLibre` 
                    WHERE `evenementId` = " . $common[0];
        break;

        case 3:
            // Compétition
            $req = "SELECT * 
                    FROM `competition` 
                    WHERE `evenementId` = " . $common[0];
        break;

    }

    $curseur = $PDO->prepare($req);
    $curseur ->execute();
    
    $common += $curseur->fetch();

    echo json_encode($common);
}

function getAvailablePlayers($PDO, $eid)
{


    // Recupère les adherents qui ne sont pas inscrits à l'évenement 
    $req = "SELECT * , L.adresse, L.commune 
            FROM `evenement` E 

            INNER JOIN lieu L
             ON L.id = E.lieu 
             
             WHERE E.`id` = $eid";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();
    
    $common = $curseur->fetch();
 
}

function getPlayersRegisteredForEvent($PDO, $eid)
{

    $req = "SELECT I.id as NumPaire,A.id, A.nom, A.prenom 
            FROM `inscrire` I
            
            INNER JOIN adherent A
            ON A.id IN(`adherent`, `partenaire1`, `partenaire2`, `partenaire3`)
            
            WHERE `evenementId` = $eid;
            ORDER BY I.id";
            
    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    echo json_encode($curseur->fetchAll());


}

function unregisterFromEvent($PDO, $iid)
{

    $req = "DELETE 
            FROM `inscrire` 
            WHERE `inscrire`.`id` = $iid";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();
    
    echo $curseur->errorInfo()[2];
}

function getPlayerFavorite($PDO, $aid)
{
    $req = "SELECT idFavoris, nom, prenom
            FROM `favoris` 
            
            INNER JOIN adherent
            ON adherent.id = `idFavoris`
            
            WHERE idAdherent = $aid";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    echo json_encode($curseur->fetchAll());
}

function unsetFromFavorite($PDO, $aid, $fid)
{

    $req = "DELETE 
            FROM `favoris`
            
            WHERE `idAdherent` = $aid && `idFavoris` = $fid";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    echo $curseur->errorInfo()[2];
}

function addToFavorite($PDO, $aid, $fid)
{
    $req = "INSERT INTO `favoris` 
            (`idAdherent`, `idFavoris`) 
            VALUES ($aid, $fid)";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    echo $curseur->errorInfo()[2];

}

function getEveryMembers($PDO, $except)
{


    // Liste des ids déjà dans la liste favoris
        $except = json_decode($except, true);


    // 1. Parse pour récupérer que les ids
        $notThem = "";

        for ($i=0; $i < sizeof($except); $i++) { 
            $notThem .= $except[$i][0] . ",";
        }
    
    $notThem = substr($notThem, 0, -1);
  

    // 2. Exectute la requête qui n'inclura pas les except
    $req = "SELECT * 
            FROM `adherent` 
            
            WHERE `id` NOT IN ($notThem)";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    echo json_encode($curseur->fetchAll());


}

?>