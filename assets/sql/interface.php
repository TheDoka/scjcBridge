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
				echo importEvents(createPDO(), $_POST['events']);
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
                return sendMail($_POST['mailContent']);
            break;
            case 'createRegistrationNotificationMailForEvent':
                echo json_encode(createRegistrationNotificationMailForEvent(createPDO(), $_POST['eid'], $_POST['ids']));
            break;
            case 'createUnRegistrationNotificationMailForEvent':
                echo json_encode(createUnRegistrationNotificationMailForEvent(createPDO(), $_POST['eid'], $_POST['ids'], $_POST['ref']));
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
            case 'updateUserStatut':
                echo json_encode(updateUserStatut(createPDO(), $_POST['aid'], $_POST['statut']));
            break;
            case 'setUserLoggedState':
                echo json_encode(setUserLoggedState(createPDO(), $_POST['aid'], $_POST['statut']));
            break;
            case 'getAllStatut':
                echo json_encode(getAllStatut(createPDO()));
            break;
            case 'getAllNiveaux':
                echo json_encode(getAllNiveaux(createPDO()));
            break;

            case 'getAllCategorie':
                echo json_encode(getAllCategorie(createPDO()));
            break;
            case 'getAllPublic':
                echo json_encode(getAllPublic(createPDO()));
            break;
            case 'getAllDivison':
                echo json_encode(getAllDivison(createPDO()));
            break;
            case 'getAllStade':
                echo json_encode(getAllStade(createPDO()));
            break;
            case 'updateUserInfos':
                echo updateUserInfos(createPDO(), $_POST['userInfos']);
            break;
            case 'deleteUser':
                echo json_encode(deleteUser(createPDO(), $_POST['aid']));
            break;
            case 'getEvenementsRaccorde':
                echo json_encode(getEvenementsRaccorde(createPDO(), $_POST['eid']));
            break;
            case 'isRegisteredForEvent':
                echo json_encode(isRegisteredForEvent(createPDO(), $_POST['eid'], $_POST['aid']));
            break;
            case 'getPermissionEvenement':
                echo json_encode(getPermissionEvenement(createPDO(), $_POST['ety']));
            break;
            case 'getDroits':
                echo json_encode(getDroits(createPDO()));
            break;
            case 'getPermissionStatut':
                echo json_encode(getPermissionStatut(createPDO(), $_POST['statut']));
            break;
            case 'getEty':
                echo json_encode(getEty(createPDO(), $_POST['ety']));
            break;
            case 'newStatuts':
                echo json_encode(newStatuts(createPDO(), $_POST['statuts']));
            break;
            case 'deleteStatut':
                echo json_encode(deleteStatut(createPDO(), $_POST['sid']));
            break;
            case 'deletePermStatut':
                echo json_encode(deletePermStatut(createPDO(), $_POST['sid'], $_POST['did']));
            break;
            case 'newPermStatut':
                echo json_encode(newPermStatut(createPDO(), $_POST['permStatut']));
            break;
            case 'newEty':
                echo json_encode(newEty(createPDO(), $_POST['ety']));
            break; 
            case 'newDroit':
                echo json_encode(newDroit(createPDO(), $_POST['droit']));
            break;
            case 'deleteDroit':
                echo json_encode(deleteDroit(createPDO(), $_POST['did']));
            break;
            case 'deleteEty':
                echo json_encode(deleteEty(createPDO(), $_POST['ety']));
            break;
            case 'newPermEty':
                echo json_encode(newPermEty(createPDO(), $_POST['permEty']));
            break;
            case 'deletePermEty':
                echo json_encode(deletePermEty(createPDO(), $_POST['ety'], $_POST['did']));
            break;
            case 'updateEtyColor':
                echo json_encode(updateEtyColor(createPDO(), $_POST['ety'], $_POST['color']));
            break;
            case 'newEvenement':
                echo json_encode(newEvenement(createPDO(), $_POST['evenement']));
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

