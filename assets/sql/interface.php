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
				echo connexion(createPDO(), $_POST['licenseId'], $_POST['pass']);
            break;
            case  'getUser': 
				echo getUser(createPDO(), $_POST['aid']);
            break;
            case 'importEvents': 
				echo importEvents(createPDO(), $_POST['events'], $_POST['type']);
            break;
            case 'getEvents': 
				echo json_encode(getEvents(createPDO()));
            break;
            case 'getEventInfo': 
				echo json_encode(getEventInfo(createPDO(), $_POST['eid']));
            break;
            case 'getLieux': 
				echo json_encode(getLieux(createPDO()));
            break;
            case 'getAvailablePlayers': 
				return getAvailablePlayers(createPDO(), $_POST['eid']);
            break;
            case 'getPlayersRegisteredForEvent': 
				echo json_encode(getPlayersRegisteredForEvent(createPDO(), $_POST['eid']));
            break;
            case 'unregisterFromEvent': 
				echo unregisterFromEvent(createPDO(), $_POST['iid']);
			break;
            case 'getPlayerFavorite': 
				echo getPlayerFavorite(createPDO(), $_POST['aid'], $_POST['except']);
            break;
            case 'unsetFromFavorite': 
				echo unsetFromFavorite(createPDO(), $_POST['aid'], $_POST['fid']);
            break;
            case 'getEveryMembers': 
				echo getEveryMembers(createPDO(), $_POST['except']);
            break;
            case 'addToFavorite': 
				echo addToFavorite(createPDO(), $_POST['aid'], $_POST['fid']);
            break;
            case 'registerToEventWith': 
				echo registerToEventWith(createPDO(), $_POST['eid'], $_POST['ids']);
            break;
            case 'registerSOSpartenaire':
                echo registerSOSpartenaire(createPDO(), $_POST['aid'], $_POST['eid']);
            break;
            case 'unregisterSOSpartenaire':
                echo unregisterSOSpartenaire(createPDO(), $_POST['players'], $_POST['eid']);
            break; 
            case 'isRegisteredForSOSpartenaire':
                echo isRegisteredForSOSpartenaire(createPDO(), $_POST['aid'], $_POST['eid']);
            break;
            case 'getPlayersRegisteredForSOSpartenaire':
                echo getPlayersRegisteredForSOSpartenaire(createPDO(), $_POST['eid'], $_POST['aid']);
            break;
            case 'sendMail':
                require_once('../php/mail.php');
                //return sendMail($_POST['mailContent']);
            break;
            case 'createRegistrationNotificationMailForEvent':
                echo json_encode(createRegistrationNotificationMailForEvent(createPDO(), $_POST['eid'], $_POST['ids']));
            break;
            case 'createUnRegistrationNotificationMailForEvent':
                echo json_encode(createUnRegistrationNotificationMailForEvent(createPDO(), $_POST['eid'], $_POST['ids']));
            break;
            case 'getMembersFromIIDForEvent':
                echo json_encode(getMembersFromIIDForEvent(createPDO(), $_POST['iid']));
            break;
            case 'quickInsertUserAndFav':
                echo quickInsertUserAndFav(createPDO(), $_POST['aid'], $_POST['lastname'], $_POST['name'], $_POST['mail'], $_POST['license']);
            break;
            case 'getAllPlayersRegistrations':
                echo json_encode(getAllPlayersRegistrations(createPDO(), $_POST['aid']));
            break;
            case 'updateEventDate':
                echo updateEventDate(createPDO(), $_POST['eid'], $_POST['startDate'], $_POST['endDate']);
            break;
            case 'deletePaireAssociatedWithIID':
                echo deletePaireAssociatedWithIID(createPDO(), $_POST['iid']);
            break;
            case 'unregisterPaire':
                echo unregisterPaire(createPDO(), $_POST['pid'], $_POST['pid']);
            break;
            case 'unregisterFromRemplacant':
                echo unregisterFromRemplacant(createPDO(), $_POST['aid'], $_POST['iid']);
            break;
            case 'registerRemplacantToPid':
                echo registerRemplacantToPid(createPDO(), $_POST['aid'], $_POST['pid']);
            break;
            case 'registerIsolees':
                echo registerIsolees(createPDO(), $_POST['eid'], $_POST['ids']);
            break;
            case 'getIIDWithPID':
                echo json_encode(getIIDWithPID(createPDO(), $_POST['pid']));
            break;
            case 'importIntoPairesIsolees':
                echo importIntoPairesIsolees(createPDO(), $_POST['eid'], $_POST['paires']);
            break;
            case 'getPairesIsoleesForEvent':
                echo json_encode(getPairesIsoleesForEvent(createPDO(), $_POST['eid'], $_POST['exceptPID']));
            break;
            case 'getMembersOfPaireIsolee':
                echo json_encode(getMembersOfPaireIsolee(createPDO(), $_POST['pid']));
            break;
            case 'getMembersOfPaire':
                echo json_encode(getMembersOfPaire(createPDO(), $_POST['pid']));
            break;
            case 'deletePaireIsole':
                echo deletePaireIsole(createPDO(), $_POST['pid']);
            break;
            case 'createPaireIsoleeNotificationMailForEvent':
                echo json_encode(createPaireIsoleeNotificationMailForEvent(createPDO(), $_POST['eid'], $_POST['ids']));
            break;
            case 'createNewPaireRemplacement':
                echo createNewPaireRemplacement(createPDO(), $_POST['iid']);
            break;
            case 'updateEvent':
                echo updateEvent(createPDO(), $_POST['event']);
            break;
            case 'deleteEvent':
                echo deleteEvent(createPDO(), $_POST['eid'], $_POST['ety']);
            break;
        }


}

