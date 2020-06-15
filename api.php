<?php

    include 'assets/sql/interface.php';

    /* 
        Pour utiliser l'API, et pour un minimum de 'sécurité', on utilisera le hash de valeurs uniques propres à l'utilisateurs... 
        Ici j'utiliserai: MD5(idAdherent+Nom)
        
    */

    if (isset($_GET['token']))
    {

        $info = isValidToken($_GET['token']);
        if ($info)
        {
            // Exec
            switch ($_GET['f'])
            {
                // unre for unRegister.
                case 'unre':
                    $aid = $info[0][0];
                    $eid = $_GET['eid'];
                    
                    apiUnregisterPaire(createPDO(), $aid, $eid);
                    redirect("inscription.php?eid=$eid");
                break;
            }
            
            echo 'valid';

        } else {
            echo 'Invalid token.';
        }

    }


    function isValidToken($hash)
    {
        $PDO = createPDO();

        /*
            On éffectue une requête SQL afin de trouver la personne qui possède le token.
        */

        $req = "SELECT * 
                FROM `adherent` 
                WHERE MD5(CONCAT(`id`,`nom`)) = '$hash'";

        $curseur = $PDO->prepare($req);
        $curseur ->execute();

        $response = $curseur->fetchAll();


        return $response;

    }

?>