function getAllStatut($PDO)
{

    $req = "SELECT * 
            FROM `statut`";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    return $curseur->fetchAll();

}

function getAllNiveaux($PDO)
{

    $req = "SELECT * 
            FROM `niveau`";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    return $curseur->fetchAll();

}

function getUser($PDO, $aid)
{
    $req = "SELECT `id`,`nom`,`prenom`,`mail`,`tel`,`commune`,`sexe`,`password`,`numeroLicense`, A.`idStatut`, S.libelle as statut, A.`idNiveau`, N.numeroSerie as Niveau
            FROM adherent A
                        
            INNER JOIN statut S
            ON A.idStatut = S.idStatut
                        
            INNER JOIN niveau N
            ON A.idNiveau = N.idNiveau

            WHERE id = $aid
           ";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();
    
    $unClient = $curseur->fetch();
    
    return json_encode($unClient);
}

function importEvents($PDO, $evenements)
{
    $req = "";
    $curr= "";
    $secondStep = "";
    // Raccordés
    $raccorde = [];

    // We skip one cause it's the header
    for ($i=1; $i < sizeof($evenements['data']) ; $i++) { 
        
        // Pour chaque ligne on coupe à chaque virgule
            $curr = explode(',', $evenements['data'][$i][0]);

        /* 
            Common:  
                Type         [0] => 1
                Groupe       [1] => 1
                Prix         [2] => 'prix'
                Dte début    [3] => 1/6/2020
                Her début    [4] => 9:00
                Dte fin      [5] => 1/6/2020
                Her fin      [6] => 21:00
                Lieu         [7] => Poitiers
                Paires       [8] => 2

            Tournoi / Compétition: 
                Niveau       [9] => B27
                IMP          [10] => FALSE

            Tournoi:
                Apero        [11] => FALSE
                Repas        [12] => FALSE
                DC           [13] => FALSE

            Compétition: 
                Catégorie    [14] =>  Open
                Division     [15] =>  Couipe du Comite
                Stade        [16] =>  Finale
                Public       [17] =>  Seniors

            #type 
                1 : tournoi
                2 : partie libre
                3 : compétition
                4 : évenement
                5 : spécial
        */
        
        // 1. Ajoute l'évenement 

            // Checking whether or not the time format, format should be: HH:MM:SS
                $curr[4] = correctTimeFormat($curr[4]);
                $curr[6] = correctTimeFormat($curr[6]);

            // Parse the date to MySQL format switching dd/mm/yyyy to yyyy/mm/dd
                $startDate = correctDateTimeFormat($curr[3], $curr[4]);
                $endDate = correctDateTimeFormat($curr[5], $curr[6]);
                
            // Build the title
                $title = makeEventTile($curr, $curr[0]);

            // Parse each items and add to the request

                $req .= "INSERT INTO `evenement` 
                        (`id`, `titre`, `prix`, `dteDebut`, `dteFin`, `lieu`, `type`, `paires`) VALUES 
                        (NULL, '$title', '$curr[2]', '$startDate', '$endDate', $curr[7], $curr[0], $curr[8]);
                        ";

                
            // Si l'événement appartient à un groupe et n'est pas un évenement spécial
            if ($curr[1] != 0)
            {
                $curseur = $PDO->prepare($req);
                $curseur ->execute();

                // Récupère l'élément ajouté
                $req = "SELECT LAST_INSERT_ID();";
                
                $curseur = $PDO->prepare($req);
                $curseur ->execute();
                $eventId = $curseur->fetch()[0];

                /*
                (   
                    [groupe]
                        [membre] id évenement
                    [groupe]
                        ...
                */
                if ($curr[0] != 5)
                {    
                    $raccorde["" . $curr[1]][] = $eventId;
                } else {
                    // Spécial
                    $raccorde["" . $curr[1]]['master'] = $eventId;
                }   

                $req = "";
            } 
            

            // 2. Décide de si l'évenement est un tournoi, une compétition ou bien ... et agis en fonction

            switch ($curr[0])
            {

                case 1:
                    // tournoi
                    $req .= "INSERT INTO `tournoi` 
                            (`id`, `evenementId`, `repas`, `apero`, `imp`, `niveauRequis`, `DC`) VALUES
                            (NULL, LAST_INSERT_ID(), '$curr[11]', '$curr[12]', '$curr[10]', (SELECT `idNiveau` FROM `niveau` WHERE `numeroSerie` = '$curr[9]'), '$curr[13]');";
                break; 

                case 2:
                    // partie libre
                    $req .= "INSERT INTO `partieLibre` 
                            (`id`, `evenementId`, `niveauRequis`) VALUES 
                            (NULL, LAST_INSERT_ID(), (SELECT `idNiveau` FROM `niveau` WHERE `numeroSerie` = '$curr[9]'));
                            ";
                break; 
                
                case 3:
                    // compétition

                    $req .= "INSERT INTO `competition` 
                            (`id`, `evenementId`, `catComp`, `division`, `stade`, `public`) VALUES 
                            (NULL, LAST_INSERT_ID(), $curr[14], $curr[15], $curr[16], $curr[17]);";
                break;


            }

        }


    // Effectue les raccordement
    foreach ($raccorde as $groupe) {

        for ($i=0; $i < sizeof($groupe)-1; $i++) { 
            $Aeid = $groupe['master'];
            $Beid = $groupe[$i];
            $req .= "INSERT INTO `raccorde` 
                    (`Aeid`, `Beid`) VALUES 
                    ($Aeid,$Beid);
                    ";

        }

    }
    
    $curseur = $PDO->prepare($req);
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


    switch ($type)
    {

        case 1:
            // tournoi

            $title = "Tournoi Débutant";
            if ($data[7] != "4T")
            {
                if ($data[8] == 4) $title = "Patton par 4"; else $title = "Tournoi de régularité";
                if ($data[10]) $title .= " (IMP)"; else $title .= "(%)";
            }

        break; 

        case 2:
            // compétition
            $title = "Partie Libre";
        break;

        case 3:
            // compétition
            $req = "";
            $title = "$data[15] $data[17] $data[14] par $data[8] $data[16]";
        break;
                
        case 5:
            $title = "Spécial";
        break;

        default:
            $title = "Événement";
        break;

    }


    return $title;
}