/*
    TO DO: 

        * Transformer ça en fonction

            $req = "";

            $curseur = $PDO->prepare($req);
            $curseur ->execute();

            return $curseur->errorInfo()[2];    

        return sendQuery(req, type[0,1] -> return text/errorInfo);
*/

function apiUnregisterPaire($PDO, $aid, $eid)
{

    $userInfo = getRegistrationInformation($PDO, $aid, $eid);
    
    /*
        I. On récupère l'iid et le pid de l'inscription
    */
    $iid = $userInfo['iid'];
    $pid = $userInfo['pid'];

    /*
        II. Supprime la paire
    */

    unregisterPaire($PDO, $pid);

    /*
        II. On récupères les paires restantes
    */
    $pairesRestantes = getMembersFromIIDForEvent($PDO, $iid);
     
    if (sizeof($pairesRestantes) > 1)
    {
        /*
            III. Supprime le reste des paires
        */
        deletePaireAssociatedWithIID($PDO, $iid);
        
        /*
            IV. On importe les paires restante dans les paires isolées.
        */

        importIntoPairesIsolees($PDO, $eid, $pairesRestantes);

    }

    /*
        V. Supprime l'inscription
    */
    unregisterFromEvent($PDO, $iid);

}

function getRegistrationInformation($PDO, $aid, $eid)
{
    $req = "SELECT A.id, A.nom, A.prenom, I.Id as iid, P.pid as pid
            FROM `paire` P
                        
            INNER JOIN inscrire I
            ON paire1 = P.pid
            OR paire2 = P.pid
            OR remplacant = P.pid 
            
            INNER JOIN adherent A 
            ON A.id = P.adherent
            
            WHERE `evenementId` = $eid && A.id = $aid";


    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    return $curseur->fetch();
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
    $curseur = null;

    return json_encode($unClient);
    
    
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
    
    return json_encode($unClient);
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
                break;

            }

        }



    $curseur = $PDO->prepare($secondStep);
    $curseur ->execute();

    $curseur->fetch();
    // In case, respond with error message, it should be empty
    return $curseur->errorInfo()[2];
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

    $except = getFormatedIds($result, 0);

    $query = "SELECT * 
            FROM `evenement` 
            WHERE `id` NOT IN ($except);";

    $statement = $PDO->prepare($query);
    $statement->execute();

    $result = $result = array_merge($result, $statement->fetchAll());

    return $result;
    
}

