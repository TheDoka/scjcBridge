<?php 

include('assets/php/utils.php');

if (!logged())
{
    echo "<script>alert(\"Vous n'êtes pas connecté, vous allez être redirigé vers la page de connexion.\"); window.location = 'login.php'; </script>";
}

?>

<style>

</style>
    


<html>

    <head>

        <!-- Jquery -->    
            <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
            <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
            
        <!-- Popper --> 
            <script src="assets/js/popper.js"></script>
        
        <!-- BOOTSTRAP -->
            <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet"  crossorigin="anonymous">
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
            <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>


         <!-- FontAwesome -->    
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

        <!-- NavBar -->    
            <link href="assets/css/navbar.css" rel="stylesheet" crossorigin="anonymous">

        <!-- FullCalendar --> 
            <script src='assets/js/fullcalendar/core/main.js'></script>
            <script src='assets/js/fullcalendar/core/locales/fr.js'></script>
            <link href='assets/css/fullcalendar/core/main.css' rel='stylesheet' />

            <script src='assets/js/fullcalendar/interaction/main.js'></script>

            <script src='assets/js/fullcalendar/daygrid/main.js'></script>
            <link href='assets/css/fullcalendar/daygrid/main.css' rel='stylesheet' />

            <script src='assets/js/fullcalendar/list/main.js'></script>
            <link href='assets/css/fullcalendar/list/main.css' rel='stylesheet' />

            <script src='assets/js/fullcalendar/timegrid/main.js'></script>
            <link href='assets/css/fullcalendar/timegrid/main.css' rel='stylesheet' />

            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js" integrity="sha256-5oApc/wMda1ntIEK4qoWJ4YItnV4fBHMwywunj8gPqc=" crossorigin="anonymous"></script>

         <!-- Utils --> 
            <script src='assets/js/utils.js'></script>
            <link rel="icon" type="image/png" href="./favicon.ico" />

        <!-- JsPDF -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js" integrity="sha256-gJWdmuCRBovJMD9D/TVdo4TIK8u5Sti11764sZT1DhI=" crossorigin="anonymous"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.4/jspdf.plugin.autotable.min.js" integrity="sha256-4CNCFqz7EvqtM61GxKY25T/MFIWTh2Iqelbm+HrhYq8=" crossorigin="anonymous"></script>
        
        <!-- next --> 

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
                
                // Gestion du joueur
                    var aid = <?php echo intval($_COOKIE['logged']) ?>;
                    var user;
                    var permissionsJoueur = []; 
                // Gestion de l'agenda
                    var calendar;
                    var current_event = [];

                
                
            
                // Main
                init();

                function init()
                {
                    /*
                        Gestion des permissions
                    */
                    user = getUser(aid);
   
                    getPermissionStatut(user['idStatut']).forEach(permission => {
                        permissionsJoueur.push(parseInt(permission['did']));
                    });  

                    if (havePermission(permissionsJoueur,6))    // Droit accès de base
                    {
                        $('#gestionBase').show();
                    }
       
                    /*
                        Génère l'agenda
                    */
                    var calendarEl = document.getElementById('calendar');
                    calendar = new FullCalendar.Calendar(calendarEl, {
                        locale: 'FR',
                        height: $(window).height()*0.85,
                        plugins: ['interaction', 'dayGrid', 'list', 'timeGrid'],
                        header: {
                            left: 'prevYear,prev,next,nextYear today',
                            center: 'title',
                            right: 'dayGridMonth,dayGridWeek,dayGridDay, listMonth, timeGridWeek'
                        },
                        businessHours: {
                            startTime: '6:00',
                            endTime: '23:00',
                        },
                        minTime: '10:00',
                        maxTime: '22:00',
                        defaultDate: new Date().toISOString().slice(0,10),
                        firstDay: 1,
                        defaultView: 'timeGridWeek',
                        navLinks: true, 
                        eventLimit: true, 
                        editable: havePermission(permissionsJoueur,8), // Permission 8: Ajuster les événements dans l'agenda (déplacements/étirements)
                        allDaySlot: false,

                        eventResize:function(event)
                        {
                            let newStartDate = moment(event.event.start).format("YYYY-MM-DD HH:mm:ss");
                            let newEndDate = moment(event.event.end).format("YYYY-MM-DD HH:mm:ss");
                            let eid = event.event.id;

                            if (confirm("L'évenement aura pour nouvelle date: \nDébut: " + newStartDate + "\nFin     : " + newEndDate))
                            {
                                updateEventDate(eid, newStartDate, newEndDate);
                            } else {
                                event.revert();
                            }
                            
                        },

                        eventDrop:function(event)
                        {


                            let newStartDate = moment(event.event.start).format("YYYY-MM-DD HH:mm:ss");
                            let newEndDate = moment(event.event.end).format("YYYY-MM-DD HH:mm:ss");
                            let eid = event.event.id;

                            if (confirm("L'évenement aura pour nouvelle date: \nDébut: " + newStartDate + "\nFin     : " + newEndDate))
                            {
                                updateEventDate(eid, newStartDate, newEndDate);
                            } else {
                                event.revert();
                            }

                        },
                        eventClick:function(event)
                        {

                            event = event.event;
                            var todayDate = moment(new Date($.now())).format("YYYY-MM-DD HH:mm:ss");
                            var passed = moment(event['start']).format("YYYY-MM-DD HH:mm:ss") < todayDate;

                            i = 0;
                            // inscrit
                            if (event.classNames.includes('inscrit')) { i++; }

                            // ++ 
                            for (let j = 0; j < permissionsJoueur.length; j++) {
                            
                                if (event.classNames.includes(permissionsJoueur[j]))
                                    i++;
                            }

                            // Le type d'évenement fait partie des classes
                            i++;
                            console.log(i);
                            console.log(event.classNames.length);
                            // Si l'utilisateur à les droits et que l'évenement n'est pas passé ou que l'évenemnt n'est pas une finale
                            if (i >= event.classNames.length && !passed || havePermission(permissionsJoueur,18)) // Permission 18: Accéder à n'importe quel événement 
                            {

                                if (confirm("Description de l'évenement?"))
                                {
                                        window.location = 'inscription.php?eid=' + event.id;
                                }

                            } else {
                                alert("L'inscription n'est pas/plus disponible pour cet évenement.");
                            }


                        },
                        datesRender : function(){

                                /*
                                    Clique droit sur les évenements, l'action ne se passe pas sur tout le document, alors il faut recréer le trigger à chaque nouvelle élément.
                                */
                                $('.fc-event').bind('contextmenu', function(e){
                                    e.preventDefault();
                                    editEventModal();
                                });

                                $('.fc-today-button').after('<select id="trieEvenement" class="selectpicker" multiple data-live-search="false"></select>');
                                if (havePermission(permissionsJoueur,17)) // Permission 17: Créer des événements
                                {
                                    $('.fc-today-button').after('<button id="newEvent" class="btn btn-primary">Ajouter un évenement</button>');

                                }
                                let data = getEty(-1);
                                let last = "";

                                data.forEach(typeEvent => {
                                
                                    if (typeEvent['libelle'] != last)
                                    {
                                        $('#trieEvenement').append(`<option class="t${typeEvent[0]}" selected>${typeEvent['libelle']}</option>`);
                                        last = typeEvent['libelle'];
                                    } 

                                });
                                $('select').selectpicker();

                        },
                        eventMouseEnter: function( event, jsEvent, view )
                        {
                            current_event = event.event;
                        }
                    });

                    callendarImport();           
                    calendar.render();
                    $('#helpModal').modal().show();

                }


                
                // ------------- Gestion de l'agenda ----------------

                    /*
                        Importe les evenements de la base de données dans l'agenda
                    */
                    function callendarImport()
                    {

                        /*
                            On recupères tout les événements et leurs informations
                        */
                        events = [];
                        $.ajaxSetup({async: false});  
                        $.post('assets/sql/interface.php',
                        {
                            function: 'getEvents',
                        }, function(data) {
                            events = JSON.parse(data);
                        });
                        
                        /*
                            On regarde les évenements où est inscrits notre joueur:
                        */
                        registeredForEvents = [];
                        $.post('assets/sql/interface.php',
                        {
                            function: 'getAllPlayersRegistrations',
                            aid: aid,
                        }, function(data) {

                            registeredForEvents = JSON.parse(data);
                        });

                        let permissionsEvents = getArrayOfPermissions();              
    
                        events.forEach(event => {

                            bcColor = event['color'];
                            classNames = [];

                            i = 0;
                            // vérifie si il y a des permissions, et ajoute les permissions à l'évenement
                            while (permissionsEvents[event['type']] && i < permissionsEvents[event['type']].length)
                            {        
                                classNames.push(parseInt(permissionsEvents[event['type']][i]['did']));            
                                i++;  
                            }

                            classNames.push('t' + event['type'])

                            let title = "";

                            if (isInArray(registeredForEvents, event[0]))
                            {
                                title += "★ ";
                                bcColor = '#00a6ff';
                                classNames.push("inscrit");
                            }
                            title += event['titre'];

                            calendar.addEvent({
                                id: event[0],
                                title: title,
                                start: event['dteDebut'],
                                end: event['dteFin'],
                                classNames: classNames,
                                backgroundColor: bcColor,
                            });
                            
                        });


                    }  
                    
                    /*
                        Checkbox de trie des evenements de l'agenda 
                    */
                    $(document).on('change', '#trieEvenement', function (e) {

                        $(this).find("option:not(:selected)").each(function(key,value){
                            $(".fc-event." + value.className).hide();
                            console.log( value.className);
                        });

                        $(this).find("option:selected").each(function(key,value){
                            $(".fc-event." + value.className).show();
                        });
            
                    });

                    // ------------- Gestion création d'un évenement --------

                        /*
                            Bouton ajout évenement
                        */
                        $(document).on('click', '#newEvent', function() {

                            /* 
                                Get the additionnal event info
                            */
                            let lieux = JSON.parse(getLieux());
                            let niveaux = getAllNiveaux(); 
                            let etys = getEty(-1);

                            /* 
                                Empty form
                            */
                            $('#newEventLieuEdit').empty();
                            $('#newEventTypeEdit').empty();
                            $('#newEventNR').empty();

                            /*
                                Populate Lieux
                            */
                            lieux.forEach(lieu => {
                                $('#newEventLieuEdit').append($('<option>', { 
                                    id: lieu['id'],
                                    text : lieu['commune'] + ' ' + lieu['adresse'], 
                                }));
                            });

                            /*
                                Populate Niveaux
                            */
                            niveaux.forEach(niveau => {
                                $('#newEventNR').append($('<option>', { 
                                    id: niveau['idNiveau'],
                                    text : niveau['numeroSerie'], 
                                    }));
                            });
                            
                            /*
                                Populate Ety
                            */
                            etys.forEach(ety => {
                                $('#newEventTypeEdit').append($('<option>', { 
                                    id: ety['id'],
                                    text : ety['libelle'], 
                                    }));
                            });
                            

                            $('#newEventLieuEdit').selectpicker("refresh");
                            $('#newEventTypeEdit').selectpicker("refresh");
                            $('#newEventNR').selectpicker("refresh");
                            
                            $('#newDteDebutEdit').val(moment(new Date($.now())).format("YYYY-MM-DDTHH:mm:ss"));
                        
                            $("#newEventModal").modal('show');
                        }); 

                        /*
                            Selection du type d'évenement 
                        */
                        $('#newEventTypeEdit').on('change', function(e){

                            $('#newTournoi').hide(); 
                            $('#newPartieLibre').hide(); 
                            $('#newCompetition').hide(); 
                            $('#newSpecial').hide();  
                            let niveaux = [];

                            switch (parseInt($(this).children(":selected").attr("id")))
                            {
                                case 1: $('#newTournoi').show();      
                                    $('#newEventNR').empty();
                                    niveaux = getAllNiveaux(); 

                                    niveaux.forEach(niveau => {
                                        $('#newEventNR').append($('<option>', { 
                                            id: niveau['idNiveau'],
                                            text : niveau['numeroSerie'], 
                                            }));
                                        });
                                    $('#newEventNR').selectpicker("refresh");

                                    $('#newTournoi').show();  
                                
                                break;
                                case 2:    
                                
                                    $('#newEventNRPT').empty();
                                    niveaux = getAllNiveaux();

                                    niveaux.forEach(niveau => {
                                        $('#newEventNRPT').append($('<option>', { 
                                            id: niveau['idNiveau'],
                                            text : niveau['numeroSerie'], 
                                            }));
                                        });
                                    $('#newEventNRPT').selectpicker("refresh");

                                    $('#newPartieLibre').show();  
                                    
                                break;

                                case 3: $('#newCompetition').show();     
                                
                                    $('#newEventStade').empty();
                                    $('#newEventCatComp').empty();
                                    $('#newEventDivison').empty();
                                    $('#newEventPublic').empty();

                                    let stades = getAllStade();
                                    let categories = getAllCategorie();
                                    let divisions = getAllDivison();
                                    let publics = getAllPublic();

                                    stades.forEach(stade => {
                                        $('#newEventStade').append($('<option>', { 
                                            id: stade['id'],
                                            text : stade['libelle'], 
                                            }));
                                        });
                                    $('#newEventStade').selectpicker("refresh");
                                    
                                    categories.forEach(categorie => {
                                        $('#newEventCatComp').append($('<option>', { 
                                            id: categorie['id'],
                                            text : categorie['libelle'], 
                                            }));
                                        });
                                    $('#newEventCatComp').selectpicker("refresh");
                                    
                                    divisions.forEach(division => {
                                        $('#newEventDivison').append($('<option>', { 
                                            id: division['id'],
                                            text : division['libelle'], 
                                            }));
                                        });
                                    $('#newEventDivison').selectpicker("refresh"); 
                                    
                                    publics.forEach(public => {
                                        $('#newEventPublic').append($('<option>', { 
                                            id: public['id'],
                                            text : public['libelle'], 
                                            }));
                                        });
                                    $('#newEventPublic').selectpicker("refresh");

                                break;

                                case 5: 
                                    let events = getEvents();
                                    $('#tableEventRaccorde > tbody').empty(); 
                                    events.forEach(event => {

                                        $('#tableEventRaccorde').append(
                                            `<tr>
                                                <td><a href="inscription.php?eid=${event[0]}">${event['titre']}</a></td>
                                                <td>${event['dteDebut']}</td>
                                                <td><input id="${event[0]}" type="checkbox" class="raccorderAvec"></input></td>
                                            </tr>`
                                        );

                                    });

                                    $('#newSpecial').show();      
                                break;
                        
                                default:

                                    $('#newEvenement').show();       
                                
                                break;
                        
                            }
                        });

                        /*
                            Ajoute l'évenement à la base
                        */
                        $('#confirmNewEvent').on('click', function(){

                            // Get all form property:
                            let ety = $('#newEventTypeEdit').children(":selected").attr("id");
                            let res = "";

                            switch (parseInt(ety))
                            {
                                case 1: // Tournoi
                                    
                                                    
                                    var tournoi = [{
                                                            
                                        titre: $('#newEventNameEdit').val(),
                                        dteDebut: $('#newDteDebutEdit').val(),
                                        dteFin: $('#newDteFinEdit').val(),
                                        prix: $('#newEventPrixEdit').val(),
                                        lieu: $('#newEventLieuEdit').children(":selected").attr("id"),
                                        paire: $('#newEventPaireEdit').val(),
                                        
                                        niveauRequis: $('#newEventNR').children(":selected").attr("id"),
                                        repas: $('#newEventRepas').val()=='Non'?0:1,
                                        apero: $('#newEventApero').val()=='Non'?0:1,
                                        imp: $('#newEventIMP').val()=='Non'?0:1,
                                        dc: $('#newEventDC').val()=='Non'?0:1,

                                        ety: 1,

                                    }];
                                    
                                    console.log(tournoi);
                                    if (confirm("Confirmer l'ajout?"))
                                    {
                                        res = newEvenement(tournoi);
                                    }

                                break;

                                case 2: // Partie libre   
                                
                                    var partieLibre = [{
                                        
                                        titre: $('#newEventNameEdit').val(),
                                        dteDebut: $('#newDteDebutEdit').val(),
                                        dteFin: $('#newDteFinEdit').val(),
                                        prix: $('#newEventPrixEdit').val(),
                                        lieu: $('#newEventLieuEdit').children(":selected").attr("id"),
                                        paire: $('#newEventPaireEdit').val(),

                                        niveauRequis: $('#newEventNR').children(":selected").attr("id"),

                                        ety: 2,

                                    }];

                                    
                                    if (confirm("Confirmer l'ajout?"))
                                    {
                                        newEvenement(partieLibre);
                                    }
                                    
                                break;

                                case 3: // Compétition

                                    var competition = [{
                                        
                                        titre: $('#newEventNameEdit').val(),
                                        dteDebut: $('#newDteDebutEdit').val(),
                                        dteFin: $('#newDteFinEdit').val(),
                                        prix: $('#newEventPrixEdit').val(),
                                        lieu: $('#newEventLieuEdit').children(":selected").attr("id"),
                                        paire: $('#newEventPaireEdit').val(),

                                        catComp: $('#newEventCatComp').children(":selected").attr("id"),
                                        division: $('#newEventDivison').children(":selected").attr("id"),
                                        stade: $('#newEventStade').children(":selected").attr("id"),
                                        public: $('#newEventPublic').children(":selected").attr("id"),

                                        ety: 3,

                                    }];

        
                                    if (confirm("Confirmer l'ajout?"))
                                    {
                                        newEvenement(competition);
                                    }

                                break;

                                case 5: // Spécial
                                
                                    let raccordes = [];
                                    $('.raccorderAvec:checkbox:checked').each(function () {
                                        raccordes.push($(this).attr('id'));
                                    }); 

                                    var special = [{
                                                            
                                        titre: $('#newEventNameEdit').val(),
                                        dteDebut: $('#newDteDebutEdit').val(),
                                        dteFin: $('#newDteFinEdit').val(),
                                        prix: $('#newEventPrixEdit').val(),
                                        lieu: $('#newEventLieuEdit').children(":selected").attr("id"),
                                        paire: $('#newEventPaireEdit').val(),
                                        
                                        raccordes: raccordes,
                                        ety: 5,
                                    }];

                                    
                                    if (confirm("Confirmer l'ajout?"))
                                    {
                                        res = newEvenement(special);
                                    }
                                    

                                break;

                                default: // Evenement
                                    alert($('#newEventTypeEdit').children(":selected").attr("id"));
                                    var evenement = [{
                                                    
                                        titre: $('#newEventNameEdit').val(),
                                        dteDebut: $('#newDteDebutEdit').val(),
                                        dteFin: $('#newDteFinEdit').val(),
                                        prix: $('#newEventPrixEdit').val(),
                                        lieu: $('#newEventLieuEdit').children(":selected").attr("id"),
                                        paire: $('#newEventPaireEdit').val(),

                                        ety: $('#newEventTypeEdit').children(":selected").attr("id"),
                                    }];

                                    if (confirm("Confirmer l'ajout?"))
                                    {
                                        res = newEvenement(evenement);
                                    }

                                break;

                            }

                            check(res);

                        });


                    // ------------- Edition d'un évenement -----------------

                        /*
                            Trigger lorse que clique droit sur un évenement
                        */
                        function editEventModal(e) {

                            if (havePermission(permissionsJoueur,7)) // Droit de gestion d'évenements
                            {
                                
                                /* 
                                    Get the additionnal event info
                                */
                                let additionnal = JSON.parse(getEvent(current_event['id']));
                                let lieux = JSON.parse(getLieux());
                                let registered = JSON.parse(getPlayersRegisteredForEvent(current_event['id']));
                                let eventTypes = getEty(-1);

                                /*
                                    Empty form
                                */
                                $('#eventParticipants tbody').empty();
                                $('#eventTypeEdit').empty();
                                $('#eventLieuEdit').empty();

                                /*
                                    Populate Lieux
                                */
                                lieux.forEach(lieu => {
                                    $('#eventLieuEdit').append($('<option>', { 
                                        id: lieu['id'],
                                        text : lieu['commune'] + ' ' + lieu['adresse'], 
                                    }));
                                });
                                /*
                                    Populate Ety
                                */
                                eventTypes.forEach(ety => {
                                    $('#eventTypeEdit').append($('<option>', { 
                                        id: ety['id'],
                                        text : ety['libelle'], 
                                    }));
                                });
                                /*
                                    Populate table Participants
                                */

                                let i = 0;
                                let noms = "";
                                let prenoms = "";

                                if (registered.length > 0)
                                {
                                    let iid = registered[0]['iid'];
                                    while (i < registered.length)
                                    {

                                        if (i < registered.length && registered[i]['iid'] == iid)
                                        {

                                            while (i < registered.length && registered[i]['iid'] == iid)
                                            {
                                                noms    += registered[i]['nom'] + "</br>";
                                                prenoms += registered[i]['prenom'] + "</br>";

                                                pid = registered[i]['NumPaire'];
                                                i++;

                                            }

                                            $('#eventParticipants > tbody').append(
                                                `<tr>` +
                                                    `<td>${iid}</td>` +
                                                    `<td>${noms}</td>` +
                                                    `<td>${prenoms}</td>`+
                                            '</tr>'
                                            );
                                                


                                            if (i < registered.length && registered[i]['NumPaire'] != pid)
                                            {
                                                noms    = "";
                                                prenoms = "";
                                                pid = registered[i]['NumPaire'];
                                                iid = registered[i]['iid'];
                                            }


                                        }

                                    }
                                }
                                console.log();
                                $('#' + additionnal['lieu']).prop('selected', true);
                                $('#' + additionnal['type'][0]).prop('selected', true);
                                $('#eventLieuEdit').selectpicker("refresh");
                                $('#eventTypeEdit').selectpicker("refresh");
                                

                                $('#eventPaireEdit').val(additionnal['paires']).change();

                                
                                $('#eventNameEdit').val(additionnal['titre']);
                                $('#eventPrixEdit').val(additionnal['prix']);

                                $('#dteDebutEdit').val(moment(current_event['start']).format("YYYY-MM-DDTHH:mm:ss"));
                                $('#dteFinEdit').val(moment(current_event['end']).format("YYYY-MM-DDTHH:mm:ss"));
                            
                                $("#eventEditModal").modal('show');
                                $(".deleteEventButton").attr('id', additionnal['type']);
                            }
                        }

                        /*
                            Supprime un évenement
                        */
                        $('.deleteEventButton').on('click', function (e) {
                        
                            if (confirm("Êtes vous sûr de vouloir supprimer l'évenement? Cette action est irréversible!"))
                            {
                                let ety = e.target.id;
                                check(deleteEvent(current_event['id'], ety));
                        
                            }
                        });
                        
                        /*
                            Sauvegarde les modification d'un évenement
                        */
                        $('#saveEditEventButton').on('click', function () {
                        
                            if (confirm('Êtes vous sûr de vouloir sauvegarder les modifications? Cette action est irréversible!'))
                            {
                                /*
                                    event[0] titre
                                        [1] dteDebut
                                        [2] dteFin
                                        [3] prix
                                        [4] lieu
                                        [5] paires
                                */

                                event = {
                                    id:      current_event['id'],
                                    titre:   $('#eventNameEdit').val(),
                                    dteDebut:$('#dteDebutEdit').val(),
                                    dteFin:  $('#dteFinEdit').val(),
                                    prix:    $('#eventPrixEdit').val(),
                                    lieu:    $('#eventLieuEdit').find('option:selected').attr('id'),
                                    paires:  $('#eventPaireEdit').val(),
                                    ety:    $('#eventTypeEdit').find('option:selected').attr('id'),
                                };

                                check(updateEvent(event));

                            }


                        }); 
                        
                        /*
                            Exporte la liste des participants de l'évenement
                        */
                        $('#exportJoueursButton').on('click', function () {
                        
                            const doc = new jsPDF()
                            
                            doc.autoTable({html: '#eventParticipants'})
                                                
                            doc.save($('#eventNameEdit').val() + "-" + moment(new Date()).format('DD/MM/YYYY') + ".pdf");
        
                        });
                // ------------- Autres ---------------------------------

                        
                
                $('#helpLink').on('click', function() {
                    $('#helpModal').modal().show();
                });
                /*
                    Vérifie la présence d'érreur
                    data: retour de fonction à vérifier
                            * vide : pas d'erreurs
                            * !vide: erreur
                    @return Message
                */    
                function check(data)
                {
                    if (!data)
                    {
                        alert('Succès');
                        document.location.reload();
                    } else {
                        alert('Une erreur est survenue.\n' + data);
                    }
                }


            });

        </script>
            

    </head>