function getEvents($PDO)
{

    $result = [];

    /*
        Récupères les informations des compétitons, tournois et parties libres
    */
    $query = "SELECT * 
            FROM `evenement` E
            
            INNER JOIN competition C 
            ON E.`id` = C.evenementId
            
            INNER JOIN typeEvenement T
            ON T.id = E.type;
            
            SELECT * 
            FROM `evenement` E

            INNER JOIN tournoi C 
            ON E.`id` = C.evenementId

            INNER JOIN typeEvenement T
            ON T.id = E.type;
        
            
            SELECT * 
            FROM `evenement` E

            INNER JOIN partieLibre C 
            ON E.`id` = C.evenementId

            INNER JOIN typeEvenement T
            ON T.id = E.type;

            
            ";

    $statement = $PDO->prepare($query);
    $statement->execute();

    $result = $statement->fetchAll();
    
    for ($i=0; $i < 2; $i++) { 
        $statement->nextRowset();
        $result = array_merge($result, $statement->fetchAll());
    }

    $except = getFormatedIds($result, 0);

    /*  
        Remarquons l'inner join, si l'évenement n'était pas une compétitons/tournois/parties libres
        alors il ne serait pas apparu dans la requête.
        On récupère le reste des évenements qui ne sont liés à aucunes autre tables.
    */

    $query = "SELECT * 
              FROM `evenement` E
              
              INNER JOIN typeEvenement T
              ON T.id = E.type

              WHERE E.type > 3;
             ";

    $statement = $PDO->prepare($query);
    $statement->execute();

    $result = array_merge($result, $statement->fetchAll());

    return $result;
    
}

