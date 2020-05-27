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

        <!-- next --> 

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


            });

                    
            document.addEventListener('DOMContentLoaded', function() {
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
                    defaultDate: '2020-02-12',
                    navLinks: true, // can click day/week names to navigate views
                    editable: true,
                    eventLimit: true, // allow "more" link when too many events
                    events: [
                        {
                        title: 'All Day Event',
                        start: '2020-02-01'
                        },
                        {
                        title: 'Long Event',
                        start: '2020-02-07',
                        end: '2020-02-10'
                        },
                        {
                        groupId: 999,
                        title: 'Repeating Event',
                        start: '2020-02-09T16:00:00'
                        },
                        {
                        groupId: 999,
                        title: 'Repeating Event',
                        start: '2020-02-16T16:00:00'
                        },
                        {
                        title: 'Conference',
                        start: '2020-02-11',
                        end: '2020-02-13'
                        },
                        {
                        title: 'Meeting',
                        start: '2020-02-12T10:30:00',
                        end: '2020-02-12T12:30:00'
                        },
                        {
                        title: 'Lunch',
                        start: '2020-02-12T12:00:00'
                        },
                        {
                        title: 'Meeting',
                        start: '2020-02-12T14:30:00'
                        },
                        {
                        title: 'Happy Hour',
                        start: '2020-02-12T17:30:00'
                        },
                        {
                        title: 'Dinner',
                        start: '2020-02-12T20:00:00'
                        },
                        {
                        title: 'Birthday Party',
                        start: '2020-02-13T07:00:00'
                        },
                        {
                        title: 'Click for Google',
                        url: 'http://google.com/',
                        start: '2020-02-28'
                        }
                    ],
                    // events: 'load.php',
                    selectable:true,
                    selectHelper:true,
                    select: function(start, end, allDay)
                    {
                        var title = prompt("Enter Event Title");
                            if(title)
                            {
                                var start = $.fullCalendar.formatDate(start, "Y-MM-DD HH:mm:ss");
                                var end = $.fullCalendar.formatDate(end, "Y-MM-DD HH:mm:ss");
                                $.ajax({
                                    url:"insert.php",
                                    type:"POST",
                                    data:{title:title, start:start, end:end},
                                    success:function()
                                    {
                                        calendar.fullCalendar('refetchEvents');
                                        alert("Added Successfully");
                                    }
                                })
                            }
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
                        if(confirm("Are you sure you want to remove it?"))
                        {
                            var id = event.id;
                            
                            $.ajax({
                                url:"delete.php",
                                type:"POST",
                                data:{id:id},
                                success:function()
                                {
                                    calendar.fullCalendar('refetchEvents');
                                    alert("Event Removed");
                                }
                            })
                        }
                    },
                });

                calendar.render();
            });



        </script>
            

    </head>


    <body>
        
        <div class="wrapper d-flex align-items-stretch">
            
            <nav id="sidebar">

                <div class="custom-menu">
                    <button type="button" id="sidebarCollapse" class="btn btn-primary"></button>
                </div>

                <div class="img bg-wrap text-center py-4" style="background-image: url(ressources/img/bg_1.jpg);">
                    <div class="user-logo">
                        <div class="img" style="background-image: url(ressources/img/jm.jpg);"></div>
                        <h3>Jean-Marie</h3>
                    </div>
                </div>

                <ul class="list-unstyled components mb-5">

                    <li class="active">
                        <a href="#"><span class="fa fa-home mr-3"></span> Agenda</a>
                    </li>

                    <li>
                        <a href="#"><span class="fa fa-download mr-3 notif"><small class="d-flex align-items-center justify-content-center">5</small></span> Inscription</a>
                    </li>

                    <li>
                        <a href="#"><span class="fa fa-gift mr-3"></span> Profil / Partenaires </a>
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
                
                <h2>Tableaux des tournois à venir</h2>
                

                <div id='calendar'></div>

            </div>
        </div>

    </body>
</html>