<?php 

include('assets/php/utils.php');

$_POST['statut'] = "admin";
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
        
        <!-- SmtpJS -->


        <!-- Main --> 
            <script src="assets/js/utils.js"></script>

        <script type="text/javascript">

        $(document).ready(function(){

            var fullHeight = function() {

                $('.js-fullheight').css('height', $(window).height());
                $(window).resize(function(){
                    $('.js-fullheight').css('height', $(window).height());
                });

            };
            fullHeight();

            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
            });

            $(document).on('click', '.desinscription', function (e) {
                inscriptionID = e.target.id;
                unregister(inscriptionID);
            });

            $(document).on('change', '.inscrireAvec', function (e) {

                if(this.checked) {
                    if (paire > 0 )
                    {
                        paire--;
                    } else {
                        alert("Impossible d'inscrire un autre joueur, veuillez vérifier le nombre de joueurs ajoutés.");
                        this.checked = false;
                    }

                } else {
                    paire++;
                }

                if (paire == 0)
                {
                    $('#buttonInscrire').addClass("btn-primary");  
                    $('#buttonInscrire').prop('disabled', false);
                } else {
                    $('#buttonInscrire').removeClass("btn-primary");  
                    $('#buttonInscrire').prop('disabled', true);
                }

            });

            $(document).on('click', '#buttonInscrire', function (e) {

                if (confirm("Êtes-vous sûr de vouloir vous inscrire à l'évenement?"))
                {
                
                    let joueursID = [];

                    $('.inscrireAvec:checkbox:checked').each(function () {
                            joueursID.push(parseInt(this.id));
                    });
                    
                    
                    registerToEventWith(joueursID, aid);

                    if (SOSenabled)
                    {
                        // On desinscrit les joueurs SOS
                        unregisterSOSpartenaire(joueursID);
                    }
                    
                    notifyRegisterByMail(eid, joueursID);
                    
                    

                }
                


            });


            $(document).on('click', '#buttonInscrirePL', function (e) {

                if (confirm("Êtes-vous sûr de vouloir vous inscrire à l'évenement?"))
                {

                    let joueursID = [aid];
                    
                    registerToEventWith(joueursID, aid);                   

                    notifyRegisterByMail(eid, joueursID);
                    
                }

            });

            $('#buttonSOS').on('click', function() {

                if ($('#tableSOS').is(':hidden'))
                {
                    if (confirm('Êtes-vous sûr de vouloir rejoindre SOS partenaire?'))
                    {
                        registerSOSpartenaire();
                        $('#tableSOS').show();          

                    }
                } else {
                    if (confirm('Êtes-vous sûr de vouloir vous desinscrire de SOS partenaire?'))
                    {
                        unregisterSOSpartenaire([]);
                        $('#tableSOS').hide();
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

            var eid = <?php echo $_GET['eid'] ?>;
            var ety = null;
            var aid = <?php echo intval($_COOKIE['logged']) ?>;

            var anom = '<?php echo $_COOKIE['nom'] ?>';
            var inscrit = false;
            var paire = 0;
            var SOSenabled = false;

            initWithEvent();

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
                     

                    if (SOSenabled)
                    {
                        $('.pourInscrire').show();
                        $('.JO').hide();    // Cache table joueurs
                        $('.PA').hide();    // Cache table favoris

                        $('#buttonSOS').show();
                        $('#tableSOS').show();

                        $.post('assets/sql/interface.php',
                        {
                            function: 'getPlayersRegisteredForSOSpartenaire',
                            eid: eid,
                            aid: aid,
                        }, function(data) {
                            data = JSON.parse(data);
                            populateSOSpartenaire(data);

                        });

                    }

                    // Si n'est pas inscrit et SOS desactivé
                    if (!inscrit && !SOSenabled)
                    {
                            $( ".pourInscrire" ).show();

                            switch (ety)
                            {
                                case 0: // Evenement 
                                    $('.JO').hide();    // Cache table joueurs
                                    $('.PA').hide();    // Cache table favoris
                                    $('#buttonInscrire').removeAttr("disabled");
                                    $('#buttonInscrire').addClass("btn-primary");  
                                    $('#buttonInscrire').prop('disabled', false);
                                    $('#buttonInscrire').prop('id', 'buttonInscrirePL');
                                break;
                                                         
                                case 1: // tournoi
                                    $('#buttonSOS').show();
                                break;

                                case 2:                 // Partie Libre 
                                    $('.JO').hide();    // Cache table joueurs
                                    $('.PA').hide();    // Cache table favoris
                                    $('#buttonInscrire').removeAttr("disabled");
                                    $('#buttonInscrire').addClass("btn-primary");  
                                    $('#buttonInscrire').prop('disabled', false);
                                    $('#buttonInscrire').prop('id', 'buttonInscrirePL');
                                break;

                                case 3: // Compétition
                                    
                                break;
                            }
                            var ignore = [];


                            // On se rajoute à l'array, car on ne veut pas être afficher dans la liste de joueurs
                            ignore.push([aid]);

                            /*
                                Particulier ici, la fonction qui 'getEveryMembers' prends un argument 'Except'
                                qui permet d'ignorer certains membres 'not in'. 
                                Le problème c'est que 'getPlayersRegisteredForEvent' à pour [0] NumPaire, et,
                                get.. prends l'id du joueur pour comparer.
                            */

                            for (let index = 0; index < alreadyRegistered.length; index++) {
                                alreadyRegistered[index][0] = alreadyRegistered[index][1];
                                ignore.push(alreadyRegistered[index]);
                            }


                            // III. Récupère favoris
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
                                    }
                                },
                            });

                            // Push favoris to ignore list
                            for (let index = 0; index < favoris.length; index++) {
                                ignore.push(favoris[index]);
                            }

                            // IV. Table des joueurs disponibles
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

            function unregister(iid)
            {

                if (confirm("Êtes-vous sûr de vouloir vous desinscrire de l'évenement?"))
                {
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
                            function: 'unregisterFromEvent',
                            iid: iid,
                        }, function(data) {
                            
                                if (data)
                                {
                                    alert('Une erreur est survenue!\n' + data);
                                } else {
                                    document.location.reload(true);
                                }

                        });


                    

                }

            }

            function maSituationInscrits(data)
            {

                if (data.length > 0)
                {
                    // 1. Vérifie si l'adherent est inscrit
                    i = 0;
                    while (i < data.length && data[i]['id'] != aid)
                    {
                        i++;
                    }


                    var noms = "";
                    var prenoms = "";

                    // 2. Vérifie si l'adherent est inscrit
                    if (i < data.length)
                    {

                        // ID de l'inscription
                        var iid = data[i][0];
                        
                        var pid = data[i]['NumPaire'];
                        
                        /* 
                            Récupères le nombre de membre de la paire
                            à savoir, les membres sont tous cote a cote
                            dans l'array car la requête est faite avec un
                            order by sur l'id de la paire.
                        */

                        // Lit jusqu'a changement de paire et construit l'affichage
                        // pour la table.
                        
                        while (i < data.length && data[i]['NumPaire'] == pid)
                        {
                            noms     += data[i]['nom'] + "</br>";
                            prenoms  += data[i]['prenom'] + "</br>";
                            i++;
                        }

                        var membresDeLaPaire = i;

                        $('#tableSituation > tbody').append(
                            '<tr>' +
                                `<td> ${pid}</td>` +
                                `<td>${noms}</td>` +
                                `<td>${prenoms}</td>` +
                                `<td><button id="${iid}" type="button" class="btn btn-danger desinscription">Se desincrire</button></td>` +
                            '</tr>'
                        );

                        own_pid = pid;
                    } else {
                        own_pid = -1;
                    }
                        
                        i = 0;

                        while (i < data.length)
                        {
                            
                            pid = data[i][0];
                            noms = "";
                            prenoms = "";
                            
                            if (pid != own_pid)
                            {
                                while (i < data.length && pid == data[i]['NumPaire'])
                                {
                                    pid = data[i][0];
                                    noms     += data[i]['nom'] + "</br>";
                                    prenoms  += data[i]['prenom'] + "</br>";
                                    i++;

                                }
                            
                                
                                $('#tableInscrit > tbody').append(
                                    '<tr>' +
                                        `<td> ${pid}</td>` +
                                        `<td>${noms}</td>` +
                                        `<td>${prenoms}</td>`+ +
                                    '</tr>'
                                );

                                
                            } else {
                                i++;
                            }

                        }

                    // Si notre paire id (c'est à dire nous même) est présent dans les inscrits et n'est pas égale à 'introuvable' alors : vrai sinon faux
                    return own_pid != -1;
                }



            }

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
                              " / Stade: " + event['stade'];

                        $('#more').text(tmp);
                    break;
                }

            }

            function createCallendarForOneEvent(event)
            {
                var calendarEl = document.getElementById('calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    locale: 'fr',
                    height: 150,
                    plugins: [ 'dayGrid' ],
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

            function registerToEventWith(joueursID)
            {

                    $.post('assets/sql/interface.php',
                        {
                            function: 'registerToEventWith',
                            aid: aid,
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

            function registerSOSpartenaire()
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
       
            function unregisterSOSpartenaire(joueursId)
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
       

            function notifyRegisterByMail(eid, ids)
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

        });
            


        </script>
            

    </head>


<style>



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

                    <?php if ($_POST['statut'] == "admin")
                    {
                        echo '<li>
                                <a href="admin.php"><span class="fa fa-table mr-3"></span>Gestion administrateur</a>
                              </li>';
                    }
                    ?>

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
                                <tbody> </tbody>
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