function getEventsOnly($PDO, $only)
{

    $result = [];

    $query = "SELECT * 
            FROM `evenement` E
            
            INNER JOIN competition C 
            ON E.`id` = C.evenementId
            
            WHERE E.id IN ($only);

            SELECT * 
            FROM `evenement` E

            INNER JOIN tournoi C 
            ON E.`id` = C.evenementId
            
            WHERE E.id IN ($only);

            SELECT * 
            FROM `evenement` E

            INNER JOIN partieLibre C 
            ON E.`id` = C.evenementId
            
            WHERE E.id IN ($only);
            ";

    $statement = $PDO->prepare($query);
    $statement->execute();

    $result = $statement->fetchAll();
    
    for ($i=0; $i < 2; $i++) { 
        $statement->nextRowset();
        $result = array_merge($result, $statement->fetchAll());
    }

    $query = "SELECT *
              FROM `evenement` E
              
              INNER JOIN typeEvenement T
              ON T.id = E.type

              WHERE E.type > 3 && E.id IN ($only);
             ";

    $statement = $PDO->prepare($query);
    $statement->execute();

    $result = array_merge($result, $statement->fetchAll());

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
    
    array_merge($common, $curseur->fetchAll());

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

    $req = "SELECT A.id, A.nom, A.prenom, A.mail
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
        Format de l'array:
        [0] id paire 1  <-> AID! <-> PID DE PAIRE ISOLEE
        [1] id paire 1  NULL <-> PID DE PAIRE ISOLEE <-> PID Paire2
        
        [2] id paire 2  <-> NULL <-> PID DE PAIRE ISOLEE
        [3] id paire 2  NULL

        [4] id paire 3  NULL   
        [5] id paire 3  NULL
        [6] id paire 3  NULL

        Subject: Notification d'inscription
        Body: 
            Vous venez d'être inscrit à l'événement: [TITRE].
            Vous êtes inscrits avec: 
                * NOM prenom
                * NOM prenom
        Cliquer ici pour refuser/annuler: [LIEN]
        
    */

    $playersInfos = [];

    $referant = $ids[sizeof($ids)-1];

    // Inscription avec seul // avec paire normal
    if ((!is_array($ids[0]) && !is_array($ids[1])) && !is_array($ids[2]))
    {

        // Le référant est toujours la dernière personne.
        $ids = getLowFormatedIds($ids);
        $playersInfos = getPlayersInfo($PDO, $ids);

        $i = 0;
        while ($i < sizeof($playersInfos) && $playersInfos[$i][0] != $referant) { $i++; }
        $referant = $playersInfos[$i];

    } else {

        /*
            On regarde si l'inscription contient des paires isolées, si c'est le cas, on récupère l'id des joueurs membres
        */
        $i = 0;
        while (!is_array($ids[$i]))
        {
            $playersInfos[$i] = $ids[$i];
            $i++;
        }

        $playersInfos = getLowFormatedIds($playersInfos);
        $playersInfos = getPlayersInfo($PDO, $playersInfos);

        while (is_array($ids[$i]))
        {
            $joueursPaire = getMembersOfPaireIsolee($PDO, $ids[$i][0]);

            for ($j=0; $j < 2; $j++) { 
                $playersInfos[$i+$j] = $joueursPaire[$j];
                $i++;
            }
            $i++;
        }

        $playersInfos = array_merge($playersInfos, getPlayersInfo($PDO, $referant));
        $referant = $playersInfos[sizeof($playersInfos)-1];

    }

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
            " Vous venez d'être inscrit à l'événement: '" . $eventInfo['titre'] . "' par: " . $referant['nom'] . ' ' . $referant['prenom'] . "\n". 
            " Date/Heure: ". $eventInfo['dteDebut']. "\n" . 
            " Membres de la paire: \n $players \n" . 
            " Si vous souhaiter refuser ou annuler l'inscription cliquez ici: " . createUnregistrationLinkForEvent($referant[0], $referant['nom'], $eid). ".".
            " Ou rendez vous sur l'interface."
        ),
        'to' => $toMails,
    );

    return $content;


}

