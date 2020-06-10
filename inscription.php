<?php 

include('assets/php/utils.php');

if (!logged())
{
    echo "<script>alert(\"Vous n'êtes pas connecté, vous allez être redirigé vers la page de connexion.\"); window.location = 'login.php'; </script>";
}

?>
    
<html>

    <head>

        <!-- Jquery -->    
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

        <!-- BOOTSTRAP -->
            <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet"  crossorigin="anonymous">
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
        
        <!-- FontAwesome -->    
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

        <!-- NavBar -->    
            <link href="assets/css/navbar.css" rel="stylesheet">

        <!-- FullCalendar --> 
            <link href='assets/css/fullcalendar/core/main.css' rel='stylesheet' />
            <link href='assets/css/fullcalendar/daygrid/main.css' rel='stylesheet' />
            <script src='assets/js/fullcalendar/core/main.js'></script>
            <script src='assets/js/fullcalendar/interaction/main.js'></script>
            <script src='assets/js/fullcalendar/daygrid/main.js'></script>

        <!-- DataTable --> 
            <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
            <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
        
        <!-- Main --> 
            <script src="assets/js/utils.js"></script>



        <script type="text/javascript">

        $(document).ready(function(){

            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
            });
            fullHeight();

            function getCheckedIds()
            {
                let joueursID = [];
                // A refaire mdr
                $('.inscrireAvec:checkbox:checked').each(function () {
                        if ($(this).parent().hasClass('paire1'))
                            joueursID.push(parseInt(this.id));
                });
                $('.inscrireAvec:checkbox:checked').each(function () {
                        if ($(this).parent().hasClass('paire2'))
                            if ($(this).hasClass('paire'))
                            {
                                // Précise que c'est une paire   
                                joueursID.push([parseInt(this.id)]);
                            } else {
                                joueursID.push(parseInt(this.id));
                            }
                });
                $('.inscrireAvec:checkbox:checked').each(function () {
                        if ($(this).parent().hasClass('paireRemplacant'))
                            joueursID.push(parseInt(this.id));
                });
                return joueursID;
            }

            var checked = 0;
            $(document).on('change', '.inscrireAvec', function (e) {

                
                if(this.checked) 
                {

                    if (checked < paire)
                    {
                        
                        /*
                            Inscription avec une paire
                        */
                        if ($(this).hasClass('paire'))
                        {
                            if (checked + 2 <= paire)
                            {
                                $(this).parent().addClass("paire2"); 
                                checked = checked + 2;
                            } else {
                                alert("Impossible d'inscrire un autre joueur, veuillez vérifier le nombre de joueurs ajoutés.");
                                this.checked = false;
                            }

                        /*
                            Forme une paire
                        */
                        } else {

                            if (checked > 2) { 
                                $(this).parent().addClass("paireRemplacant");
                            } else {
                                if (checked > 0 ){
                                    $(this).parent().addClass("paire2"); 
                                } else {
                                    $(this).parent().addClass("paire1"); 
                                }
                            }
                            checked++;
                        }

                    } else {
                        alert("Impossible d'inscrire un autre joueur, veuillez vérifier le nombre de joueurs ajoutés.");
                        this.checked = false;
                    }

                } else {
                    if ($(this).hasClass('paire'))
                    {
                        checked = checked-2;
                    } else {
                        checked--;
                    }

                    $(this).parent().removeClass("paire1"); 
                    $(this).parent().removeClass("paire2"); 
                    $(this).parent().removeClass("paireRemplacant"); 
                }
            
                /*
                    Vérifie paire cohérente
                */

                if (checked % 2 != 0 && checked <= paire)
                {
                    buttonInscrire(true);
                } else {
                    if (checked > 3)
                    {
                        buttonInscrire(true);
                    } else {
                        buttonInscrire(false);
                    }
                }

            });


            function buttonInscrire(en)
            {
                if (en)
                {
                    $('#buttonInscrire').addClass("btn-primary");  
                    $('#buttonInscrire').prop('disabled', false);
                } else {
                    $('#buttonInscrire').removeClass("btn-primary");  
                    $('#buttonInscrire').prop('disabled', true);
                }
            }

            $(document).on('click', '.inscription', function (e)
            {
            
                let pid = e.target.id;
                if (confirm('Êtes vous sûr de vouloir rejoindre la cet paire de remplaçant?'))
                {
                    registerRemplacant(aid, pid);
                }

            });

            $(document).on('click', '#buttonInscrire', function (e) {

                if (confirm("Êtes-vous sûr de vouloir vous inscrire à l'évenement?"))
                {
                
                    let joueursID = [];
                    // On se met dans l'array;
                    joueursID.push(aid);
                    // On ajoute les autres membres
                    joueursID = joueursID.concat(getCheckedIds());
                    console.log(joueursID);

                    for (let i = joueursID.length; 6 >= joueursID.length; i++) {

                        joueursID.push("NULL");  
                    }

                    registerToEventWith(eid, joueursID);       

                    if (SOSenabled)
                    {
                        // On desinscrit les joueurs SOS
                        unregisterSOSpartenaire(aid, eid, joueursID);
                    }
                    
                   
                    notifyRegisterByMail(aid, eid, joueursID);
                   
                    
                }
                


            });

            $(document).on('click', '#buttonInscrirePL', function (e) {

                if (confirm("Êtes-vous sûr de vouloir vous inscrire à l'évenement?"))
                {

                    let joueursID = [aid];
                    for (let i = joueursID.length; 6 >= joueursID.length; i++) {
                        joueursID.push("NULL");  
                    }
                    
                    registerToEventWith(eid, joueursID);        
                               
                    notifyRegisterByMail(aid, eid, joueursID);

                }

            });

            $('#buttonSOS').on('click', function() {

                if ($('#tableSOS').is(':hidden'))
                {
                    if (confirm('Êtes-vous sûr de vouloir rejoindre SOS partenaire?'))
                    {
                        registerSOSpartenaire(aid, eid);
                        $('#tableSOS').show();          

                    }
                } else {
                    if (confirm('Êtes-vous sûr de vouloir vous desinscrire de SOS partenaire?'))
                    {
                        unregisterSOSpartenaire(aid, eid, []);
                        $('#tableSOS').hide();
                    }
                }
            });

            
            $(document).on('click', '.desinscription', function (e) {

                // Id d'identification
                // iid pour compétition et partie libres
                // pid pour tournoi
                let id = e.target.id;

                /*
                    Désinscription de remplaçant
                    Désinscription intégrale
                    Desinscription de paire
                    Desinscription de paire isolée
                */
                if ($(this).hasClass('remplacant'))
                {   // Compétition mais inscrit en remplaçant
                    if (confirm("Êtes-vous sûr de vouloir vous retirer des remplaçants?"))
                    {
                        unregisterFromRemplacant(aid, id);
                    }
                } else {
                    
                    if ($(this).hasClass('competition') && confirm("Êtes-vous sûr de vouloir vous desinscrire de l'évenement?"))
                    {
                        // Désinscription intégrale
                        unregisterPaires(aid, eid, id);
                    } else {    
                        // Tournoi
                        if ($(this).hasClass('paireIsolee') && confirm("Êtes-vous sûr de vouloir vous desinscrire de l'évenement?"))
                        {
                            // Desinscription de paire isolée
                            deletePaireIsolee(id);
                        } else {
                            // Desinscription de paire
                            let error = unregisterPaire(id, eid);
                            if (error.length > 0)
                            {
                                alert('Une erreur est survenue: \n' + error);
                            }

                        }

                    }
                }


            });

            var tableJoueurs = $('#tableJoueurs').DataTable({
               paging: false,
               ordering: false,
               info: false,
               pageLength: 10,
               language: {
                    "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json"
                    
               },
               columns: [
                    { "width": "5%"},
                    { "width": "40%"},
                    { "width": "40%"},
                    { "width": "10%", "orderable": false }
                ]
            });

            var tableMesFavoris = $('#tableMesFavoris').DataTable({
               paging: false,
               ordering: false,
               info: false,
               pageLength: 10,
               searching: false,
               language: {
                    "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json"
                    
               },
               columns: [
                    { "width": "10%"},
                    { "width": "40%"},
                    { "width": "40%"},
                    { "width": "10%", "orderable": false }
                ]
            });

            var tableSOSpartenaire = $('#tableSOS').DataTable({
               paging: false,
               ordering: false,
               info: false,
               pageLength: 10,
               searching: false,

               language: {
                    "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json"
                    
               },
               columns: [
                    { "width": "10%"},
                    { "width": "40%"},
                    { "width": "40%"},
                    { "width": "10%", "orderable": false }
                ]
            });

            // IMPORTANT ! 

            var aid = <?php echo intval($_COOKIE['logged']) ?>;
            var user = getUser(aid);
            var statut = user['statut'];
            var anom = user['nom'];
            var admin = user['statut'] == "Administrateur"; 
            if (admin) $('#gestionBase').show();
            var eid = <?php echo $_GET['eid'] ?>;
            var ety = null;
            var inscrit = false;
            var paire = 0;
            var typeEvenement = 0;
            var SOSenabled = false;


            initWithEvent();

            /*
                Main: met en place l'affichage.
            */
            function initWithEvent()
            {
                
                // 1.             
                    // Populate and construct the calendar
                    // Also shows the description
                        $.ajaxSetup({async: false});  
                        $.post('assets/sql/interface.php',
                        {
                            function: 'getEventInfo',
                            eid: eid,
                        }, function(data) {

                            data = JSON.parse(data);
                            setEventDescription(data);
                        });

                // 2.
                // Populate tables
                

                    // I. Check if player as registered for SOS partenaire
                    $.post('assets/sql/interface.php',
                        {
                            function: 'isRegisteredForSOSpartenaire',
                            eid: eid,
                            aid: aid,
                        }, function(data) {
                            data = JSON.parse(data);

                            SOSenabled = data.length > 0;

                        });
                    
                    
                    // II. Check if player already registered
                    var alreadyRegistered = [];

                    $.post('assets/sql/interface.php',
                        {
                            function: 'getPlayersRegisteredForEvent',
                            eid: eid,
                        }, function(data) {
                            alreadyRegistered = JSON.parse(data);

                            if (data)
                            {
                                inscrit = maSituationInscrits(alreadyRegistered);
                            }

                        });
       
                        
                    // III. Table des paires isolées
                    $.post('assets/sql/interface.php',
                    {
                        function: 'getPairesIsoleesForEvent',
                        eid: eid,
                        exceptPID: [0],
                    }, function(data) {
                        data = JSON.parse(data);

                        if (data.length > 0) 
                        {
                            populatePaireIsolee(data);
                            // Ajoute à les listes des déjà inscrits,
                            // l'index des fonctions qui prennent un $except est [0] c'est sensé être l'id du joueur
                            // ici l'id [1] correspond à l'id du joueur alors:
                            for (let index = 0; index < data.length; index++) {
                                alreadyRegistered.push([parseInt(data[index]['id'])]);
                                
                            }
                        }
                    });

                    if (SOSenabled)
                    {
                        $('.pourInscrire').show();
                        $('.JO').hide();    // Cache table joueurs
                        $('.PA').hide();    // Cache table favoris

                        $('#buttonSOS').show();
                        $('#tableSOS').show();

                    }

                    
                    // IV. Check if players are in SOS partenaire list
                    

                    $.post('assets/sql/interface.php',
                        {
                            function: 'getPlayersRegisteredForSOSpartenaire',
                            eid: eid,
                            aid: aid,
                        }, function(data) {
                            
                            data = JSON.parse(data);
                            // Ajoute les joueurs SOS à la liste des "déjà inscrits"
                            alreadyRegistered = alreadyRegistered.concat(data);
                            populateSOSpartenaire(data);
                        });

                    // Si n'est pas inscrit et SOS desactivé
                    if (!inscrit && !SOSenabled)
                    {
                            $( ".pourInscrire" ).show();

                            switch (ety)
                            {
                                case 0: // Evenement 
                                    $('.JO').hide();    // Cache table joueurs
                                    $('.PA').hide();    // Cache table favoris
                                    $('.paireIsolee').hide(); // Cache table paire isolée
                                    $('#buttonInscrire').removeAttr("disabled");
                                    $('#buttonInscrire').addClass("btn-primary");  
                                    $('#buttonInscrire').prop('disabled', false);
                                    $('#buttonInscrire').prop('id', 'buttonInscrirePL');
                                break;
                                                         
                                case 1: // tournoi
                                    $('#buttonSOS').show();
                                    //$('#tableSOS').show();


                                break;

                                case 2:                 // Partie Libre 
                                    $('.JO').hide();    // Cache table joueurs
                                    $('.PA').hide();    // Cache table favoris
                                    $('.paireIsolee').hide();
                                    $('#buttonInscrire').removeAttr("disabled");
                                    $('#buttonInscrire').addClass("btn-primary");  
                                    $('#buttonInscrire').prop('disabled', false);
                                    $('#buttonInscrire').prop('id', 'buttonInscrirePL');
                                break;

                                case 3: // Compétition
                                    $('.paireIsolee').hide(); // Cache table paire isolée
                                break;
                            }
                            
                            var ignore = [];

                            // On se rajoute à l'array, car on ne veut pas être afficher dans la liste de joueurs
                            ignore.push([aid]);

                            ignore = ignore.concat(alreadyRegistered);

                            // V. Récupère favoris
                            var favoris = [];
                            $.ajax({
                                type: "POST",
                                async: false,
                                url: "assets/sql/interface.php",
                                data: {
                                    function: 'getPlayerFavorite',
                                    aid: aid, 
                                    except: JSON.stringify(ignore),
                                },
                                success: function(data)
                                {

                                    favoris = JSON.parse(data);
                                    if (favoris)
                                    {
                                        mesFavorisCheck(favoris, tableMesFavoris);
                                        // Push favoris to ignore list
                                        ignore = ignore.concat(favoris);
                                    }
                                },
                            });
                            
                            // VI. Table des joueurs disponibles
                            $.post('assets/sql/interface.php',
                            {
                                function: 'getEveryMembers',
                                except: JSON.stringify(ignore),
                            }, function(data) {
                                data = JSON.parse(data);
                                if (data)
                                {
                                    populatejoueurDisponible(data);
                                }

                            });

                    }       
            }


            /*
                Rempli l'entête de l'évenement
            */
            function setEventDescription(event)
            {

                // Common
                createCallendarForOneEvent(event);

                $('#titre').text(event['titre']);
                $('#debut').text("Commence le : " + event['dteDebut']);
                $('#fin').text("Fini le : " + event['dteFin']);
                $('#lieu').text("Place de l'évenement : " + event['commune'] + ", " + event['adresse']);
                
                ety = parseInt(event['type']);

                // On fait partie de la paire
                paire = event['paires']-1; 
                if (event['type'] == 3)
                {
                    paire = 6;
                }

                // Utile pour detecter les inscriptions de compétitions
                typeEvenement = event['type'];

                let tmp = "";

                switch (parseInt(event['type']))
                {
                    case 1:
                        // Tournoi
                        $('#paire').text("Inscription par paire de " + event['paires']);
                        tmp = "Apéro: " + event['apero'] +
                              " / Repas: " + event['repas'];
                        $('#more').text(tmp);
                    break;
                    
                    case 2:
                        // Partie libre
                    break;
                    
                    case 3:

                        // Compétition
                        $('#paire').text("Inscription par paire de " + event['paires']);
                        tmp = "Catégorie: " + event['catComp'] +
                              " / Division: " + event['division'] +
                              " / Public: " + event['public'] +
                              " / Stade: " + event['stade'] +
                              " / Prix: " + event['prix'];

                        $('#more').text(tmp);
                    break;
                }

            }

            /*
                Créer un calendrier pour l'évenement
            */
            function createCallendarForOneEvent(event)
            {
                var calendarEl = document.getElementById('calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    locale: 'fr',
                    height: 150,
                    
                    plugins: [ 'dayGrid', 'interaction'],
                    header: {
                        left: '',
                        center: 'title',
                        right: ''
                    },
                    defaultDate: event['dteDebut'],
                    firstDay: 1,
                    defaultView: 'dayGridWeek',
                    eventLimit: true, // allow "more" link when too many events
                    events: [
                        {
                            title  : event['titre'],
                            start  : event['dteDebut'],
                            end    : event['dteFin'],
                        },
                    ],
                    eventColor: '#ff0000',
                });

                calendar.render();
            }


            /*
                Rempli la table SOS partenaire
            */
            function populateSOSpartenaire(data)
            {

                for (let i = 0; i < data.length; i++) {
                
                tableSOSpartenaire.row.add([
                                    i,
                                    data[i]['nom'],
                                    data[i]['prenom'],
                                    `<td><input type="checkbox" class="inscrireAvec SOS" id="${data[i]['id']}"></input></td>`
                                ]).node().id = i;
                    
                }
                tableSOSpartenaire.draw();

            }
            
            /*
                Rempli la table des joueurs disponibles
            */
            function populatejoueurDisponible(data)
            {
                for (let i = 0; i < data.length; i++) {
                
                    tableJoueurs.row.add([
                                        i,
                                        data[i]['nom'],
                                        data[i]['prenom'],
                                        `<td><input type="checkbox" class="inscrireAvec" id="${data[i]['id']}"></input></td>`
                                    ]).node().id = i;
                        
                }
                tableJoueurs.draw();
            }

            /*
                Rempli la table des paires isolées
            */
            function populatePaireIsolee(data)
            {
                    
                        noms = "";
                        prenoms = "";
                        pid = data[0]['pid'];
                        let option = "";
                        i = 0;
                        while (i < data.length)
                        {


                            if (data[i]['id'] == aid)
                            {
                                option = `<button id='${pid}' class='btn btn-danger desinscription paireIsolee'>Se retirer des paires isolées</button>`;
                            } else {
                                option = `<input type='checkbox' class='inscrireAvec paire' id='${data[i]['pid']}'>`;
                            }

                            while (i < data.length && data[i]['pid'] == pid)
                            {
                                noms    += data[i]['nom'] + "</br>";
                                prenoms += data[i]['prenom'] + "</br>";

                                pid = data[i]['pid'];
                                i++;
                            }

                            


                            $('#tablePaireIsolee > tbody').append(
                                `<tr>` +
                                `<td>${pid}</td>` +
                                `<td>${noms}</td>` +
                                `<td>${prenoms}</td>`+
                                `<td>${option}</td>`+
                            '</tr>'
                            );

                            if (i < data.length && data[i]['pid'] != pid)
                            {
                                added = 0;
                                noms    = "";
                                prenoms = "";
                                pid = data[i]['pid'];
                            }

 

                        }

 
                    
               
                
                    
                

            }

            /*
                Rempli la table table avec des checkbox de classe inscrireAvec en option
                [i] [nom] [prenom] [options]
            */
            function mesFavorisCheck(data, table)
            {

                for (let i = 0; i < data.length; i++) {

                    table.row.add([
                            i+1,
                            data[i]['nom'],
                            data[i]['prenom'],
                            `<td><input type="checkbox" class="inscrireAvec favoris" id="${data[i][0]}"></input></td>`,
                        ]).node().id = i;
                    
                }
                table.draw();

            }

            /*
                Rempli la table table avec des buttons de classe retirerFavori en option.
                [i] [nom] [prenom] [options]
            */
            function mesFavorisButton(data, table)
            {

                for (let i = 0; i < data.length; i++) {

                    table.row.add([
                            i+1,
                            data[i]['nom'],
                            data[i]['prenom'],
                            `<td><button id="${data[i][0]}" type="button" class="btn btn-danger retirerFavori favoris">Retirer favori</button></td>`,
                        ]).node().id = i;
                    

                }
            }

            /*
                Rempli les tables 'Ma situation' et 'Inscrits'
                @return inscrit 
            */
            function maSituationInscrits(data)
            {

                // I.Id, P.pid as NumPaire, A.id, A.nom, A.prenom

                var inscrit = false;
                if (data.length > 0)
                {
                    // 1. Vérifie si l'adherent est inscrit
                    i = 0;
                    while (i < data.length && data[i]['id'] != aid)
                    {
                        i++;
                    }

                    inscrit = i < data.length;

                    var noms = "";
                    var prenoms = "";
                    var option = "";

                    // 2. Vérifie si l'adherent est inscrit
                    if (inscrit)
                    {

                        // ID de l'inscription               
                        var iid = data[i]['iid'];
                       
                        // ID de de la paire du joueur
                        var pid = data[i]['NumPaire'];

                        /*
                            Change le mode de désinscription
                        */
                        switch (ety)
                            {
                                case 0: // Evenement 
                                    option = `<button id='${pid}' type='button' class='btn btn-danger desinscription evenement'>Se desincrire</button>`;
                                break;
                                                         
                                case 1: // tournoi
                                    option = `<button id='${pid}' type='button' class='btn btn-danger desinscription tournoi'>Se desincrire</button>`;
                                break;

                                case 2: // Partie Libre 
                                    option = `<button id='${pid}' type='button' class='btn btn-danger desinscription partieLibre'>Se desincrire</button>`;
                                break;

                                case 3: // Compétition
                                    option = `<button id='${iid}' type='button' class='btn btn-danger desinscription competition'>Se desincrire</button>`;
                                break;
                            }
                        
                        if (data.length > 3)
                        {
                            remplacant = [];
                            paireRemplacant = parseInt(data[0]['NumPaire'])+2;

                            for (let j = 0; j < data.length; j++) {
                                if (data[j]['NumPaire'] == paireRemplacant)
                                {    
                                    remplacant.push(parseInt(data[j]['id']));    
                                }
                            }

                            if (remplacant.includes(aid))
                            {
                                option = `<button id='${iid}' class='btn btn-danger desinscription competition remplacant'>Se retirer de la paire</button>`;
                            } 
                        }

                        // Lit jusqu'a changement d'inscription et construit l'affichage
                        // pour la table.
                        i=0;
                        while (i < data.length && data[i]['iid'] == iid)
                        {
                            noms     += data[i]['nom'] + "</br>";
                            prenoms  += data[i]['prenom'] + "</br>";
                            i++;
                        }
                        
                        $('#tableSituation > tbody').append(
                            '<tr>' +
                                `<td> ${iid}</td>` +
                                `<td>${noms}</td>` +
                                `<td>${prenoms}</td>` +
                                `<td>${option}</td>` +
                            '</tr>'
                        );

                        own_iid = iid;
                    } else {
                        own_iid = -1;
                    }
                        
                        noms = "";
                        prenoms = "";
                        pid = data[0]['NumPaire'];
                        iid = data[0]['iid'];
                        var elementInPaire = 0;
                        let added = 0;


                        i = 0;
                        while (i < data.length)
                        {

                            if (i < data.length && data[i]['iid'] == iid)
                            {

                                while (i < data.length && data[i]['NumPaire'] == pid)
                                {
                                    noms    += data[i]['nom'] + "</br>";
                                    prenoms += data[i]['prenom'] + "</br>";

                                    pid = data[i]['NumPaire'];
                                    i++;
                                    added++;

                                }

                        

                                // Pour les compétitions, si la paire est une paire de remplaçant
                                if (ety == 3 && added > 4)
                                {
                                    // Vérifie si la paire est pleine 
                                    if (added != 7)
                                    {
                                        option = `<button id="${pid}" class='btn btn-danger inscription remplacant'>S'ajouter en remplaçant</button>`;
                                    }
                                    
                                    added = 0;
                                } else {
                                    option = "";
                                }

                                $('#tableInscrit > tbody').append(
                                    `<tr>` +
                                        `<td>${iid}</td>` +
                                        `<td>${noms}</td>` +
                                        `<td>${prenoms}</td>`+
                                        `<td>${option}</td>`+
                                '</tr>'
                                );
                                    


                                if (i < data.length && data[i]['NumPaire'] != pid)
                                {
                                    noms    = "";
                                    prenoms = "";
                                    pid = data[i]['NumPaire'];
                                    iid = data[i]['iid'];
                                }
   

                            }
                            




                        }
                        
            
                    return inscrit;
                }



            }
 

        });
            


        </script>
            

    </head>


