<?php 

include('assets/php/utils.php');
$_POST['statut'] = "admin";
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

        <!-- BOOTSTRAP -->
            <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet"  crossorigin="anonymous">
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
       
        <!-- Popper --> 
            <script src="assets/js/popper.js"></script>
        
        <!-- FontAwesome -->    
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

        <!-- NavBar -->    
            <link href="assets/css/navbar.css" rel="stylesheet" crossorigin="anonymous">

        <!-- FullCalendar --> 
            <link href='assets/css/fullcalendar/core/main.css' rel='stylesheet' />
            <link href='assets/css/fullcalendar/daygrid/main.css' rel='stylesheet' />
            <script src='assets/js/fullcalendar/core/main.js'></script>
            <script src='assets/js/fullcalendar/interaction/main.js'></script>
            <script src='assets/js/fullcalendar/daygrid/main.js'></script>

         <!-- FullCalendar --> 
            <script src='assets/js/utils.js'></script>

        <!-- next --> 

        <script type="text/javascript">

            $(document).ready(function(){

                var todayDate = new Date().toISOString().slice(0,10);
                var aid = <?php echo intval($_COOKIE['logged']) ?>;
                var statut = "";
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

                var calendarEl = document.getElementById('calendar');

                var calendar = new FullCalendar.Calendar(calendarEl, {
                    locale: 'fr',
                    height: 650,
                    plugins: [ 'interaction', 'dayGrid' ],
                    header: {
                        left: 'prevYear,prev,next,nextYear today',
                        center: 'title',
                        right: 'dayGridMonth,dayGridWeek,dayGridDay'
                    },
                    defaultDate: todayDate,
                    firstDay: 1,
                    defaultView: 'dayGridWeek',
                    navLinks: true, // can click day/week names to navigate views
                    editable: true,
                    eventLimit: true, // allow "more" link when too many events
                    selectable:true,
                    selectHelper:true,
                    select: function(start, end, allDay)
                    {
                       
                    },
                    editable:true,
                    eventResize:function(event)
                    {

                        var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
                        var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");
                        var title = event.title;
                        var id = event.id;
                        $.ajax({
                            url:"update.php",
                            type:"POST",
                            data:{title:title, start:start, end:end, id:id},
                            success:function(){
                                calendar.fullCalendar('refetchEvents');
                                alert('Event Update');
                            }
                        })
                    },

                    eventDrop:function(event)
                    {
                        console.log(event);
                        var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
                        var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");
                        var title = event.title;
                        var id = event.id;

                        $.ajax({
                            url:"update.php",
                            type:"POST",
                            data:{title:title, start:start, end:end, id:id},
                            success:function()
                            {
                                calendar.fullCalendar('refetchEvents');
                                alert("Event Updated");
                            }
                        });
                    },

                    eventClick:function(event)
                    {
                        event = event.event;

                        if (event['classNames'][1] == 'inscrire')
                        {

                            if (!event['classNames'].includes('all'))
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
                });

                callendarImport();

                getUser(aid);
                function getUser(aid)
                {

                    $.post('assets/sql/interface.php',
                    {
                        function: 'getUser',
                        aid: aid
                    }, function(data) {
                        data = JSON.parse(data);
                        statut = data['statut'];
                    });

                }
        
                // TO REMOVE
                statut = "membre";

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
                        }
                        title += event['titre'];

                        calendar.addEvent({
                            id: event[0],
                            title: title,
                            start: event['dteDebut'],
                            end: event['dteFin'],
                            classNames: classNames,
                            backgroundColor: bcColor
                        });

                    });

                    
                    

                }  



                
            });

 


        </script>
            

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

                    <li class="active">
                        <a href="#"><span class="fa fa-home mr-3"></span> Agenda</a>
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
                
                <h2>Évènements à venir</h2>
                

                <div id='calendar'></div>

            </div>
        </div>

    </body>
</html>