/*
    Recherche dans un array
    @return contient
*/
function isInArray(arr, element)
{
    i = 0;
    while (i < arr.length && arr[i][0] != element)
    { 
        i++;
    }

    return i < arr.length;
}

/*
    Recupère les informations d'un joueur
    @return [] informations adherent
*/
function getUser(aid)
{
    let user = [];
    $.ajax({
        url: 'assets/sql/interface.php',
        method:"POST",
        data:{
            function: 'getUser',
            aid: aid
        },
        success: function (data) {
            user = JSON.parse(data);
        },
        async:   false
    });

    return user;

}

/*
    Inscrit [] joueursID à un évenement eid.
    @return /
*/
function registerToEventWith(eid, joueursID)
{

        $.post('assets/sql/interface.php',
            {
                function: 'registerToEventWith',
                eid: eid,
                ids: JSON.stringify(joueursID),
            }, function(data) {
                    console.log(data);
                    
                    if (data)
                    {
                        alert('Une erreur est survenue!\n' + data);
                    } else {
                        document.location.reload(true);
                    }
                    

            });
            

}


/*
    Supprime la paire pid des paires
*/
function unregisterPaire(pid, eid)
{
    let iid = 0;
    let error = "";


    
    /*
        I. On récupère l'id de l'inscription de la paire
    */
    $.ajaxSetup({async: false});  
    $.ajax({
        type: "POST",
        async: false,
        url: "assets/sql/interface.php",
        data: {
            function: 'getIIDWithPID',
            pid: pid,
        },
        success: function(data)
        {
            iid = JSON.parse(data)['iid'];
            console.log(data);

        },
    });

    /*
        II. Recupère memebre de la paire qui va être désinscrite
    */
    $.post('assets/sql/interface.php',
    {
        function: 'getMembersOfPaire',
        pid: pid,
    }, function(data) {

        data = JSON.parse(data);
        notifiyUnRegisterByMail(eid, data);
        
    });

    /*
        II. Supprime la paire
    */
        
        $.post('assets/sql/interface.php',
        {
            function: 'unregisterPaire',
            pid: pid,
            iid: iid,
        }, function(data) {
            
            if (data) {
                error += "[2] " + data + "\n";
            }
            
        });
        
    /*
        II. On récupères les paires restantes
    */
    pairesRestantes = [];
    $.post('assets/sql/interface.php',
    {
        function: 'getMembersFromIIDForEvent',
        iid: iid,
    }, function(data) {
        pairesRestantes = JSON.parse(data);
        console.log(pairesRestantes);

    });

    if (pairesRestantes.length > 1)
    {
        notifiyPaireIsoleeByMail(eid, pairesRestantes);
        
        /*
            III. Supprime le reste des paires
        */
        $.post('assets/sql/interface.php',
        {
            function: 'deletePaireAssociatedWithIID',
            iid: iid,
        }, function(data) {
            if (data) {
                error += "[3] " + data + "\n";
            }
        });
        
        /*
            IV. On importe les paires restante dans les paires isolées.
        */

        $.post('assets/sql/interface.php',
        {
            function: 'importIntoPairesIsolees',
            eid: eid,
            paires: pairesRestantes,
        }, function(data) {

            if (data) {
                error += "[4] " + data + "\n";
            }

        });

    }

    /*
        V. On supprime l'inscription
    */
    $.post('assets/sql/interface.php',
    {
        function: 'unregisterFromEvent',
        iid: iid,
    }, function(data) {
        
        if (data) {
            error += "[5] " + data + "\n";
        }
        
    });

    return error;
}

/*
    Supprime la paire pid des paires isolées
*/
function deletePaireIsolee(pid)
{
    $.post('assets/sql/interface.php',
    {
        function: 'deletePaireIsole',
        pid: pid,
    }, function(data) {
        console.log(data);
        if (data)
        {

        } else {
            document.location.reload(true);
        }
    });

}

