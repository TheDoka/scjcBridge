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
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
        
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

        <!-- next --> 

        <script type="text/javascript">

            $(document).ready(function(){


                $('#sidebarCollapse').on('click', function () {
                    $('#sidebar').toggleClass('active');
                });
                fullHeight();


                var todayDate = new Date().toISOString().slice(0,10);
                var aid = <?php echo intval($_COOKIE['logged']) ?>;
                var user = getUser(aid);
                var statut = user['statut'];
                var admin = user['statut'] == "Administrateur"; 
                if (admin)
                {
                    $('#gestionBase').show();
                }
       
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
                    defaultDate: todayDate,
                    firstDay: 1,
                    defaultView: 'timeGridWeek',
                    navLinks: true, // can click day/week names to navigate views
                    eventLimit: true, // allow "more" link when too many events
                    editable: admin,
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

                        if (event['classNames'][1] == 'inscrire' || admin)
                        {

                            if (!event['classNames'].includes('all') && !admin)
                            {

                                if (event['classNames'].includes('membre', 'sympathisant'))
                                {
                                    
                                    if (!['membre', 'sympathisant'].includes(statut))
                                    {
                                        alert('Cet évenement est reservé aux membres ou aux sympathisants.')
                                        return;
                                    } 

                                }

                            }

                          

                            if (confirm("Inspecter l'êvenement?"))
                            {
                                    window.location = 'inscription.php?eid=' + event.id;
                            }

                        } else {
                            alert("L'inscription n'est pas/plus disponible pour cet évenement.");
                        }


                    },
                    datesRender : function(){
                        $('.fc-today-button').after('<select class="selectpicker" multiple data-live-search="false">'+
                                                        '<option id="dC" selected>Compétitions</option>'+
                                                        '<option id="dT" selected>Tournois</option>'+
                                                        '<option id="dPL" selected>Parties Libres</option>'+
                                                        '<option id="dES" selected>Evénements spéciaux</option>'+
                                                        '<option id="dIN" selected>Afficher événements inscrits</option>'+
                                                    '</select>');
                        $('select').selectpicker();
                    }
                });

                callendarImport();           
                calendar.render();

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
                        console.log(registeredForEvents);
                    });

                    events.forEach(event => {
                        /*
                        #type 
                            0 : évenement
                            1 : tournoi
                            2 : partie libre
                            3 : compétition
                        */
                       
                        bcColor = "";
                        classNames = [];

                        switch (parseInt(event['type']))
                        {
                            case 0:
                                bcColor = "#8e6eb7";
                                classNames.push("evenement");
                                classNames.push("inscrire");
                                classNames.push("all");
                            break;

                            case 1:
                                bcColor = "#e14658";
                                classNames.push("tournoi");
                                classNames.push("inscrire");
                                if (event['DC'])
                                {

                                    classNames.push("membre");
                                    classNames.push("sympathisant");

                                } 
                            break;

                            case 2:
                                bcColor = "#aaaaaa";
                                classNames.push("partieLibre");
                                classNames.push("inscrire");

                                classNames.push("membre");
                                classNames.push("sympathisant");
                            break;
                            
                            case 3: 
                                bcColor = "#c0b3a0";
                                classNames.push("competition");
                                // On vérifie que la compétition n'a pas commencé
                                if (event.stade == 3) // Stade d'inscription
                                {

                                    classNames.push("inscrire");

                                }
                            break;

                        }

                        let title = "";

                        if (isInArray(registeredForEvents, event[0]))
                        {
                            title += "★ ";
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

                $(document).on('change', '.selectpicker', function (e) {
                    var selected = []; //array to store value
                    $(this).find("option:selected").each(function(key,value){
                        selected.push(value.id); //push the text to array
                    });
                    /*

                        dC  = Compétitions
                        dT  = Tournois
                        dPL = Parties Libres
                        dES = Evénements spéciaux
                        dIN = Evenements inscrits

                    */
                    
                    if (selected.includes('dC'))
                    {
                        $('.competition').show();
                    } else {$('.competition').hide();}
                    if (selected.includes('dT'))
                    {
                        $('.tournoi').show();
                    } else {$('.tournoi').hide();}
                    if (selected.includes('dPL'))
                    {
                        $('.partieLibre').show();
                    } else {$('.partieLibre').hide();}
                    if (selected.includes('dES'))
                    {
                        $('.evenement').show();
                    } else {$('.evenement').hide();}
                    if (selected.includes('dIN'))
                    {
                        $('.inscrit').show();
                    } else {$('.inscrit').hide();}


                        
                });

                $(window).resize(function() {
                    var calHeight = $(window).height()*0.85;
                    calendar.setOption('height', calHeight);
                });

            });


        </script>
            

    </head>


<style>

.bootstrap-select .dropdown-toggle .filter-option{

    position: relative;
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

                    <li class="active">
                        <a href="#"><span class="fa fa-home mr-3"></span> Agenda</a>
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
                
                <h2>Évènements à venir</h2>

                <div id='calendar'></div>

            </div>
        </div>

    </body>
</html>