function createUnRegistrationNotificationMailForEvent($PDO, $eid, $ids, $ref)
{
    
    global $site_url;
    
    $ids = getFormatedIds($ids, 0);
    $playersInfos = getPlayersInfo($PDO, $ids);
 
    $i = 0;
    while ($i < sizeof($playersInfos) && $playersInfos[$i][0] != $ref) { $i++; }
    if ($i < sizeof($playersInfos))
    {
        $referant = $playersInfos[$i];
    } else {
        $referant['nom'] = "un";
        $referant['prenom'] = "administrateur";
    }


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
            " Vous venez d'être désinscrit de l'événement: '" . $eventInfo['titre'] . "' par: " . $referant['nom'] . ' ' . $referant['prenom'] . "\n". 
            " Les joueurs suivants ont étés desinscrits: \n " . $players. "\n" . 
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
            " Vous venez d'être désinscrit de '" . $eventInfo['titre'] . ", vous allez être placé en paire disponibles". 
            " Seront placés: \n " . $players. "\n" . 
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

    $req = "SELECT `evenementId`, I.id as iid, P.pid
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
             [7] ety

    */
    
    $req = "UPDATE `evenement` 
            SET 
            `titre`= :titre,
            `dteDebut`= :dteDebut,
            `dteFin`= :dteFin,
            `prix`= :prix,
            `lieu`= :lieu,
            `paires`= :paires,
            `type`= :ety
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

    // Si l'évenement est raccordé
    $req .= "DELETE FROM `raccorde`  
            WHERE `raccorde`.`Beid` = $eid;";


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

        case 5: // Spécial
            
            $req .= "DELETE FROM `raccorde`  WHERE `raccorde`.`Aeid` = $eid;";
        break;

    }    

    $req .= "DELETE 
             FROM `evenement` 
             WHERE `evenement`.`id` = $eid;";

    $curseur = $PDO->prepare($req);
    $curseur->execute();

    return $curseur->errorInfo()[2];
    
}

function updateUserStatut($PDO, $aid, $statut)
{

    $req = "UPDATE adherent
            SET `idStatut` = $statut
            WHERE id = $aid;
           ";

    $curseur = $PDO->prepare($req);
    $curseur->execute();

    return $curseur->errorInfo()[2];

}

function updateUserInfos($PDO, $userInfo)
{

    $userInfo = json_decode($userInfo, true);
    print_r($userInfo);
    $req = "UPDATE `adherent` 
            SET 
            `nom`= :nom,
            `prenom`= :prenom,
            `mail`= :mail,
            `tel`= :tel,
            `commune`= :commune,
            `numeroLicense`= :numeroLicense,
            `idStatut`= :statut,
            `idNiveau`= :niveau
            WHERE `id` = :id";

    $curseur = $PDO->prepare($req);
    $curseur->execute($userInfo);

    return $curseur->errorInfo()[2];

}

function setUserLoggedState($PDO, $aid, $logged)
{
    $req = "UPDATE adherent
            SET `logged` = $logged
            WHERE id = $aid;
        ";

    $curseur = $PDO->prepare($req);
    $curseur->execute();
    
    return $curseur->errorInfo()[2];

}