function getLieux($PDO)
{


    $req = "SELECT * 
            FROM `lieu`";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    return $curseur->fetchAll();
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

    $req = "SELECT A.id, A.nom, A.prenom, I.Id as iid, P.pid as NumPaire, I.paire1, I.paire2, I.remplacant
            FROM `inscrire` I
                        
            INNER JOIN paire P
            ON paire1 = P.pid
            OR paire2 = P.pid
            OR remplacant = P.pid 
            
            INNER JOIN adherent A 
            ON A.id = P.adherent
            
            WHERE `evenementId` = $eid
            ORDER BY NumPaire";
            
    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    /*
        [0] NumPaire
        [1] Nom
        [2] Prenom
    */

    return $curseur->fetchAll();


}

function deletePaireIsole($PDO, $pid)
{
    $req = "DELETE 
            FROM `isolees` 
            WHERE `isolees`.`pid` = $pid";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    return $curseur->errorInfo()[2];

}

function getMembersOfPaireIsolee($PDO, $pid)
{

    $req = "SELECT A.id, A.nom, A.prenom
            FROM isolees 
            
            INNER JOIN adherent A
            ON A.id = adherent
            
            WHERE isolees.pid = $pid";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    return $curseur->fetchAll();
}
function getMembersOfPaire($PDO, $pid)
{

    $req = "SELECT A.id, A.nom, A.prenom
            FROM paire 
            
            INNER JOIN adherent A
            ON A.id = adherent
            
            WHERE paire.pid = $pid";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    return $curseur->fetchAll();
}