<style>


    .paire1 {
        background-color: red;
    }

    .paire2 {
        background-color: blue;
    }

    .paireRemplacant {
        background-color: green;
    }

    .LeftRightHeader { 
        overflow:auto;         
    } 

    .left, .right { 
        padding: 1em; 
        background: white; 
    } 

    .left  { 
        float: left; 
         width: 30%; 
         margin-top: 1%;
         font-size: medium;
    }
    
    .right { 
        float: right;  
        width: 70%; 
    } 

    .eqL {
        float: left;
        width: 50%;
        padding: 1em;
    }
    .eqR {
        float: right;
        width: 50%;
        padding: 1em;
    }

    .btn-mid20 {
        width: 20%;
        
    }

    #inscriptArea{
        overflow-x: hidden;
    }

    .pourInscrire{
        display: none;
    }

</style>

    <body>
        
        <div class="wrapper d-flex align-items-stretch">
            
            <nav id="sidebar">

                <div class="custom-menu">
                    <button type="button" id="sidebarCollapse" class="btn btn-primary"></button>
                </div>

                <div class="img bg-wrap text-center py-4" style="background-image: url(ressources/img/bg_1.webp);">
                    <div class="user-logo">
                        <div class="img" style="background-image: url(ressources/img/user.jpg);"></div>
                        <h3><?php echo $_COOKIE['nom'] . " " . $_COOKIE['prenom'] ?></h3>
                    </div>
                </div>

                <ul class="list-unstyled components mb-5">

                    <li>
                        <a href="index.php"><span class="fa fa-home mr-3"></span> Agenda</a>
                    </li>

                    <li>
                        <a href="#"><span class="fa fa-users mr-3 active"></span> Inscription</a>
                    </li>

                    <li>
                        <a href="profil.php"><span class="fa fa-gift mr-3"></span> Profil / Partenaires </a>
                    </li>
                    
                    <li>
                        <a style="display: none" id="gestionBase" href="admin.php"><span class="fa fa-table mr-3"></span>Gestion administrateur</a>
                    </li>

                    <li>
                        <a href="login.php?logoff"><span class="fa fa-sign-out mr-3"></span> Se déconnecter</a>
                    </li>
      
                </ul>

            </nav>

            <div id="content" class="p-4 p-md-5 pt-5">
                
                <h2 id="titre"></h2>
                
                <div id="eventHeader" class="LeftRightHeader">
                    <div id="headerText" class="left">
                        <span id="debut">Commence le: 13/08/2001 à 13h00</span></br>
                        <span id="fin">Fini le: 13/08/2001 à 13h00</span></br>
                        <span id="lieu">Lieu: Loudun, adresse</span></br>
                        <span id="paire"></span></br>
                        <span id="more"></span></br>
                    </div>
                    <div id='calendar' class="right"></div>
                </div>
                
                <div id="inscriptArea" class="LeftRightHeader">

                    <div class="eqL">
  
                        <h3>Ma situation: </h3>
                            <table class="table" id="tableSituation">
                                <thead>
                                    <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Nom</th>
                                    <th scope="col">Prénom</th>
                                    <th scope="col">Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   
                                </tbody>
                            </table>

                        <div class="pourInscrire PA">
                            <h3>Partenaire favoris: </h3>
                                <table class="table" id="tableMesFavoris">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nom</th>
                                            <th>Prénom</th>
                                            <th>Inscrire avec</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                        </div>
                        <div class="pourInscrire SOS">
                            <h3 style="display: none;" id="buttonSOS">SOS Partenaire: <button class="btn btn-primary">Activer/Desactiver</button></h3>
                            <table style="display: none;" class="table" id="tableSOS">
                                <thead>
                                    <tr>
                                    <th>#</th>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Options</th>
                                    </tr>
                                </thead>
                                <tbody>


                                </tbody>
                            </table>
                        </div>
                        
                        <div style="display: none;" class="pourInscrire paireIsolee">
                            <h3>Paire isolées: </h3>
                            <table class="table" id="tablePaireIsolee">
                                <thead>
                                    <tr>
                                    <th>#</th>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Options</th>
                                    </tr>
                                </thead>
                                <tbody>


                                </tbody>
                            </table>
                        </div>


                    </div>

                    <div class="eqR">


                        <h3>Inscrits: </h3>
                            <table class="table" id="tableInscrit">
                                <thead>
                                    <tr>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Nom</th>
                                        <th scope="col">Prénom</th>
                                        </tr>
                                    </tr>
                                </thead>
                                <tbody> 


                                
                                </tbody>
                            </table>

                        <div class="pourInscrire JO">
                            
                            <h3>Joueurs disponibles: </h3>
                                <table class="table" id="tableJoueurs">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Last</th>
                                            <th>First</th>
                                            <th>Options</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <tbody>
                                   
                                    </tbody>
                                    </tbody>

                                </table>

                        </div>   
                        
                        
                    </div>

                </div>
                    
                <footer class="footer">
                        <div class="container-fluid">
                            <div class="row text-center">
                                <div style="padding: 1em; "class="col-lg-12">      
                                    <button id="buttonInscrire" type="button" class="btn btn-secondary btn-lg btn-mid20" disabled>S'inscire</button>
                                </div>
                            </div>
                        </div>        
                 </footer>

            </div>
                

       
        </div>



        
    </body>
</html>