function deleteUser($PDO, $aid)
{

    $registrations = getAllPlayersRegistrations($PDO, $aid);

    $error = "";
    $req = "";
    foreach ($registrations as $key => $registration) {

        // Supprime toutes les données en lien avec le joueurs
        $req = "DELETE FROM `inscrire`
                WHERE `inscrire`.`id` = $registration[1]; 

                DELETE FROM `paire`
                WHERE `paire`.`pid` = $registration[2];

                DELETE FROM `sos`
                WHERE `sos`.`idAdherent` = $aid;
                
                DELETE FROM `isolees`
                WHERE `isolees`.`pid` = $registration[2];
                ";
    }

    $req .= "DELETE FROM `favoris`
             WHERE `favoris`.`idFavoris` = $aid OR `favoris`.`idAdherent` = $aid;

             DELETE FROM `adherent`
             WHERE `adherent`.`id` = $aid;";

    $curseur = $PDO->prepare($req);
    $curseur->execute();
    $error .= $curseur->errorInfo()[2];
    
    return $error;

}

function getEvenementsRaccorde($PDO, $eid)
{
    $req = "SELECT Beid
            FROM `raccorde`

            WHERE `Aeid` = $eid";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    $raccordes = getFormatedIds($curseur->fetchAll(), 0);

    return getEventsOnly($PDO, $raccordes);     

}

function isRegisteredForEvent($PDO, $eid, $aid)
{
    $req = "SELECT A.id, A.nom, A.prenom, I.Id as iid, P.pid as NumPaire, I.paire1, I.paire2, I.remplacant
            FROM `inscrire` I
                        
            INNER JOIN paire P
            ON paire1 = P.pid
            OR paire2 = P.pid
            OR remplacant = P.pid 
            
            INNER JOIN adherent A 
            ON A.id = P.adherent
            
            WHERE `evenementId`= $eid AND $aid IN (SELECT adherent FROM paire WHERE pid = P.pid)
            ORDER BY NumPaire";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();


    return $curseur->fetchAll();       

}

/*
    Récupère les permissions et les droits associés aux types d'évenements
    ~ ety: type d'évenement
    @return [id, libelle, color, did, droit]
*/
function getPermissionEvenement($PDO, $ety)
{

    $req = "SELECT T.libelle as event, T.id as ety, D.libelle as droit, D.id as did
            FROM permissionEvenement P
            
            INNER JOIN typeEvenement T
            ON T.id = P.typeEvenement
            
            INNER JOIN droit D
            ON D.id = P.`droit`

            ORDER BY T.id
           ";

    if ($ety != -1)
        $req .= "WHERE T.id = $ety";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();


    return $curseur->fetchAll();        

}

/*
    Récupère le contenu de la table 'droit'
    @return id, libelle
*/
function getDroits($PDO)
{

    $req = "SELECT * 
            FROM `droit`
           ";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    return $curseur->fetchAll();        

}

/*
    Récupère le contenu de la table 'stade'
    @return id, libelle
*/
function getAllStade($PDO)
{

    $req = "SELECT * 
            FROM `stade`
           ";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    return $curseur->fetchAll();        

}
/*
    Récupère le contenu de la table 'division'
    @return id, libelle
*/
function getAllDivison($PDO)
{

    $req = "SELECT * 
            FROM `division`
           ";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    return $curseur->fetchAll();        

}

/*
    Récupère le contenu de la table 'public'
    @return id, libelle
*/
function getAllPublic($PDO)
{

    $req = "SELECT * 
            FROM `public`
           ";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    return $curseur->fetchAll();        

}
/*
    Récupère le contenu de la table 'categorieCompetition'
    @return id, libelle
*/
function getAllCategorie($PDO)
{

    $req = "SELECT * 
            FROM `categorieCompetition`
           ";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    return $curseur->fetchAll();        

}

/*
    Récupère la permission et les droits associès au statut
    ~ statut: id statut
    @return [id, libelle, did, droit]
*/
function getPermissionStatut($PDO, $statut)
{

    $req = "SELECT S.libelle as statut, S.idStatut as sid, D.libelle as droit, D.id as did
            FROM permissionStatut P
            
            INNER JOIN statut S
            ON S.idStatut = P.`idStatut`
            
            INNER JOIN droit D 
            ON D.id = P.droit
           ";


    if ($statut != -1)
        $req .= "WHERE S.idStatut = $statut";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    return $curseur->fetchAll();        

}