/*
    Effectue la désinscription, supprime les paires associés à l'inscription puis supprimme l'inscription.
*/
function unregisterPaires(aid, eid, iid)
{

    /*
        Envoi un mail à l'ensemble des personnes inscrite avec cette paire
    */

    $.post('assets/sql/interface.php',
    {
        function: 'getMembersFromIIDForEvent',
        iid: iid,
    }, function(data) {
        
            let ids = JSON.parse(data);
            console.log(ids);
           
            notifiyUnRegisterByMail(eid, ids);

    });

    $.post('assets/sql/interface.php',
    {
        function: 'deletePaireAssociatedWithIID',
        iid: iid,
    }, function(data) {

        console.log(data);
    });

    $.post('assets/sql/interface.php',
    {
        function: 'unregisterFromEvent',
        iid: iid,
    }, function(data) {
            console.log(data);
            if (data)
            {
                alert('Une erreur est survenue!\n' + data);
            } else {
                document.location.reload(true);
            }

    });
        

}

/*
    Ajoute le joeur aid à la paire pid
*/
function registerRemplacantToPid(aid, pid)
{

    $.post('assets/sql/interface.php',
    {
        function: 'registerRemplacantToPid',
        aid: aid,
        pid: pid,
    }, function(data) {
            console.log(data);
            
            if (data)
            {
                alert('Une erreur est survenue!\n' + data);
            } else {
                document.location.reload(true);
            }
            

    });

}

/*
    Forme une paire isolée avec les joueurs
*/
function registerIsolees(eid, ids)
{

    $.post('assets/sql/interface.php',
    {
        function: 'registerIsolees',
        eid: eid,
        ids: ids,
    }, function(data) {
            console.log(data);
            
            if (data)
            {
                alert('Une erreur est survenue!\n' + data);
            } else {
                document.location.reload(true);
            }
            

    });

}

/*
    Donne une paire de remplacement a l'inscription IID
    @return pid
*/
function createNewPaireRemplacement(iid)
{

    $.ajax({
        url: 'assets/sql/interface.php',
        method:"POST",
        data:{
            function: 'createNewPaireRemplacement',
            iid: iid
        },
        success: function (data) {
            pid = data;
        },
        async:   false
    });

    return pid;
}

/*
    Retire le joeur aid de la paire des remplaçants de l'inscription iid
*/
function unregisterFromRemplacant(aid, iid)
{

    $.post('assets/sql/interface.php',
    {
        function: 'unregisterFromRemplacant',
        aid: aid,
        iid: iid,
    }, function(data) {
            console.log(data);
            
            if (data)
            {
                alert('Une erreur est survenue!\n' + data);
            } else {
                document.location.reload(true);
            }
            

    });



}

/*
    Inscrit aid à SOS partenaire
    @return /
*/
function registerSOSpartenaire(aid, eid)
{
        $.post('assets/sql/interface.php',
            {
                function: 'registerSOSpartenaire',
                aid: aid,
                eid: eid,
            }, function(data) {
                    if (data)
                    {
                        alert('Une erreur est survenue!\n' + data);
                    } else {
                       document.location.reload(true);
                    }

            });
}

/*
    Desinscrit aid de SOS partenaire
    aid, eid sont des variables globales
    @return /
*/
function unregisterSOSpartenaire(aid, eid, joueursId)
{

        // L'array joueursId contient tout les joueurs cochés, or il ne nous contient pas.
        // Quand on s'inscrit on souhaite ne plus être sur la liste SOS partenaire, ainsi que nos partenaires inscrits.

        joueursId.push(aid);

        $.post('assets/sql/interface.php',
            {
                function: 'unregisterSOSpartenaire',
                players: JSON.stringify(joueursId),
                eid: eid,
            }, function(data) {
                console.log(data);
                    if (data)
                    {
                       alert('Une erreur est survenue!\n' + data);
                    } else {
                       document.location.reload(true);
                    }

            });
}