function registerToEventWith($PDO, $eid, $joueursID)
{
    /*

        // To do 
        // inscription [] [] 
        
        
        Format de l'array:
        [0] id paire 1  <-> AID! <-> PID DE PAIRE ISOLEE
        [1] id paire 1  NULL <-> PID DE PAIRE ISOLEE <-> PID Paire2
        
        [2] id paire 2  <-> NULL <-> PID DE PAIRE ISOLEE
        [3] id paire 2  NULL

        [4] id paire 3  NULL   
        [5] id paire 3  NULL
        [6] id paire 3  NULL
    */

    $joueursID = json_decode($joueursID);  

    /*
        Particularités:
         L'utilisation de 'LAST_INSERTED_ID' dans les requêtes présente un risque, je ne suis pas sûr que ce soi parfaitement syncrone. 
         Si deux joeurs s'inscrivent en même temps, il est possible que le last inserted id soi partagé entre les deux requêtes.
         Ce qui renderait l'inscription très étrange. Mais à priori il est conservé pour une session donnée.

        Index: il permet de savoir où commence la paire,
        la paire deux peut commencer à l'index 1: si c'est une inscription de deux paire isolées.
        la paire deux peut commencer à l'index 2: si c'est un rattachement d'une paire formée et une paire isolée.

    */


    /*
        Créer la première paire:
    */    
    if (!is_array($joueursID[0]) && !is_array($joueursID[1]))
    {
    
        $req = "INSERT INTO `paire` 
            (`pid`, `adherent`)
            VALUES (NULL, '$joueursID[0]');
            ";

        if ($joueursID[1] != NULL)
        {
            $req .= "INSERT INTO `paire` 
                    (`pid`, `adherent`)
                    VALUES (LAST_INSERT_ID(), '$joueursID[1]');
                    ";
        }

        $curseur = $PDO->prepare($req);
        $curseur ->execute();

        $paire1 = $PDO->lastInsertId();
        $index = 2;

    } else {
        /*
            Si [0] et [1] sont des array, ils représentent alors deux paires isolées.
        */

        // Selectionne la paire isolée
        $pid = $joueursID[0][0]; 
        $paire1 = trasnferIsolePaire(createPDO(), $pid);
        $index = 1;

        if (is_array($joueursID[1]))
        {

            // Selectionne la paire isolée
            $pid = $joueursID[1][0]; 
            $paire2 = trasnferIsolePaire(createPDO(), $pid);
            $index = 2;
        }

    }

    /*
        Seconde paire 
    */
    if (!is_array($joueursID[$index]) && !is_array($joueursID[$index]) && $joueursID[$index] != "NULL")
    {
        
        $req = "INSERT INTO `paire` 
                (`pid`, `adherent`)
                VALUES (NULL, '$joueursID[$index]');
            
                INSERT INTO `paire` 
                (`pid`, `adherent`)
                VALUES (LAST_INSERT_ID(), '" . $joueursID[$index+1] . "');
                ";

        $curseur = $PDO->prepare($req);
        $curseur ->execute();

        $paire2 = $PDO->lastInsertId();

    } else {
        if (!is_array($joueursID[0]) && !is_array($joueursID[1]))
        {
            /*
                Si c'est un array, c'est une paire isolée.
            */
            if (is_array($joueursID[2]))
            {
                $index = 2;
                // Selectionne la paire isolée
                $pid = $joueursID[$index][0];
                $paire2 = trasnferIsolePaire(createPDO(), $pid);
            } else {
                $paire2 = "NULL";
            }
        }
    
    }
    

    /*
        Paire de remplaçants
    */
    if ($joueursID[4] != "NULL")
    {

        $req = "INSERT INTO `paire` 
                (`pid`, `adherent`)
                VALUES (NULL, '$joueursID[4]');
                ";

        if ($joueursID[5] != "NULL")
        {
            $req .= "INSERT INTO `paire` 
                    (`pid`, `adherent`)
                    VALUES (LAST_INSERT_ID(), '$joueursID[5]');
                    ";
        }

        if ($joueursID[6] != "NULL")
        {
            $req .= "INSERT INTO `paire` 
                    (`pid`, `adherent`)
                    VALUES (LAST_INSERT_ID(), '$joueursID[6]');
                    ";
        }

        $curseur = $PDO->prepare($req);
        $curseur ->execute();

        $remplacant = $PDO->lastInsertId();

    } else {
        $remplacant= "NULL";
    }

    /*
        On éffectue l'inscription à l'évenement avec les paires données.
    */

        
    $req = "INSERT INTO `inscrire` 
    (`id`, `evenementId`, `paire1`, `paire2`, `remplacant`) 
    VALUES (NULL, $eid, $paire1, $paire2, $remplacant);";
    
    $curseur = $PDO->prepare($req);
    $curseur ->execute();
    
    return $curseur->errorInfo()[2];

}

/*

    Transfère une paire isolée vers une paire
    @return pid

*/
function trasnferIsolePaire($PDO, $pid)
{

    // Tranfère la paire isolée en paire et récupère le pid.
    $paire = copyIsolePaire($PDO, $pid);

    // Supprime paire isolée
    deletePaireIsole($PDO, $pid);

    return $paire;
}

/*
    Génère les requêtes de création d'une paire via un pid de paire isolée
    @return paireId
*/
function copyIsolePaire($PDO, $pid)
{
        /*
            Récupère info des joueurs de la paire isolée
        */
        $joueurs = getMembersOfPaireIsolee($PDO, $pid);

        $id = $joueurs[0]['id'];
        $req = "INSERT INTO `paire` 
                (`pid`, `adherent`)
                VALUES (NULL, '$id');
                ";

        
        for ($i=1; $i < sizeof($joueurs) ; $i++) { 
                $id = $joueurs[$i]['id'];
                $req .= "INSERT INTO `paire` 
                        (`pid`, `adherent`)
                        VALUES (LAST_INSERT_ID(), '$id');
                ";
        }

        $curseur = $PDO->prepare($req);
        $curseur ->execute();

        return $PDO->lastInsertId();

}