/*
    Récupère les type evenements
    ~ ety: id évenement
    @return [id, libelle, permissionLvl]
*/
function getEty($PDO, $ety)
{

    $req = "SELECT * 
            FROM `typeEvenement`
           ";


    if ($ety != -1)
        $req .= "WHERE id = $ety";

    $curseur = $PDO->prepare($req);
    $curseur ->execute();

    return $curseur->fetchAll();        

}

/*
    Ajoute x nombre de statut
    statuts: [libelle, droits]
    @return PDO error
*/
function newStatuts($PDO, $statuts)
{
    $req = "";

    $statuts = json_decode($statuts, true);
    
    foreach ($statuts as $statut){
        $libelle = $statut['libelle'];

        $req .= "INSERT INTO `statut` 
                (`idStatut`, `libelle`) 
                VALUES (NULL, '$libelle');";
    }

    $curseur = $PDO->prepare($req);
    $curseur->execute();
    
    return $curseur->errorInfo()[2];;      

}

/*
    Ajoute le tournoi tournoi à l'agenda
    tournoi: []
    @return PDO error
*/
function newEvenement($PDO, $evenement)
{

    $evenement = json_decode($evenement, true)[0];

    $req = "INSERT INTO `evenement` 
            (`id`, `titre`, `prix`, `dteDebut`, `dteFin`, `lieu`, `type`, `paires`) VALUES 
            (NULL, :titre, :prix, :dteDebut, :dteFin, :lieu, :ety, :paire);
           ";

    switch ($evenement['ety'])      
    {
        case 1:
            
            $req .= "INSERT INTO `tournoi` 
                    (`id`, `evenementId`, `repas`, `apero`, `imp`, `niveauRequis`, `DC`) VALUES
                    (NULL, last_insert_id(), :repas, :apero, :imp, :niveauRequis, :dc);
                    ";

        break;

        case 2:
            $req .= "INSERT INTO `partieLibre` (`id`, `evenementId`, `niveauRequis`) VALUES 
                    (NULL, last_insert_id(), :niveauRequis)
                    ";
        break;

        case 3:
                $req .= "INSERT INTO `competition` 
                        (`id`, `evenementId`, `catComp`, `division`, `stade`, `public`) VALUES 
                        (NULL, last_insert_id(), :catComp, :division, :stade, :public);
                        ";
        break;

        case 5:

            foreach ($evenement['raccordes'] as $key => $raccorde) {
                $req .= "INSERT INTO `raccorde` 
                        (`Aeid`, `Beid`) VALUES 
                        (last_insert_id(), $raccorde);
                        ";
            }

            // MySQL qui pense que si elements entrés > élements utilsés = erreur
            unset($evenement['raccordes']);

        break;

    }


    $curseur = $PDO->prepare($req);
    $curseur->execute($evenement);

    return $curseur->errorInfo()[2];;      

}

/*
    Ajoute x nombre de permissionStatut
    statuts: [id, libelle, droit]
    @return PDO error
*/
function newPermStatut($PDO, $permStatut)
{
    $req = "";

    $permsStatuts = json_decode($permStatut, true);
    
    foreach ($permsStatuts as $permStatut){
        $sid = $permStatut['sid'];
        $did = $permStatut['did'];

        $req .= "INSERT INTO `permissionStatut` 
                (`idStatut`, `droit`) 
                VALUES ($sid, $did);";
    }

    $curseur = $PDO->prepare($req);
    $curseur->execute();
    
    return $curseur->errorInfo()[2];;      

}

/*
    Ajoute x nombre de ety
    ety: [libelle]
    @return PDO error
*/
function newEty($PDO, $ety)
{
    
    $req = "";

    $etys = json_decode($ety, true);
    
    foreach ($etys as $ety){
        $libelle = $ety['libelle'];
        $color = $ety['color'];

        $req .= "INSERT INTO `typeEvenement` 
                (`id`, `libelle`, `color`) 
                VALUES (NULL, '$libelle', '$color')";
    }

    $curseur = $PDO->prepare($req);
    $curseur->execute();
    
    return $curseur->errorInfo()[2];;      

}

