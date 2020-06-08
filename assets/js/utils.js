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
                    /*
                    if (data)
                    {
                        alert('Une erreur est survenue!\n' + data);
                    } else {
                        document.location.reload(true);
                    }
                    */

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
function notifiyUnRegisterByMail(aid, eid, ids)
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