function unregisterFromEvent($PDO, $iid)
{

    /*
        Supprime l'inscription
    */
    $req = "DELETE 
            FROM `inscrire` 
            WHERE `inscrire`.`id` = $iid;";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();
    
    return $curseur->errorInfo()[2];
}

function createNewPaireRemplacement($PDO, $iid)
{

    $req = "SELECT MAX(`pid`)+1 as pid
            FROM `paire`;

            UPDATE `inscrire`
            SET `remplacant` = (SELECT MAX(`pid`)+1 FROM `paire`)
            WHERE `inscrire`.`id` = $iid;
            ";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();
    
    return $curseur->fetchAll()[0][0];

}

function deletePaireAssociatedWithIID($PDO, $iid)
{
    $info = getMembersFromIIDForEvent($PDO, $iid);
    // Récupère le numéro de paire de chaque adhérent
    $paireIds = getFormatedIds($info, 3);

    /*
        Dissoud les paires
    */
    $req = "DELETE 
            FROM `paire` 
            WHERE `paire`.`pid` IN ($paireIds);";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    return $curseur->errorInfo()[2];

}

function unregisterFromEventComplex($PDO, $aid, $eid)
{

    $req = "DELETE 
            FROM `inscrire` 
            WHERE `adherent` = $aid && `evenementId` = $eid";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    return $curseur->errorInfo()[2];
}

function getPlayerFavorite($PDO, $aid, $except)
{
    // Liste des ids déjà dans la liste favoris
    $except = json_decode($except, true);
    
    // 1. Parse pour récupérer que les ids
        $notThem = getFormatedIds($except, 0);

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

    return json_encode($curseur->fetchAll());
}

function unsetFromFavorite($PDO, $aid, $fid)
{

    $req = "DELETE 
            FROM `favoris`
            
            WHERE `idAdherent` = $aid && `idFavoris` = $fid";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    return $curseur->errorInfo()[2];
}

function addToFavorite($PDO, $aid, $fid)
{
    $req = "INSERT INTO `favoris` 
            (`idAdherent`, `idFavoris`) 
            VALUES ($aid, $fid)";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    return $curseur->errorInfo()[2];

}

function getEveryMembers($PDO, $except)
{
        
    // Liste des ids déjà dans la liste favoris
    $except = json_decode($except, true);

    // 1. Parse pour récupérer que les ids
        $notThem = getFormatedIds($except, 0);

    // 2. Exectute la requête qui n'inclura pas les except
    $req = "SELECT * 
            FROM `adherent`";

    if ($notThem)
    {
        $req .= " WHERE `id` NOT IN ($notThem)";
    }

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    return json_encode($curseur->fetchAll());


}