/*
    Ajoute x nombre de droit
    droit: [libelle]
    @return PDO error
*/
function newDroit($PDO, $droit)
{
    
    $req = "";

    $droits = json_decode($droit, true);
    
    foreach ($droits as $droit){
        $libelle = $droit['libelle'];

        $req .= "INSERT INTO `droit` 
                (`id`, `libelle`) 
                VALUES (NULL, '$libelle')";
    }

    $curseur = $PDO->prepare($req);
    $curseur->execute();
    
    return $curseur->errorInfo()[2];;      

}

/*
    Supprime le statut 
    sid: id statut
    @return PDO error
*/
function deleteStatut($PDO, $sid)
{
    $req = "DELETE 
            FROM `statut`
            WHERE `statut`.`idStatut` = $sid
           ";
   
    $curseur = $PDO->prepare($req);
    $curseur->execute();
    
    return $curseur->errorInfo()[2];;      

}

/*
    Supprime le droit 
    did: id droit
    @return PDO error
*/
function deleteDroit($PDO, $did)
{
    $req = "DELETE 
            FROM `droit`
            WHERE `droit`.`id` = $did
           ";
   
    $curseur = $PDO->prepare($req);
    $curseur->execute();
    
    return $curseur->errorInfo()[2];;      

}
/*
    Supprime le type d'évenement 
    ety: id type évenement
    @return PDO error
*/
function deleteEty($PDO, $ety)
{
    $req = "DELETE 
            FROM `typeEvenement`
            WHERE `typeEvenement`.`id` = $ety
           ";
   
    $curseur = $PDO->prepare($req);
    $curseur->execute();
    
    return $curseur->errorInfo()[2];;      

}

/*
    Supprime la permission evenement
    ety: id type évenement
    did: id type évenement
    @return PDO error
*/
function deletePermEty($PDO, $ety, $did)
{
    $req = "DELETE 
            FROM `permissionEvenement`
            WHERE `typeEvenement` = $ety AND `droit` = $did;
           ";
   
    $curseur = $PDO->prepare($req);
    $curseur->execute();
    
    return $curseur->errorInfo()[2];;      
}
/*
    Met à jour la couleur de l'évenement
    ety: id type évenement
    color: couleur hex
    @return PDO error
*/
function updateEtyColor($PDO, $ety, $color)
{

    $req = "UPDATE `typeEvenement` 
            SET `color` = '$color' 
            WHERE `typeEvenement`.`id` = $ety;
           ";
   
    $curseur = $PDO->prepare($req);
    $curseur->execute();
    
    return $curseur->errorInfo()[2];

}


/*
    Supprime le statut 
    sid: id statut
    @return PDO error
*/
function deletePermStatut($PDO, $sid, $did)
{

    $req = "DELETE 
            FROM `permissionStatut`
            WHERE `permissionStatut`.`idStatut` = $sid AND `permissionStatut`.`droit` = $did;
           ";
   
    $curseur = $PDO->prepare($req);
    $curseur->execute();
    
    return $curseur->errorInfo()[2];;      

}

/*
    Ajoute une nouvelle permission d'évenement
    permEty: [permETy]
    @return PDO error
*/
function newPermEty($PDO, $permEty)
{

    $req = "";

    $permEtys = json_decode($permEty, true);
    
    foreach ($permEtys as $permEty){
        $id = $permEty['id'];
        $droit = $permEty['droit'];

        $req .= "INSERT INTO `permissionEvenement` 
                (`typeEvenement`, `droit`) 
                VALUES ($id, $droit)";
    }

    $curseur = $PDO->prepare($req);
    $curseur->execute();
    
    return $curseur->errorInfo()[2];;    

}
?>