<?php

include('sql.php');
include(dirname(__FILE__) . '/../php/utils.php');

if (isset($_POST['function']))
{
        /*
            Global
        */

        $site_url = "http://aweebsserver.ddns.net:1500/scjcBridge/";

		switch($_POST['function'])
		{

			case  'login': 
				return connexion(createPDO(), $_POST['licenseId'], $_POST['pass']);
            break;
            case  'getUser': 
				return getUser(createPDO(), $_POST['aid']);
            break;
            case 'importEvents': 
				return importEvents(createPDO(), $_POST['events'], $_POST['type']);
            break;
            case 'getEvents': 
				return getEvents(createPDO());
            break;
            case 'getEventInfo': 
				return getEventInfoInterface(createPDO(), $_POST['eid']);
            break;
            case 'getAvailablePlayers': 
				return getAvailablePlayers(createPDO(), $_POST['eid']);
            break;
            case 'getPlayersRegisteredForEvent': 
				return getPlayersRegisteredForEvent(createPDO(), $_POST['eid']);
            break;
            case 'unregisterFromEvent': 
				return unregisterFromEvent(createPDO(), $_POST['iid']);
			break;
            case 'getPlayerFavorite': 
				return getPlayerFavorite(createPDO(), $_POST['aid'], $_POST['except']);
            break;
            case 'unsetFromFavorite': 
				return unsetFromFavorite(createPDO(), $_POST['aid'], $_POST['fid']);
            break;
            case 'getEveryMembers': 
				return getEveryMembers(createPDO(), $_POST['except']);
            break;
            case 'addToFavorite': 
				return addToFavorite(createPDO(), $_POST['aid'], $_POST['fid']);
            break;
            case 'registerToEventWith': 
				return registerToEventWith(createPDO(), $_POST['aid'], $_POST['eid'], $_POST['ids']);
            break;
            case 'registerSOSpartenaire':
                return registerSOSpartenaire(createPDO(), $_POST['aid'], $_POST['eid']);
            break;
            case 'unregisterSOSpartenaire':
                return unregisterSOSpartenaire(createPDO(), $_POST['players'], $_POST['eid']);
            break; 
            case 'isRegisteredForSOSpartenaire':
                return isRegisteredForSOSpartenaire(createPDO(), $_POST['aid'], $_POST['eid']);
            break;
            case 'getPlayersRegisteredForSOSpartenaire':
                return getPlayersRegisteredForSOSpartenaire(createPDO(), $_POST['eid'], $_POST['aid']);
            break;
            case 'sendMail':
                require_once('../php/mail.php');
                return sendMail($_POST['mailContent']);
            break;
            case 'createRegistrationNotificationMailForEvent':
                return createRegistrationNotificationMailForEvent(createPDO(), $_POST['eid'], $_POST['ids']);
            break;
            case 'createUnRegistrationNotificationMailForEvent':
                return createUnRegistrationNotificationMailForEvent(createPDO(), $_POST['eid'], $_POST['ids']);
            break;
            case 'getMembersFromIIDForEvent':
                return getMembersFromIIDForEvent(createPDO(), $_POST['iid']);
            break;
            case 'quickInsertUserAndFav':
                return quickInsertUserAndFav(createPDO(), $_POST['aid'], $_POST['lastname'], $_POST['name'], $_POST['mail'], $_POST['license']);
            break;
            case 'getAllPlayersRegistrations':
                return getAllPlayersRegistrations(createPDO(), $_POST['aid']);
            break;
            case 'updateEventDate':
                return updateEventDate(createPDO(), $_POST['eid'], $_POST['startDate'], $_POST['endDate']);
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

function getUser($PDO, $aid)
{
    $req = "SELECT `id`,`nom`,`prenom`,`mail`,`tel`,`commune`,`sexe`,`password`,`numeroLicense`, S.libelle as statut, N.numeroSerie as Niveau
            FROM adherent A
            
            INNER JOIN statut S
            ON A.idStatut = S.idStatut
            
            INNER JOIN niveau N
            ON A.idNiveau = N.idNiveau
            
            WHERE A.id = $aid";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();
    
    $unClient = $curseur->fetch();
    
    echo json_encode($unClient);
}

function importEvents($PDO, $evenements, $type)
{
    $req = "";
    $curr= "";
    $secondStep = "";

    // We skip one cause it's the header
    for ($i=1; $i < sizeof($evenements['data']) -1 ; $i++) { 
        
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

    $result = [];
    $autres = [];

    $query = "SELECT * 
            FROM `evenement` E
            
            INNER JOIN competition C 
            ON E.`id` = C.evenementId;
            

            SELECT * 
            FROM `evenement` E

            INNER JOIN tournoi C 
            ON E.`id` = C.evenementId;
            

            SELECT * 
            FROM `evenement` E

            INNER JOIN partieLibre C 
            ON E.`id` = C.evenementId;";

    $statement = $PDO->prepare($query);
    $statement->execute();

    $result = $statement->fetchAll();
    
    for ($i=0; $i < 2; $i++) { 
        $statement->nextRowset();
        $result = array_merge($result, $statement->fetchAll());
    }

    $except = getFormatedIds($result);

    $query = "SELECT * 
            FROM `evenement` 
            WHERE `id` NOT IN ($except);";

    $statement = $PDO->prepare($query);
    $statement->execute();

    $result = $result = array_merge($result, $statement->fetchAll());

    echo json_encode($result);
    
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

            $req = "SELECT CC.`id`, CC.`evenementId`, C.libelle AS catComp, D.libelle AS division, S.libelle as stade, P.libelle as public
                    FROM `competition` CC
                                        
                    INNER JOIN categorieCompetition C
                    ON `catComp` = C.id
                    
                    INNER JOIN division D
                    ON `division` = D.id
                    
                    INNER JOIN stade S
                    ON `stade` = S.id
                    
                    INNER JOIN public P
                    ON `public` = P.id 
                    
                    WHERE `evenementId` = " . $common[0];
        break;

    }

    $curseur = $PDO->prepare($req);
    $curseur ->execute();
    
    $common += $curseur->fetch();

    return $common;
}

function getEventInfoInterface($PDO, $eid)
{
    // Façon moche de permettre de reutiliser la fonction sans Jquery
    echo json_encode(getEventInfo($PDO, $eid));
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

    $req = "SELECT A.id, I.id as NumPaire, A.nom, A.prenom 
            FROM `inscrire` I
            
            INNER JOIN adherent A
            ON A.id IN(`adherent`, `partenaire1`, `partenaire2`, `partenaire3`)
            
            WHERE `evenementId` = $eid;
            ORDER BY I.id";
            
    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    echo json_encode($curseur->fetchAll());


}

function registerToEventWith($PDO, $aid, $eid, $joueursID)
{
    
    $joueursID = json_decode($joueursID);

    if (sizeof($joueursID) == 1)
    {
        $joueursID[1] = 'NULL';
        $joueursID[2] = 'NULL';
    }

    $req = "INSERT INTO `inscrire` 
            (`id`, `evenementId`, `adherent`, `partenaire1`, `partenaire2`, `partenaire3`) 
            VALUES (NULL, '$eid', '$aid', $joueursID[0], $joueursID[1], $joueursID[2]);";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    echo $curseur->errorInfo()[2];

    
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

function unregisterFromEventComplex($PDO, $aid, $eid)
{

    $req = "DELETE 
            FROM `inscrire` 
            WHERE `adherent` = $aid && `evenementId` = $eid";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    echo $curseur->errorInfo()[2];
}

function getPlayerFavorite($PDO, $aid, $except)
{
    // Liste des ids déjà dans la liste favoris
    $except = json_decode($except, true);
    
    // 1. Parse pour récupérer que les ids
        $notThem = getFormatedIds($except);

    $req = "SELECT idFavoris, nom, prenom
            FROM `favoris` 
            
            INNER JOIN adherent
            ON adherent.id = `idFavoris`
            
            WHERE idAdherent = $aid";

    if ($notThem)
    {
        $req .= " && `idFavoris` NOT IN ($notThem)";
    }

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
        $notThem = getFormatedIds($except);

    // 2. Exectute la requête qui n'inclura pas les except
    $req = "SELECT * 
            FROM `adherent`";

    if ($notThem)
    {
        $req .= " WHERE `id` NOT IN ($notThem)";
    }

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    echo json_encode($curseur->fetchAll());


}

function getFormatedIds($except)
{

    // 1. Parse pour récupérer que les ids
    $notThem = "";

    for ($i=0; $i < sizeof($except); $i++) { 
        $notThem .= $except[$i][0] . ",";
        
    }

    $notThem = substr($notThem, 0, -1);

    // "0, 1, 2, 3, 4"
    return $notThem;
}

function getLowFormatedIds($except)
{

    // 1. Parse pour récupérer que les ids
    $notThem = "";

    for ($i=0; $i < sizeof($except); $i++) { 
        $notThem .= $except[$i] . ",";
       
    }

    $notThem = substr($notThem, 0, -1);

    // "0, 1, 2, 3, 4"
    return $notThem;
}

function registerSOSpartenaire($PDO, $aid, $eid)
{

    $req = "INSERT INTO `sos`
            (`idAdherent`, `evenementId`) 
            VALUES ($aid, $eid)";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    echo $curseur->errorInfo()[2];


}

function unregisterSOSpartenaire($PDO, $jouersId, $eid)
{

    $jouersId = json_decode($jouersId);

    $jouersId = getLowFormatedIds($jouersId);

    $req = "DELETE 
            FROM `sos` 
            WHERE `idAdherent` IN ($jouersId) && `evenementId` = $eid";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();
    
    echo $curseur->errorInfo()[2];

}

function isRegisteredForSOSpartenaire($PDO, $aid, $eid)
{
    $req = "SELECT * 
            FROM `sos`
            WHERE `idAdherent` = $aid && `evenementId` = $eid";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    echo json_encode($curseur->fetchAll());

}

function getPlayersRegisteredForSOSpartenaire($PDO, $eid, $aid)
{
    $req = "SELECT A.id, A.nom, A.prenom
            FROM `sos` S
            
            INNER JOIN adherent A
            ON S.`idAdherent` = A.`id`
            
            WHERE `evenementId` = $eid && A.`id` != $aid";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    echo json_encode($curseur->fetchAll());

}
 
function getPlayersInfo($PDO, $ids)
{

    $req = "SELECT * 
            FROM adherent
            WHERE id IN ($ids)";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();
    
    return $curseur->fetchAll();
}

function getMembersFromIIDForEvent($PDO, $iid)
{

    $req = "SELECT A.id, A.nom, A.prenom 
            FROM `inscrire` I
            
            INNER JOIN adherent A
            ON A.id IN(`adherent`, `partenaire1`, `partenaire2`, `partenaire3`)

            WHERE I.`id` = $iid";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();
    
    echo json_encode($curseur->fetchAll());

}

function createRegistrationNotificationMailForEvent($PDO, $eid, $ids)
{

    /*

        Subject: Notification d'inscription
        Body: 
            Vous venez d'être inscrit au [TITRE].
            Vous êtes inscrits avec: 
                * NOM prenom
                * NOM prenom
        Cliquer ici pour refuser/annuler: [LIEN]
        
    */

    // Récupère informations du réfèrent
    // [lastOne]

    
    $ids = getLowFormatedIds($ids);
    $playersInfos = getPlayersInfo($PDO, $ids);
    $referant = $playersInfos[0];
    

    $toMails = [];
    $players = "";
    foreach ($playersInfos as $key => $player) {
        array_push($toMails, $player['mail']);
        $players .= $player['nom'] . " " . $player['prenom'] . "\n";
    }

    // Recupère les information de l'évenement:
    $eventInfo = getEventInfo($PDO, $eid);

    $content[] = array(
        'subject' => "Notification d'inscription",
        'body' => nl2br(
            " Bonjour, \n" .
            " Vous venez d'être inscrit de '" . $eventInfo['titre'] . "' par: " . $referant['nom'] . ' ' . $referant['prenom'] . "\n". 
            " Date/Heure: ". $eventInfo['dteDebut']. "\n" . 
            " Membres de la paire: \n $players \n" . 
            " Si vous souhaiter refuser ou annuler l'inscription cliquez ici: " . createUnregistrationLinkForEvent($referant[0], $referant['nom'], $eid). ".".
            " Ou rendez vous sur l'interface."
        ),
        'to' => $toMails,
    );

    echo json_encode($content);


}

function createUnRegistrationNotificationMailForEvent($PDO, $eid, $ids)
{

    global $site_url;
    
    $ids = getFormatedIds($ids);
    $playersInfos = getPlayersInfo($PDO, $ids);
    $referant = $playersInfos[0];

    $toMails = [];
    $players = "";
    foreach ($playersInfos as $key => $player) {
        array_push($toMails, $player['mail']);
        $players .= $player['nom'] . " " . $player['prenom'] . "\n";
    }

    // Recupère les information de l'évenement:
    $eventInfo = getEventInfo($PDO, $eid);
    
    $content[] = array(
        'subject' => "Notification de desinscription",
        'body' => nl2br(
            " Bonjour, \n" .
            " Vous venez d'être désinscrit de '" . $eventInfo['titre'] . "' par: " . $referant['nom'] . ' ' . $referant['prenom'] . "\n". 
            " Les joueurs suivants ont été desinscrits: \n " . $players. "\n" . 
            " Pour inspecter l'évenement cliquer ici: " . $site_url . "inscription.php?eid=" . $eid
        ),
        'to' => $toMails,
    );

    echo json_encode($content);

}

function createUnregistrationLinkForEvent($aid, $nom, $eid)
{
    /*
        Devra être changer par la vraie url.
        http://aweebsserver.ddns.net:1500/scjcBridge/api.php?token=1693114948b55af09711300df7ae2822&f=unre&eid=45

        On créer le lien de désinscription.
        Actuellement ce lien est un lien 'personnel' le token est créer avec les informations utilisateur.
        Le plus propre, serait de créer un lien de 'groupe', qui supprime l'inscription avec son id et non avec un laison utilisateur.
        Après il resterait le même problème de sécurité, je serai obligé de créer un token pour authentifier l'utilisateur et sa demande,
        alors, quoi qu'il arrive ces informations seront dans la requête. Donc c'est peu important.


    */

    global $site_url;

    $link = $site_url . "api.php?token=" . md5($aid . $nom) . "&f=unre&eid=" . $eid;
    return $link;

}

function quickInsertUserAndFav($PDO, $aid, $lastname, $name, $mail, $license)
{
    $req = "INSERT INTO `adherent` 
            (`id`, `nom`, `prenom`, `mail`, `tel`, `commune`, `sexe`, `password`, `numeroLicense`, `idStatut`, `idNiveau`) 
            VALUES (NULL, '$lastname', '$name', '$mail', NULL, '', '', '', '$license', 4, 1);
            ";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();
    $fid = $PDO->lastInsertId();

    addToFavorite($PDO, $aid, $fid);

    echo $curseur->errorInfo()[2];

}

function getAllPlayersRegistrations($PDO, $aid)
{

    $req = "SELECT `evenementId`
            FROM `inscrire`
            WHERE `adherent` = $aid";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    echo json_encode($curseur->fetchAll());

}

function updateEventDate($PDO, $eid, $startDate, $endDate)
{

    $req = "UPDATE `evenement` 
            SET `dteDebut` = '$startDate',
                `dteFin` = '$endDate'
                

            WHERE `evenement`.`id` = $eid;";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();
    
    echo $curseur->errorInfo()[2];

}

?>