function getFormatedIds($except, $index)
{

    // 1. Parse pour récupérer que les ids
    $notThem = "";

    for ($i=0; $i < sizeof($except); $i++) { 
        $notThem .= $except[$i][$index] . ",";
        
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

    return $curseur->errorInfo()[2];


}

function unregisterSOSpartenaire($PDO, $joueursID, $eid)
{

    $joueursID = json_decode($joueursID);

    $joueursID = getLowFormatedIds($joueursID);

    $req = "DELETE 
            FROM `sos` 
            WHERE `idAdherent` IN ($joueursID) && `evenementId` = $eid";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();
    
    return $curseur->errorInfo()[2];

}

function isRegisteredForSOSpartenaire($PDO, $aid, $eid)
{
    $req = "SELECT * 
            FROM `sos`
            WHERE `idAdherent` = $aid && `evenementId` = $eid";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    return json_encode($curseur->fetchAll());

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

    return json_encode($curseur->fetchAll());

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

    $req = "SELECT A.id, A.nom, A.prenom, P.pid
            FROM `inscrire` I
                        
            INNER JOIN paire P
            ON paire1 = P.pid
            OR paire2 = P.pid
            OR remplacant = P.pid 
            
            INNER JOIN adherent A 
            ON A.id = P.adherent
            
            WHERE I.id = $iid";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();
    
    return $curseur->fetchAll();

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

    // Le référant est toujours la dernière personne.
    $referant = $ids[sizeof($ids)-1];

    $ids = getLowFormatedIds($ids);
    $playersInfos = getPlayersInfo($PDO, $ids);

    $i = 0;
    while ($i < $playersInfos && $playersInfos[$i][0] != $referant) { $i++; }
    $referant = $playersInfos[$i];

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

    return $content;


}

function createUnRegistrationNotificationMailForEvent($PDO, $eid, $ids)
{
    
    global $site_url;
    
    $ids = getFormatedIds($ids, 0);
    $playersInfos = getPlayersInfo($PDO, $ids);
    
    // Le référant est toujours la dernière personne.
    $referant = $playersInfos[sizeof($playersInfos)-1];

 
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

    return $content;

}

function createPaireIsoleeNotificationMailForEvent($PDO, $eid, $ids)
{

    global $site_url;
    
    $ids = getFormatedIds($ids, 0);
    $playersInfos = getPlayersInfo($PDO, $ids);

    $toMails = [];
    $players = "";
    foreach ($playersInfos as $key => $player) {
        array_push($toMails, $player['mail']);
        $players .= $player['nom'] . " " . $player['prenom'] . "\n";
    }

    // Recupère les information de l'évenement:
    $eventInfo = getEventInfo($PDO, $eid);

    $content[] = array(
        'subject' => "Notification de desinscription/Paire isolée",
        'body' => nl2br(
            " Bonjour, \n" .
            " Vous venez d'être désinscrit de '" . $eventInfo['titre'] . ", vous allez être placé en paire isolée". 
            " Les joueurs suivants ont été desinscrits: \n " . $players. "\n" . 
            " Pour inspecter l'évenement cliquer ici: " . $site_url . "inscription.php?eid=" . $eid
        ),
        'to' => $toMails,
    );

    return $content;

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

    return $curseur->errorInfo()[2];

}

function getAllPlayersRegistrations($PDO, $aid)
{

    $req = "SELECT `evenementId`
            FROM `inscrire` I
                                    
            INNER JOIN paire P
            ON paire1 = P.pid
            OR paire2 = P.pid
            OR remplacant = P.pid 
                        
            INNER JOIN adherent A 
            ON A.id = P.adherent
                        
            WHERE adherent = $aid";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    return $curseur->fetchAll();

}

function updateEventDate($PDO, $eid, $startDate, $endDate)
{

    $req = "UPDATE `evenement` 
            SET `dteDebut` = '$startDate',
                `dteFin` = '$endDate'
                

            WHERE `evenement`.`id` = $eid;";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();
    
    return $curseur->errorInfo()[2];

}

function registerRemplacantToPid($PDO, $aid, $pid)
{
    
    $req = "INSERT INTO `paire`
            (`pid`, `adherent`) 
            VALUES ($pid, $aid)";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();
    
    return $curseur->errorInfo()[2];    

}

function registerIsolees($PDO, $eid, $ids)
{
    
    $req = "INSERT INTO `isolees` 
            (`pid`, `evenementId`, `adherent`) 
            VALUES (NULL, '$eid', '$ids[0]');";


    for ($i=1; $i < sizeof($ids) ; $i++) { 
        $req .= "INSERT INTO `isolees` 
                (`pid`, `evenementId`, `adherent`) 
                VALUES (LAST_INSERT_ID(), '$eid', '$ids[$i]');";
    }

    $curseur = $PDO->prepare($req);
    $curseur ->execute();
    
    return $curseur->errorInfo()[2];    

}

function unregisterFromRemplacant($PDO, $aid, $iid)
{
    $req = "DELETE paire
            FROM paire
            
            INNER JOIN inscrire 
            ON inscrire.remplacant = pid

            WHERE inscrire.id = $iid && paire.adherent = $aid
            ";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    return $curseur->errorInfo()[2];
}

/*
    Supprime la paire pid
    @return /
*/
function unregisterPaire($PDO, $pid)
{

    $req = "DELETE 
            FROM `paire` 
            WHERE `paire`.`pid` = $pid
            ";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    return $curseur->errorInfo()[2];

}

function getIIDWithPID($PDO, $pid)
{
    $req = "SELECT DISTINCT I.id as iid
            FROM `paire` 
            
            INNER JOIN inscrire I
            ON I.paire1 = pid
            OR I.paire2 = pid
            OR I.remplacant = pid
            
            WHERE pid = $pid 
            ";
    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    return $curseur->fetch();
}

function importIntoPairesIsolees($PDO, $eid, $paires)
{

    $req = "";
    for ($i=0; $i < sizeof($paires); $i++) { 
        $pid = $paires[$i]['pid'];
        $aid = $paires[$i]['id'];
        $req .= "INSERT INTO `isolees` (`pid`, `evenementId`, `adherent`) VALUES ('$pid', '$eid', '$aid');";
    }
    $curseur = $PDO->prepare($req);
    $curseur ->execute();
    
    return $curseur->errorInfo()[2];
}

function getPairesIsoleesForEvent($PDO, $eid, $exceptPID)
{

    $exceptPID = getLowFormatedIds($exceptPID);

    $req = "SELECT I.pid, A.id, A.nom, A.prenom
            FROM `isolees` I
            
            INNER JOIN adherent A
            ON A.id = adherent

            WHERE I.pid NOT IN ($exceptPID) && I.evenementId = $eid
           ";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    return $curseur->fetchAll();

}

function updateEvent($PDO, $event)
{

    /*
        event[0] id
             [1] titre
             [2] dteDebut
             [3] dteFin
             [4] prix
             [5] lieu
             [6] paires

    */
    
    $req = "UPDATE `evenement` 
            SET 
            `titre`= :titre,
            `dteDebut`= :dteDebut,
            `dteFin`= :dteFin,
            `prix`= :prix,
            `lieu`= :lieu,
            `paires`= :paires 
            WHERE `id` = :id";

    $curseur = $PDO->prepare($req);
    $curseur->execute($event);

    return $curseur->errorInfo()[2];

}

function deleteEvent($PDO, $eid, $ety)
{

    /*
        Desinscrit les personnes inscrites à l'évenement
    */

    $registered = getPlayersRegisteredForEvent($PDO, $eid);
    
    // récupère les inscriptions
    $inscriptions = getFormatedIds($registered, 3);
    // récupère les numéros de paire
    $paires = getFormatedIds($registered, 4);


    /*
        Supprime les paires, paires isolées, joueurs sos et inscriptions associés à l'évenement
    */
    $req = "";

    if ($inscriptions)
    {
        $req .= "DELETE FROM `inscrire`
                 WHERE `inscrire`.`id` IN ($inscriptions); 
                ";
    }

    if ($paires)
    {
        $req .= "DELETE FROM `paire`
                 WHERE `paire`.`pid` IN ($paires);
                ";
    }


    $req .= "DELETE FROM `sos`
             WHERE `sos`.`evenementId` = $eid; 
            
             DELETE FROM `isolees`
             WHERE `isolees`.`evenementId` = $eid; 
            ";

    switch ($ety)
    {
        case 1: // Tournois
            $req .= "DELETE FROM `tournoi` WHERE `tournoi`.`evenementId` = $eid;";
        break;

        case 2: // Partie Libre
            $req .= "DELETE FROM `partieLibre` WHERE `partieLibre`.`evenementId` = $eid;";
        break; 
        
        case 3: // Compétition
            $req .= "DELETE FROM `competition` WHERE `competition`.`evenementId` = $eid;";
        break;
    }    

    $req .= "DELETE 
             FROM `evenement` 
             WHERE `evenement`.`id` = $eid;";

    $curseur = $PDO->prepare($req);
    $curseur->execute();

    return $curseur->errorInfo()[2];
    
}

?>