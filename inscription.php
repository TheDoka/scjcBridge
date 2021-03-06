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
        

        <!-- Moment --> 
            <script src="https://momentjs.com/downloads/moment-with-locales.min.js" crossorigin="anonymous"></script>  
 
        <!-- waitMe -->  
            <link rel="stylesheet" href="assets/css/waitMe.min.css">
            <script src="assets/js/waitMe.min.js" crossorigin="anonymous"></script>

        <!-- Main --> 
            <script src="assets/js/utils.js"></script>



        <script type="text/javascript">

        $(document).ready(function(){
            /*
                Gestion de la sidebar
            */
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
            });
            fullHeight();

            /* 
                Variables globales
            */
            var eid = <?php echo $_GET['eid'] ?>;
            var aid = <?php echo intval($_COOKIE['logged']) ?>;
            var user;
            var anom;
            var permissionsJoueur = [];
            
            // Bool
            var inscrit = false;
            var SOSenabled;
            var paireIsolee;
            var descriptionAffiche;
        
            // Int
            var paire, paire1, paire2, remplacant;
            var checked;

        

            // ----------------------------- Vérification post-inscription ----------------------
                
                /*
                    Récupère les ID des éléments .inscrireAvec coché.
                    @return joueursID -> []
                */
                function getCheckedIds()
                {
                    let joueursID = [];

                    /*
                        Remarques: les requêtes sont faites dans cet ordre car la fonction qui traite
                                les inscriptions est faite pour recevoir la paire 1 au début de l'array
                                etc...
                    */
                    $('.inscrireAvec:checkbox:checked').each(function () {
                            if ($(this).parent().hasClass('paire1'))
                            {
                                if ($(this).hasClass('paire'))
                                {
                                    joueursID.push([parseInt(this.id)]);
                                } else {
                                    joueursID.push(parseInt(this.id));
                                }
                            }
                    });
                    
                    $('.inscrireAvec:checkbox:checked').each(function () {
                            if ($(this).parent().hasClass('paire2'))
                            {
                                if ($(this).hasClass('paire'))
                                {
                                    joueursID.push([parseInt(this.id)]);
                                } else {
                                    joueursID.push(parseInt(this.id));
                                }
                            }
                    });

                    $('.inscrireAvec:checkbox:checked').each(function () {
                            if ($(this).parent().hasClass('paireRemplacant'))
                            {
                                joueursID.push(parseInt(this.id));
                            }
                            
                    });
                    return joueursID;
                }

                /*
                    Vérifie si la case est cochable et lui attribut sa couleur de paire.
                    e: this <-> inputbox.id
                    @return /
                */
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
                                    if (havePermission(permissionsJoueur,10)) // Permission 10: Inscrire d'autres personnes que soi  
                                    {
                                        if (checked == 2 && paire2 == 2)
                                        {
                                            $(this).parent().addClass("paire1");
                                            checked = checked + 2;
                                            paire1 = 2;
                                        } else {
                                            $(this).parent().addClass("paire2"); 
                                            checked = checked + 2;
                                            paire2 = 2;
                                        }
                                    } else {
                                    
                                        $(this).parent().addClass("paire2"); 
                                        checked = checked + 2;
                                        paire2 = 2;

                                    }


                                    
                                } else {
                                    alert("Impossible d'inscrire un autre joueur, veuillez vérifier le nombre de joueurs ajoutés.");
                                    this.checked = false;
                                }

                            /*
                                Forme une paire
                            */
                            } else {

                                if (paire2 == 2) { 
                                    if (paire1 < 2)
                                    {
                                        $(this).parent().addClass("paire1");
                                        paire1++;
                                    } else {
                                        $(this).parent().addClass("paireRemplacant");
                                        remplacant++;
                                    }
                                } else {
                                    if (paire1 < 2 ){
                                        $(this).parent().addClass("paire1"); 
                                        paire1++;
                                    } else {
                                        $(this).parent().addClass("paire2"); 
                                        paire2++;
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

                                if ($(this).parent().hasClass("paire2"))
                                {
                                    checked = checked-2;
                                    paire2 = paire2 - 2;
                                    $(this).parent().removeClass("paire2"); 
                                } else {
                                    checked = checked-2;
                                    paire1 = paire1 - 2;
                                    $(this).parent().removeClass("paire1"); 
                                    
                                }

                        } else {
                            checked--;
                            
                            if ($(this).parent().hasClass('paire1'))
                            {                   
                                $(this).parent().removeClass("paire1"); 
                                paire1--;
                            }
                            if ($(this).parent().hasClass('paire2'))
                            { 
                                $(this).parent().removeClass("paire2"); 
                                paire2--;
                            }
                            if ($(this).parent().hasClass('paireRemplacant'))
                            {
                                $(this).parent().removeClass("paireRemplacant"); 
                                remplacant--;
                            }
                        }

                        
                        
                    }
                
                    /*
                    console.log("Paire 1: " + paire1 + "\n");
                    console.log("Paire 2: " + paire2 + "\n");
                    console.log("Paire r: " + remplacant + "\n");
                    console.log("chk: " + checked + "\n");
                    console.log("------------------------------------\n");
                    */

                    /*
                        Vérifie paire cohérente
                    */
                    if (ety == 3)
                    {
                        if (checked >= paire-3)
                        {
                            buttonInscrire(true);
                        } else {
                            buttonInscrire(false);
                        }
                    } else {
                        if (checked == 1 && ety == 1)
                        {
                            buttonInscrire(true);
                        } else {
                            if (checked == paire)
                            {
                                buttonInscrire(true); 
                            } else {
                                buttonInscrire(false);
                            }
                            
                        }
                    }

                });


                /*
                    Interrupteur pour le boutton inscrire
                    state: true/false
                    @return /
                */
                function buttonInscrire(state)
                {
                    if (state)
                    {
                        $('#buttonInscrire').addClass("btn-primary");  
                        $('#buttonInscrire').prop('disabled', false);
                    } else {
                        $('#buttonInscrire').removeClass("btn-primary");  
                        $('#buttonInscrire').prop('disabled', true);
                    }
                }

            // ----------------------------- Inscriptions/Desinscription ------------------------
   
                /*
                    Inscrit le joueur
                    @return /
                */
                $(document).on('click', '#buttonInscrire', function () {

                    if (confirm("Confirmez-vous votre inscription ?"))
                    {
                        
                        let joueursID = [];
                        
                        if (paireIsolee == 0 && !havePermission(permissionsJoueur,10)) // Permission 10: Inscrire d'autres personnes que soi 
                        {
                            // On se met dans l'array;
                            joueursID.push(aid);
                        } else {

                            if (!havePermission(permissionsJoueur,10))
                                joueursID.push([paireIsolee]);

                            // Permet l'inscription de l'administrateur sur les événements ou une seule personne est requise
                            if (havePermission(permissionsJoueur,10) && paire == 1)
                                joueursID.push(aid);
                        }

                        // On ajoute les autres membres
                        joueursID = joueursID.concat(getCheckedIds());

                        /*
                            Inscription avec une paire isolée
                            Si tournoi et
                            seulement moi et un adherent et une paire
                            et cette paire doit être la paire 2
                            [0] aid
                            [1] joueur 
                        */

                        if (ety == 1 && joueursID.length == 2 && paire == 3 && !Array.isArray(joueursID[1]))
                        {
                            registerIsolees(eid, joueursID);
                        } else {
                            /*
                                Inscription normale
                            */
                            for (let i = joueursID.length; 6 >= joueursID.length; i++) {

                                joueursID.push("NULL");  
                            }

                            if (SOSenabled)
                            {
                                // On desinscrit les joueurs SOS
                                unregisterSOSpartenaire(aid, eid, joueursID);
                            }

                            notifyRegisterByMail(aid, eid, joueursID);
                            registerToEventWith(eid, joueursID);    

                    
                        }

                    }
                    

            
                });

                /*
                    Inscrit le joueur à une paire de remplaçant
                    e: pid -> paire éxistante
                    iid -> paire inéxistante
                    @return /
                */
                $(document).on('click', '.inscription', function (e)
                {
                
                    // Varie selon l'action, peut être pid/iid

                    let id = e.target.id;
                    // Créer la paire remplacante ou bien ajoute à la paire des remplacant
                    if (confirm('Êtes vous sûr de vouloir rejoindre cette équipe en tant que remplaçant?'))
                    {
                        if ($(this).hasClass('new'))
                        {
                            let pid = createNewPaireRemplacement(id);
                            registerRemplacantToPid(aid, pid);
                        } else {
                            
                            registerRemplacantToPid(aid, id);
                            
                        }
                    }
                });

                /*
                    Interrupteur pour SOS partenaire
                    @return /
                */
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

                /*
                    Desinscrit le joueur
                    e: iid pour compétition et partie libres
                    pid pour tournoi
                    @return /
                */
                $(document).on('click', '.desinscription', function (e) {

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
                            unregisterPaires(eid, id, aid);
                            
                        } else {    
                            // Tournoi
                            if ($(this).hasClass('paireIsolee') && confirm("Êtes-vous sûr de vouloir vous desinscrire de l'évenement?"))
                            {
                                // Desinscription de paire isolée
                                deletePaireIsolee(id);
                            } else {
                                // Desinscription de paire
                                if (!$(this).hasClass('competition') && !$(this).hasClass('paireIsolee') && confirm("Êtes-vous sûr de vouloir vous desinscrire de l'évenement?"))
                                {

                                    let error = unregisterPaire(id, eid, aid);
                                    if (error.length > 0)
                                    {
                                        alert('Une erreur est survenue: \n' + error);
                                    }
                                    document.location.reload(true);
                                }

                            }

                        }
                    }


                });

            // ----------------------------- Affichage entête description/agenda ----------------

                /*
                    Remplit l'entête de l'évenement
                    @return /
                */
                function setEventDescription(event)
                {

                    // Common
                    if (!descriptionAffiche)
                    {
                        descriptionAffiche = true;
                        createCallendarForOneEvent(event);
                    }
                    
                    $('#titre').text(event['titre']);
                    moment.locale('fr');

                    $('#debut').text("Commence à " + moment(event['dteDebut']).format("HH:mm ") + '(' + moment(event['dteDebut']).add(15, 'minutes').format('HH:mm') + ' cartes en main.)');
                    
                    $('#fin').text("Fini à " + moment(event['dteFin']).format("HH:mm "));
                    $('#lieu').text("Lieu de l'évènement : " + event['commune'] + ", " + event['adresse']);
                    
                    ety = parseInt(event['type']);

                    if (!havePermission(permissionsJoueur,10))// Permission 10: Inscrire d'autres personnes que soi 
                    {
                        // On fait partie de la paire
                        paire = event['paires']-1; 
                    } else {
                        //Permet de former des inscriptions sans nous même
                        paire = event['paires'];
                    }
                
                    if (event['type'] == 3) // compétition, on ajoute la possibilité de remplaçants
                    {
                        paire = 6;
                    }

                    // Utile pour detecter les inscriptions de compétitions
                    let typeEvenement = event['type'];

                    let tmp = "";

                    switch (parseInt(event['type']))
                    {
                        case 1:
                            // Tournoi
                            if (event['paires'] == 2)
                            {
                                $('#paire').text("Inscription par paire de " + event['paires']);
                            } else {
                                $('#paire').text("Patton par " + event['paires']);
                                
                            }
                            if (event['apero'] == 1 || event['repas'] == 1)
                            {
                                tmp = "En plus:<br>";

                                if (event['apero'] == 1)
                                    tmp += "* Apéro amélioré <br>";
                                
                                if (event['repas'] == 1)
                                    tmp += "* P’tite bouffe partagée\n";
                                
                                
                                $('#more').html(tmp);
                            }

                            
                        break;
                        
                        case 2:
                            // Partie libre
                        break;
                        
                        case 3:

                            // Compétition
                            if (event['paires'] == 2)
                            {
                                $('#paire').text("Inscription par paire de " + event['paires']);
                            } else {
                                $('#paire').text("Inscription par équipe");
                            }
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
                    @return /
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




            // ------------- Créer affichage unique pour les évenements spéciaux ----------------
                /*
                    Créer l'affichage pour les évenements spéciaux
                    data: event
                    @return /
                */
                function populateSpecialEvent(event)
                {
                    let raccordes = getEvenementsRaccorde(eid);   
                    let content = "";
                    let situation = [];
                    let situationText;
                    let option;
                    let pid = 0;
                    let currEventSituation;
                    let i = 0;

                    raccordes.forEach(event => {
                        situation = isRegisteredForEvent(event['id'], aid);
                        if (situation.length > 0)
                        {
                            situationText = "Inscrit avec: </br>";
                            i++;
                            situation.forEach(joueur => {
                                situationText += `${joueur['nom']} ${joueur['prenom']} </br>`;
                                pid = joueur['NumPaire'];
                            });

                            if (havePermission(permissionsJoueur,9)) //Permission 9: Se désinscrire d'un événement
                            {
                                switch (parseInt(event['type']))
                                {
                                                            
                                    case 1: // tournoi
                                        option = `<button id='${pid}' type='button' class='btn btn-danger desinscription tournoi'>Se desincrire</button>`;
                                    break;

                                    case 2: // Partie Libre 
                                        option = `<button id='${pid}' type='button' class='btn btn-danger desinscription partieLibre'>Se desincrire</button>`;
                                    break;

                                    case 3: // Compétition
                                        option = `<button id='${iid}' type='button' class='btn btn-danger desinscription competition'>Se desincrire</button>`;
                                    break;

                                    case 4: // Evenement 
                                        option = `<button id='${pid}' type='button' class='btn btn-danger desinscription evenement'>Se desincrire</button>`;
                                    break;
                                    
                                    case 5: // Spéciaux 
                                        
                                    break;
                                }
                            }

                        } else {           
                            situationText = "Non inscrit";  
                            option = `<a href="inscription.php?eid=${event['evenementId']}" class='btn btn-dark'>Voir l'évenement</a>`;
                        }

                        content += `<tr>
                                        <td><a href="inscription.php?eid=${event['evenementId']}">${event['titre']}</a></td>
                                        <td>${situationText}</td>
                                        <td>${option}</td>
                                    </tr>
                                `;
                    });

                    if (i == raccordes.length)
                    {
                        currEventSituation = "Inscrit à tout.";

                    } else {
                        if (i == 0)
                        {
                            currEventSituation = "Inscrit à rien.";
                            situation = isRegisteredForEvent(eid, aid);
                            if (situation.length > 0)
                            {
                                let joueursID = [];
                                joueursID.push(aid);
                                for (let i = joueursID.length; 6 >= joueursID.length; i++) {
                                    joueursID.push("NULL");  
                                }

                                unregisterPaires(eid, situation[0]['iid'], aid);
                            }
                        } else {
                            if (i != raccordes.length)
                            {
                                // vérifie si l'on est déjà inscrit
                                if (isRegisteredForEvent(eid, aid).length == 0)
                                {
                                    let joueursID = [];
                                    joueursID.push(aid);
                                    for (let i = joueursID.length; 6 >= joueursID.length; i++) {
                                        joueursID.push("NULL");  
                                    }
                                    registerToEventWith(eid, joueursID);
                                }

                                currEventSituation = "Partiellement inscrit.";
                            }
                        }
                    }

                    content += `<tr>
                                    <td><a href="#">Evenement actuel</a></td>
                                    <td>${currEventSituation}</td>
                                    <td>/</td>
                                    </tr>
                                `;

                    var html = `<table class="table display" cellspacing="0" width="100%" id="tablePaireIsolee">
                                    <thead>
                                        <tr>
                                            <th>Nom de l'évenement</th><th>Situation</th><th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    ${content}
                                    </tbody>
                                </table>`
                    


                    $("#inscriptArea").append(html);
                }

            // ----------------------------- Initialise les tables ------------------------------

                 var tableJoueurs = $('#tableJoueurs').DataTable({
                    paging: true,
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
                            { "width": "15%", "orderable": false }
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
                            { "width": "5%"},
                            { "width": "40%"},
                            { "width": "40%"},
                            { "width": "15%", "orderable": false }
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
                            { "width": "5%"},
                            { "width": "40%"},
                            { "width": "40%"},
                            { "width": "15%", "orderable": false }
                        ]
                });

            // ----------------------------- Remplit les tables ---------------------------------

                /*
                    Remplit la table SOS partenaire
                    data: event
                    @return /
                */
                function populateSOSpartenaire(data)
                {

                    for (let i = 0; i < data.length; i++) {
                    
                    tableSOSpartenaire.row.add([
                                        i+2,
                                        data[i]['nom'],
                                        data[i]['prenom'],
                                        `<td><input type="checkbox" class="inscrireAvec SOS" id="${data[i]['id']}"></input></td>`
                                    ]).node().id = i;
                        
                    }
                    tableSOSpartenaire.draw();

                }
                
                /*
                    Remplit la table des joueurs disponibles
                    data: event
                    @return /
                */
                function populatejoueurDisponible(data)
                {
                    for (let i = 0; i < data.length; i++) {
                    
                        tableJoueurs.row.add([
                                            i+1,
                                            data[i]['nom'],
                                            data[i]['prenom'],
                                            `<td><input type="checkbox" class="inscrireAvec" id="${data[i]['id']}"></input></td>`
                                        ]).node().id = i;
                            
                    }

                    tableJoueurs.draw();
                }

                /*
                    Remplit la table des paires isolées
                    data: event
                    @return /
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

                                // Si l'un des membres de la paire est nous même:
                                // Permission 15: Se retirer des paires disponibles
                                if ((data[i]['id'] == aid || data[i+1]['id'] == aid) && havePermission(permissionsJoueur,15))
                                {
                                    paire1 = 2;
                                    paire2 = 0;
                                    checked = 1;
                                    paireIsolee = parseInt(data[i]['pid']);

                                    option = `<button id='${pid}' class='btn btn-danger desinscription paireIsolee'>Se retirer des paires isolées</button>`;
                                } else {
                                    // Permission 16: Inscrire avec une paire disponible
                                    if (havePermission(permissionsJoueur,16))
                                    {
                                        option = `<input type='checkbox' class='inscrireAvec paire' id='${data[i]['pid']}'>`;
                                    } else {
                                        option = "/";
                                    }
                                    
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
                    Remplit les tables 'Ma situation' et 'Inscrits'
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
                            if (havePermission(permissionsJoueur,9)) //Permission 9: Se désinscrire d'un événement
                            {
                                switch (ety)
                                    {
                                                                
                                        case 1: // tournoi
                                            option = `<button id='${pid}' type='button' class='btn btn-danger desinscription tournoi'>Se desincrire</button>`;
                                        break;

                                        case 2: // Partie Libre 
                                            option = `<button id='${pid}' type='button' class='btn btn-danger desinscription partieLibre'>Se desincrire</button>`;
                                        break;

                                        case 3: // Compétition
                                            option = `<button id='${iid}' type='button' class='btn btn-danger desinscription competition'>Se desincrire</button>`;
                                        break;

                                        case 4: // Evenement 
                                            option = `<button id='${pid}' type='button' class='btn btn-danger desinscription evenement'>Se desincrire</button>`;
                                        break;
                                    }
                            
                            } else {
                                option = "/";
                            }
                            if (data.length > 3 && data[i]['remplacant'] != "NULL")
                            {
                                
                                paireRemplacant = data[i]['remplacant'];

                                // Récupère tout les membres remplaçant de la paire
                                remplacant = [];
                                for (let j = i; j < data.length; j++) {
                                    if (data[j]['NumPaire'] == paireRemplacant)
                                    {    
                                        remplacant.push(parseInt(data[j]['id']));    
                                    }
                                }

                                // Permission 14: Se retirer des remplaçants
                                if (remplacant.includes(aid) && havePermission(permissionsJoueur,14))
                                {
                                    option = `<button id='${iid}' class='btn btn-danger desinscription competition remplacant'>Se retirer de la paire</button>`;
                                } 
                            }

                            // Lit jusqu'a changement d'inscription et construit l'affichage
                            // pour la table.
                            i=0;
                            while (i < data.length && data[i]['iid'] != iid)
                            {
                                i++;
                            }
                            iid = data[i]['iid'];
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

                                    while (i < data.length && data[i]['iid'] == iid)
                                    {
                                        noms    += data[i]['nom'] + "</br>";
                                        prenoms += data[i]['prenom'] + "</br>";

                                        pid = data[i]['NumPaire'];
                                        i++;
                                        added++;

                                    }

                            
                                    // Pour les compétitions, si la paire est une paire de remplaçant et vérifie si la paire est pleine 
                                    // Permission 13: S'ajouter aux remplaçants
                                    if (ety == 3 && added > 4 && added != 7 && !inscrit && havePermission(permissionsJoueur,13)) 
                                    {
                                        option = `<button id="${pid}" class='btn btn-danger inscription remplacant'>S'ajouter aux remplaçants</button>`;
                                        added = 0;
                                    } else {

                                        // Si il n'y a pas de paire de remplaçant
                                        if (ety == 3 && added == 4 && !inscrit && havePermission(permissionsJoueur,13))
                                        {
                                            option = `<button id="${iid}" class='btn btn-danger inscription remplacant new'>S'ajouter en remplaçant</button>`;;
                                            added = 0;
                                        } else {
                                            option = "";
                                        }
                                        
                                        
                                    }

                                    if (havePermission(permissionsJoueur,12)) // Permission 12: Désinscrire un joueur
                                    {
                                        option += `<button id='${pid}' type='button' class='btn btn-danger desinscription evenement'>Desincrire</button>`;
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

                /*
                    Remplit la table table avec des checkbox de classe inscrireAvec en option
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
                    Remplit la table table avec des buttons de classe retirerFavori en option.
                    data : [i] [nom] [prenom] [options]
                    table: object table 
                    @return /
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


            // ----------------------------- Utils ----------------

                /*
                    Remet les valeurs par default de l'affichage
                */
                function reload()
                {
                    // Clear already existing elements
                    run_waitMe($('#content'), 'Mise à jour...', 2500);
                    tableJoueurs.clear();
                    tableMesFavoris.clear();
                    tableSOSpartenaire.clear();
                    $('#tableInscrit tbody > tr').remove();
                    $('#tableSituation tbody > tr').remove();
                    $('#tablePaireIsolee tbody > tr').remove();

                    checked = 0;
                    paire1 = 1;
                    if (havePermission(permissionsJoueur,10)) // Permission 10: Inscrire d'autres personnes que soi 
                    { 
                        paire1--;
                    } 
                    
                    paire2 = 0;
                    remplacant = 0;

                    buttonInscrire(false);
                    
                    initWithEvent();
                }

                /*
                    Affiche un logo de chargement
                    object: object jquery de l'élélement ciblé
                    txt   : texte à afficher
                    ms    : durée en ms
                    @return /
                */
                function run_waitMe(object, txt, ms)
                {
                    object.waitMe({
                            effect: 'win8',
                            text: txt,
                            bg: 'rgba(255,255,255,0.7)',
                            color: '#000',
                            maxSize: 100,
                            waitTime: ms,
                            textPos: 'vertical',
                            fontSize: '18px',
                    });
                }

            

            
            // ----------------------------- Main ----------------
            
            function init()
            {
                /*
                    Gestion des permissions
                */
                user = getUser(aid);

                getPermissionStatut(user['idStatut']).forEach(permission => {
                    permissionsJoueur.push(parseInt(permission['did']));
                });  

                /*
                    Init
                */
                    anom = user['nom'];

                    SOSenabled = false;
                    paireIsolee = 0;
                    descriptionAffiche = false;

                    paire = 0;
                    paire1 = 1;
                    paire2 = 0;
                    remplacant = 0;
                    checked = 0;

                /*
                    Permissions 
                */
                if (havePermission(permissionsJoueur,6))     // Permission 6: Droit accès de base
                {
                    $('#gestionBase').show();
                }
                if (havePermission(permissionsJoueur,10)) // Permission 10: Inscrire d'autres personnes que soi 
                {
                    paire1 = 0;
                } 
                

                initWithEvent();
                
                window.setInterval(function(){
                    reload();
                }, 30000);

            }
 
            /*
                Met en place l'affichage des tables.
                à clean
                @return /
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
                        // On s'ajoute en tant qu'inscrit SOS
                        $('#tableSituation > tbody').append(
                            '<tr>' +
                                `<td>1</td>` +
                                `<td>${user['nom']}</td>` +
                                `<td>${user['prenom']}</td>` +
                                `<td>/ Inscrit SOS</td>` +
                            '</tr>'
                        );

                        tableSOSpartenaire.row.add([
                                1,
                                user['nom'],
                                user['prenom'],
                                `<td>Inscrit</td>`
                        ]).node().id = 0;

                        $('.pourInscrire').show();
                        $('.JO').hide();             // Cache table joueurs
                        $('.PA').hide();             // Cache table favoris
                        $('.paireIsolee').hide();    // Cache table des paires isolées

                        $('#textSOS').show();
                        $('#textSOS').text('Cliquer pour vous désinscrire ');

                        $('#buttonSOS').text('Quitter SOS');
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

                    // Si n'est pas inscrit et SOS desactivé ou si c'est un évenement spécial
                    if (!inscrit && !SOSenabled || ety == 5 || havePermission(permissionsJoueur,11)) // Permission 11: Inscrire plusieurs fois
                    {
                            $( ".pourInscrire" ).show();

                            switch (ety)
                            {

                                case 1: // tournoi
                                    $('#textSOS').show();
                                    if (paire == 1)
                                    {
                                        $('.paireIsolee').hide(); 
                                    }


                                break;

                                case 2:                 // Partie Libre 
                                    $('.JO').hide();    // Cache table joueurs
                                    $('.PA').hide();    // Cache table favoris
                                    $('.paireIsolee').hide();
                                    $('#textSOSButtonSOS').hide(); // Cache SOS
                                    $('#buttonInscrire').removeAttr("disabled");
                                    $('#buttonInscrire').addClass("btn-primary");  
                                    $('#buttonInscrire').prop('disabled', false);
                                break;

                                case 3: // Compétition
                                    $('.paireIsolee').hide(); // Cache table paire isolée
                                break;

                                case 4: // Evenement 
                                    $('.JO').hide();    // Cache table joueurs
                                    $('.PA').hide();    // Cache table favoris
                                    $('#textSOSButtonSOS').hide(); // Cache SOS
                                    $('.paireIsolee').hide(); // Cache table paire isolée
                                    $('#buttonInscrire').removeAttr("disabled");
                                    $('#buttonInscrire').addClass("btn-primary");  
                                    $('#buttonInscrire').prop('disabled', false);
                                break;
                                         
                                case 5: // Spéciaux
                                
                                    $('#inscriptArea').empty();
                                    $('#bottomAction').hide();
                                    populateSpecialEvent(event);

                                break;
                            }

                            
                            var ignore = [];

                            

                            if (!havePermission(permissionsJoueur,10)) // Permission 10: Inscrire d'autres personnes que soi 
                            {
                                // On se rajoute à l'array, car on ne veut pas être afficher dans la liste de joueurs
                                ignore.push([aid]);
                                ignore = ignore.concat(alreadyRegistered);
                            }

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
            
            init();
       
        });
            


        </script>
            

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
                overflow-y: hidden;
                overflow-x: hidden;
            }
            .eqR {
                float: right;
                width: 50%;
                padding: 1em;
                overflow-y: hidden;
                overflow-x: hidden;
            }

            .btn-mid20 {
                width: 20%;
            }

            .btn-circle.btn-xl {
                width: 150px;
                height: 150px;
                padding: 10px 16px;
                border-radius: 75px;
                font-size: 20px;
                line-height: 1.33;
            }


            #inscriptArea{
                overflow-x: hidden;
            }

            .pourInscrire{
                display: none;
            }

            .my-legend{
                opacity: 0.5;
            }
            .my-legend .legend-title {
                text-align: left;
                margin-bottom: 8px;
                font-weight: bold;
                font-size: 90%;
            }
            .my-legend .legend-scale ul {
                margin: 0;
                padding: 0;
                float: left;
                list-style: none;
            }
            .my-legend .legend-scale ul li {
                display: block;
                float: left;
                width: 50px;
                margin-bottom: 6px;
                text-align: center;
                font-size: 80%;
                list-style: none;
            }
            .my-legend ul.legend-labels li span {
                display: block;
                float: left;
                height: 15px;
                width: 50px;
            }


        </style>


    </head>


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
                    
                    <li class="active">
                        <a href="#"><span class="fa fa-users mr-3active"></span> Inscription</a>
                    </li>

                    <li>
                        <a href="profil.php"><span class="fa fa-gift mr-3"></span> Profil / Partenaires </a>
                    </li>
                    
                    <li>
                        <a style="display: none" id="gestionBase" href="admin.php"><span class="fa fa-table mr-3"></span>Gestion administrateur</a>
                    </li>

                    <li>
                        <a href="https://scjc-bridge.fr"><span class="fa fa-link mr-3"></span>Retour au club</a>
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
                            <table class="table display" cellspacing="0" width="100%" id="tableSituation">
                            
                                <thead>
                                    <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Nom</th>
                                    <th scope="col">Prénom</th>
                                    <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   
                                </tbody>
                            </table>

                        <div class="pourInscrire PA">
                            <h3>Mes partenaires favoris: </h3>
                                <table class="table display" cellspacing="0" width="100%" id="tableMesFavoris">
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

                            <div calss="display" id="textSOSButtonSOS">
                                <h3 style="display: none;" id="textSOS" >Je suis seul, je m'inscris à SOS Partenaire: </h3> 
                                <button id="buttonSOS" class="btn btn-primary">Rejoindre SOS</button> 
                            </div>
                                                       
                            <table style="display: none;" class="table display" cellspacing="0" width="100%" id="tableSOS">
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
                            <h3>Paires disponibles: </h3>
                            <table class="table display" cellspacing="0" width="100%" id="tablePaireIsolee">
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


                    </div>

                    <div class="eqR">


                        <h3>Tous les inscrits: </h3>
                            <table class="table display" cellspacing="0" width="100%" id="tableInscrit">
                                <thead>
                                    <tr>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Nom</th>
                                        <th scope="col">Prénom</th>
                                        <th scope="col">Actions</th>
                                        </tr>
                                    </tr>
                                </thead>
                                <tbody> 


                                
                                </tbody>
                            </table>

                        <div class="pourInscrire JO">
                            
                            <h3>Liste des joueurs: </h3>
                                <table class="table display" cellspacing="0" width="100%" id="tableJoueurs">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nom</th>
                                            <th>Prénom</th>
                                            <th>Inscrire avec</th>
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

 


                <div id="bottomAction" class="row" style="position: fixed; bottom: 0; width: auto;">
  
                        <div style="padding: 1em;">      
                            <button id="buttonInscrire" type="button" class="btn btn-secondary btn-circle btn-xl btn-mid20" disabled>S'inscrire</button>
                        </div>

                        <div style="margin-top: 30%;" class='my-legend'>
                            <div class='legend-title'>Légende</div>
                                <div class='legend-scale'>
                                    <ul class='legend-labels'>
                                        <li><span style='background:#FF0000'></span>Paire 1</li>
                                        <li><span style='background:#0000FF;'></span>Paire 2</li>
                                        <li><span style='background:#00FF00;'></span>Remplaçants</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                </div>
  

            </div>
                

       
        </div>



        
    </body>
</html>