<style>

    .inscrit{
        background-color: #00a6ff50;
    }

    .fc-event{
        font-size: 1.25em;
    }

    .bootstrap-select .dropdown-toggle .filter-option {
        position: relative;
    }

    .information-agenda {
        position: absolute;
        margin-left: 310px;
    }
</style>

    <body>

        <!-- Modal for help -->
        <div id="helpModal" class="modal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                                <h5 class="modal-title">Aide</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                            <div class="modal-body">
                                <p>Tuto</p>
                                <p id="mod-message">MessageAdmin</p>
                            </div>


                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Contacter un administrateur</button>
                            <button type="button" class="btn btn-primary" data-dismiss="modal">J'ai compris!</button>
                        </div>
                    </div>
                </div>
        </div>

    
        <!-- Modal for event edit -->
        <div class="modal fade" id="eventEditModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">

            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">
                        <input id="eventNameEdit" class="form-control border-primary" type="text" placeholder="Nom de l'évenement">
                        <select id="eventTypeEdit"  class="select w-25"></select>                          
                       
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="input-group mb-3">
                            <input class="form-control" type="datetime-local" id="dteDebutEdit">
                            <input class="form-control" type="datetime-local" value="" id="dteFinEdit">
                        </div>

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <i class="input-group-text fa fa-trophy" aria-hidden="true"></i>
                            </div>
                            <input id="eventPrixEdit" type="text" class="form-control date" placeholder="Prix" aria-label="Prix" >                            
                       
                        </div>

                        <div class="input-group mb-3">
        
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="eventLieuEdit">Lieu</label>
                            </div>
                            <select id="eventLieuEdit"  class="select"></select>                            

                            <div class="input-group-prepend">
                                <label class="input-group-text" for="eventPaireEdit">Paire</label>
                            </div>
                            <select id="eventPaireEdit"  class="select w-25">
                                <option>1</option>
                                <option>2</option>
                                <option>4</option>
                            </select>

                        </div>

                        <div class="mb-2">
                            <h5>Inscrits:</h5>
                            <table id="eventParticipants" class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Nom</th>
                                        <th scope="col">Prénom</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <button id="exportJoueursButton" style="width:100%;" type="button" class="btn btn-info">Exporter les participants</button>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                        <button id="" type="button" class="btn btn-danger deleteEventButton">Supprimer</button>
                        <button id="saveEditEventButton" type="button" class="btn btn-primary">Sauvegarder les modifications</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Modal for new event -->
        <div class="modal fade" id="newEventModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">
                        <input id="newEventNameEdit" class="form-control border-primary" type="text" placeholder="Nom de l'évenement">
                        <select id="newEventTypeEdit"  class="select w-25"></select>                          
                    
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="input-group mb-3">
                            <input class="form-control" type="datetime-local" id="newDteDebutEdit">
                            <input class="form-control" type="datetime-local" id="newDteFinEdit">
                        </div>

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <i class="input-group-text fa fa-trophy" aria-hidden="true"></i>
                            </div>
                            <input id="newEventPrixEdit" type="text" class="form-control date" placeholder="Prix" aria-label="Prix" >                            
                    
                        </div>

                        <div class="input-group mb-3">

                            <div class="input-group-prepend">
                                <label class="input-group-text" for="newEventLieuEdit">Lieu</label>
                            </div>
                            <select id="newEventLieuEdit" class="select"></select>                            

                            <div class="input-group-prepend">
                                <label class="input-group-text" for="newEventPaireEdit">Paire</label>
                            </div>
                            
                            <select id="newEventPaireEdit" class="select w-25">
                                <option>1</option>
                                <option>2</option>
                                <option>4</option>
                            </select>
                        </div>
    
                        <!-- Tournoi -->
                        <div id="newTournoi">
                                
                            <div class="input-group mb-3">

                                <div class="input-group-prepend">
                                    <label class="input-group-text">Repas</label>
                                </div>
                                <select id="newEventRepas" class="select w-25">
                                    <option>Non</option>
                                    <option>Oui</option>
                                </select>

                                <div class="input-group-prepend">
                                    <label class="input-group-text">Apéro</label>
                                </div>
                                <select id="newEventApero"  class="select w-25">
                                    <option>Non</option>
                                    <option>Oui</option>
                                </select>

                            </div>     
                            <div class="input-group mb-3">

                                <div class="input-group-prepend">
                                    <label class="input-group-text">IMP</label>
                                </div>
                                <select id="newEventIMP" class="w-25">
                                    <option>Non</option>
                                    <option>Oui</option>
                                </select>

                                <div class="input-group-prepend">
                                    <label class="input-group-text">Niveau Requis</label>
                                </div>
                                <select id="newEventNR" class="w-25">
                                    <option>Non</option>
                                    <option>Oui</option>
                                </select>
        
                            </div>     
                            
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                        <label class="input-group-text">DC</label>
                                    </div>
                                    <select id="newEventDC" class="w-25">
                                        <option>Non</option>
                                        <option>Oui</option>
                                    </select>
                                </div>
                            </div>

                        
                        <!-- Partie Libre -->
                        <div style="display: none" id="newPartieLibre">

                            <div class="input-group mb-3">

                                <div class="input-group-prepend">
                                    <label class="input-group-text">Niveau Requis</label>
                                </div>
                                <select id="newEventNRPT">
                                    <option>Non</option>
                                    <option>Oui</option>
                                </select>

                            </div>


                        </div>

                        <!-- Compétition -->
                        <div style="display: none" id="newCompetition">

                            <div class="input-group mb-3">

                                <div class="input-group-prepend">
                                    <label class="input-group-text">Catégorie</label>
                                </div>
                                <select id="newEventCatComp" class="w-25"></select>

                                <div class="input-group-prepend">
                                    <label class="input-group-text">Divison</label>
                                </div>
                                <select id="newEventDivison" class="w-25"></select>

                            </div>
                            
                            <div class="input-group mb-3">

                                
                                <div class="input-group-prepend">
                                    <label class="input-group-text">Stade</label>
                                </div>
                                <select id="newEventStade" class="w-25"></select>
                                
                                <div class="input-group-prepend">
                                    <label class="input-group-text">Public</label>
                                </div>
                                <select id="newEventPublic" class="w-25"></select>

                            </div>


                        </div>

                        <!-- Spécial -->
                        <div style="display: none" id="newSpecial">

                            <table id="tableEventRaccorde" style="max-height:300px" class="table table-sm table-responsive">
                                <thead>
                                    <tr>
                                        <td>Nom</td>
                                        <td>Date début</td>
                                        <td>Raccorder</td>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>


                        </div>


                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                            <button id="confirmNewEvent" type="button" class="btn btn-primary">Importer l'évenement</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Content -->
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

                    <li class="active">
                        <a href="#"><span class="fa fa-home mr-3active"></span> Agenda</a>
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
                        <a id="helpLink" href="#"><span class="fa fa-question-circle mr-3"></span>Aide</a>
                    </li>


                    <li>
                        <a href="login.php?logoff"><span class="fa fa-sign-out mr-3"></span> Se déconnecter</a>
                    </li>
      
                </ul>

            </nav>

            <span class="information-agenda">Pour s'inscrire à un évenement cliquer dessus.</span>
            <div id="content" class="p-4 p-md-5 pt-5">
                <div id='calendar'></div>

            </div>
        </div>

    </body>
</html>