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

            <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js" integrity="sha256-gJWdmuCRBovJMD9D/TVdo4TIK8u5Sti11764sZT1DhI=" crossorigin="anonymous"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.4/jspdf.plugin.autotable.min.js" integrity="sha256-4CNCFqz7EvqtM61GxKY25T/MFIWTh2Iqelbm+HrhYq8=" crossorigin="anonymous"></script>

            <link rel="icon" type="image/png" href="./favicon.ico" />
        <!-- next --> 

        <script type="text/javascript">


            $(document).ready(function(){


                $('#sidebarCollapse').on('click', function () {
                    $('#sidebar').toggleClass('active');
                });
                fullHeight();

                var aid = <?php echo intval($_COOKIE['logged']) ?>;
                var user = getUser(aid);
                var tmpPermissionsJoueur = gePermissionStatut(user['idStatut']);
                var permissionsJoueur = []; 
                tmpPermissionsJoueur.forEach(permission => {
                    permissionsJoueur.push(parseInt(permission['did']));
                });  

                if (havePermission(6))    // Droit accès de base
                {
                    $('#gestionBase').show();
                }
       
                /*
                    Retourne si l'utilisateur à la permission
                */
                function havePermission(droit)
                {
                    return permissionsJoueur.includes(droit)
                }


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

                        bcColor = permissionsEvents[event['type']][0]['color'];
                        classNames = [];

                        i = 0;
                        while (i < permissionsEvents[event['type']].length)
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
                
                function updateEventDate(eid, startDate, endDate)
                {
                    $.post('assets/sql/interface.php',
                        {
                            function: 'updateEventDate',
                            eid: eid,
                            startDate: startDate,
                            endDate: endDate,
                        }, function(data) {

                            if (data)
                            {
                                alert('Une erreur est survenue: \n' + data + "\n Il est fortement recommandé d'actualiser la page.");
                            }

                        });
                }
                
                var current_event = [];
                var calendarEl = document.getElementById('calendar');
                
                var calendar = new FullCalendar.Calendar(calendarEl, {
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
                    minTime: '6:00',
                    maxTime: '24:00',
                    defaultDate: new Date().toISOString().slice(0,10),
                    firstDay: 1,
                    defaultView: 'timeGridWeek',
                    navLinks: true, 
                    eventLimit: true, 
                    editable: havePermission(8), // Permission 8: Ajuster les événements dans l'agenda (déplacements/étirements)
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
                        if (i >= event.classNames.length && !passed)
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
                                showModal();
                            });

                            $('.fc-today-button').after('<select id="trieEvenement" class="selectpicker" multiple data-live-search="false"></select>');
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

                $(document).on('change', '#trieEvenement', function (e) {

                    $(this).find("option:not(:selected)").each(function(key,value){
                        $(".fc-event." + value.className).hide();
                        console.log( value.className);
                    });

                    $(this).find("option:selected").each(function(key,value){
                        $(".fc-event." + value.className).show();
                    });

                        
                });


                function showModal(e) {
                    
                    if (havePermission(7)) // Droit de gestion d'évenements
                    {
                        /* 
                            Get the additionnal event info
                        */
                        let additionnal = JSON.parse(getEvent(current_event['id']));
                        let lieux = JSON.parse(getLieux());
                        let registered = JSON.parse(getPlayersRegisteredForEvent(current_event['id']));

                        /*
                            Empty form
                        */
                        $('#eventParticipants tbody').empty();
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
                        
                        $('#' + additionnal['lieu']).prop('selected', true);
                        $('#eventLieuEdit').selectpicker("refresh");
                        

                        $('#eventPaireEdit').val(additionnal['paires']).change();

                        

                        $('#eventNameEdit').val(additionnal['titre']);
                        $('#eventPrixEdit').val(additionnal['prix']);

                        $('#dteDebutEdit').val(moment(current_event['start']).format("YYYY-MM-DDTHH:mm:ss"));
                        $('#dteFinEdit').val(moment(current_event['end']).format("YYYY-MM-DDTHH:mm:ss"));
                    
                        $("#eventEditModal").modal('show');
                    }
                }


                $('#deleteEventButton').on('click', function () {
                
                    if (confirm("Êtes vous sûr de vouloir supprimer l'évenement? Cette action est irréversible!"))
                    {

                        let ety = 3;

                        if (current_event.classNames.includes('tournoi'))
                        {
                            ety = 1;
                        } else {
                            if (current_event.classNames.includes('partieLibre'))
                            {
                                ety = 2;
                            }
                        }

                        deleteEvent(current_event['id'], ety);
                        document.location.reload();
                    }
                });
                
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
                        };
                        
                        console.log(event);
                        updateEvent(event);
                        document.location.reload();
                    }


                }); 
                
                $('#exportJoueursButton').on('click', function () {
                
                    const doc = new jsPDF()
                    
                    doc.autoTable({html: '#eventParticipants'})
                                        
                    doc.save($('#eventNameEdit').val() + "-" + moment(new Date()).format('DD/MM/YYYY') + ".pdf");
 
                });

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

</style>

    <body>

    
        <!-- Modal for event edit -->
        <div class="modal fade" id="eventEditModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">

            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">
                        <input id="eventNameEdit" class="form-control border-primary" type="text" placeholder="Nom de l'évenement">
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
                            <input id="eventPrixEdit" type="text" class="form-control date" placeholder="Prix" aria-label="Prix" aria-describedby="basic-addon1">                            
                        </div>

                        <div class="input-group mb-3">
        
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="eventLieuEdit">Lieu</label>
                            </div>
                            <select id="eventLieuEdit" style="margin-left:1;" class="select"></select>                            

                            <div class="input-group-prepend">
                                <label class="input-group-text" for="eventPaireEdit">Paire</label>
                            </div>
                            
                            <select id="eventPaireEdit" style="margin-left:1;" class="select w-25">
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
                        <button id="deleteEventButton" type="button" class="btn btn-danger">Supprimer</button>
                        <button id="saveEditEventButton" type="button" class="btn btn-primary">Sauvegarder les modifications</button>
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
                        <a href="login.php?logoff"><span class="fa fa-sign-out mr-3"></span> Se déconnecter</a>
                    </li>
      
                </ul>

            </nav>

            <div id="content" class="p-4 p-md-5 pt-5">
                
                <div id='calendar'></div>

            </div>
        </div>

    </body>
</html>