/*
    Créer et envoie une notification mail d'inscription
    de l'évenement: eid
    aux ids: joueurs
    @return /
*/
function notifyRegisterByMail(aid, eid, ids)
{

    ids.push(aid);

    $mailContent = [];
    $.post('assets/sql/interface.php',
        {
            function: 'createRegistrationNotificationMailForEvent',
            eid: eid,
            ids: ids,
        }, function(data) {
            mailContent = JSON.parse(data);
            console.log(mailContent);
        });

    $.post('assets/sql/interface.php',
    {
        function: 'sendMail',
        mailContent: mailContent,
    }, function(data) {
        console.log(data);
    });


}
/*
    Créer et envoie une notification mail de desinscription
    de l'évenement: eid
    aux ids: joueurs
    @return /
*/
function notifiyUnRegisterByMail(eid, ids)
{

    $mailContent = [];
    $.post('assets/sql/interface.php',
        {
            function: 'createUnRegistrationNotificationMailForEvent',
            eid: eid,
            ids: ids,
        }, function(data) {

            mailContent = JSON.parse(data);
            console.log(mailContent);

        });

    $.post('assets/sql/interface.php',
    {
        function: 'sendMail',
        mailContent: mailContent,
    }, function(data) {
        console.log(data);
    });

}

/*
    Créer et envoie une notification mail que le joueur s'est fait désinscrire et passe en paire isolée
    de l'évenement: eid
    aux ids: joueurs
    @return /
*/
function notifiyPaireIsoleeByMail(eid, ids)
{

    $.post('assets/sql/interface.php',
        {
            function: 'createPaireIsoleeNotificationMailForEvent',
            eid: eid,
            ids: ids,
        }, function(data) {

            mailContent = JSON.parse(data);
            console.log(mailContent);

        });


}

/*

    Récupère les informations d'un événement
    @return evenement
*/

function getEvent(eid)
{

    
    return $.ajax({
        url: 'assets/sql/interface.php',
        method:"POST",
        async: false,
        data:{
            function: 'getEventInfo',
            eid: eid,
        },
    }).responseText;

    

}

function updateEvent(event)
{
    $.post('assets/sql/interface.php',
    {
        function: 'updateEvent',
        event: event,
    }, function(data) {
        if (data)
        {
            alert('Une erreur est surveneue: \n' + data);
        }

    });

}


function getLieux()
{
    return $.ajax({
        url: 'assets/sql/interface.php',
        method:"POST",
        data:{
            function: 'getLieux',
        },
    }).responseText; 
}

function getPlayersRegisteredForEvent(eid)
{
    return $.ajax({
        url: 'assets/sql/interface.php',
        method:"POST",
        data:{
            function: 'getPlayersRegisteredForEvent',
            eid: eid,
        },
    }).responseText; 

}

function deleteEvent(eid, ety)
{
    $.post('assets/sql/interface.php',
    {
        function: 'deleteEvent',
        eid: eid,
        ety: ety,
    }, function(data) {
        console.log(data);

    });
}

function getEveryMembers(except)
{

    return JSON.parse($.ajax({
        url: 'assets/sql/interface.php',
        method:"POST",
        async: false,
        data:{
            function: 'getEveryMembers',
            except: JSON.stringify(except),
        },
    }).responseText); 

}

function updateUserStatut(aid, statut)
{

    $.ajax({
        url: 'assets/sql/interface.php',
        method:"POST",
        data:{
            function: 'updateUserStatut',
            aid: aid,
            statut: statut,
        },
        success : function(e){ 
            return e;
        }
    });

}

function updateUserInfos(userInfos)
{

    $.ajax({
        url: 'assets/sql/interface.php',
        method:"POST",
        data:{
            function: 'updateUserInfos',
            userInfos: JSON.stringify(userInfos),
        },
        success : function(e){ 
            console.log(e);
            return e;
        }
    });


}

function setUserLoggedState(aid, statut)
{

    $.ajax({
        url: 'assets/sql/interface.php',
        method:"POST",
        data:{
            function: 'setUserLoggedState',
            aid: aid,
            statut: statut,
        },
        success : function(e){ 
            console.log(e);
            return e;
        }
    });

}

function getAllStatut()
{

    return JSON.parse($.ajax({
        url: 'assets/sql/interface.php',
        method:"POST",
        async: false,
        data:{
            function: 'getAllStatut',
        },
    }).responseText);

}
function getAllNiveaux()
{

    return JSON.parse($.ajax({
        url: 'assets/sql/interface.php',
        method:"POST",
        async: false,
        data:{
            function: 'getAllNiveaux',
        },
    }).responseText);

}


/*
    Resize la fenêtre pour cacher la navbar.
*/
function fullHeight() {

    $('.js-fullheight').css('height', $(window).height());
    $(window).resize(function(){
        $('.js-fullheight').css('height', $(window).height());
    });

}