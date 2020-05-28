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


            // IMPORTANT ! 

            var eid = <?php echo $_GET['eid'] ?>;
            var aid = <?php echo $_COOKIE['logged'] ?>;

            initWithEvent();

            function initWithEvent()
            {

                // 1.             
                    // Populate and construct the calendar
                    // Also shows the description

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

                    
                    // I. Check if player already registered
                    $.post('assets/sql/interface.php',
                        {
                            function: 'getPlayersRegisteredForEvent',
                            eid: eid,
                        }, function(data) {
                            data = JSON.parse(data);
                            if (data)
                            {
                                maSituation(data);
                                
                            }

                        });
                
                    //
            }


            function unregister(iid)
            {

                if (confirm("Êtes-vous sûr de vouloir vous desinscrire de l'êvenement?"))
                {
                    $.post('assets/sql/interface.php',
                        {
                            function: 'unregisterFromEvent',
                            iid: iid,
                        }, function(data) {
                            
                                if (data)
                                {
                                    alert('Une erreur est survenue!\n' + data);
                                }

                        });
                }

            }

            function maSituation(data)
            {
                console.log(data);
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
                        
                        /* Récupères le nombre de membre de la paire
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
                                    console.log(pid);
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
                $('#paire').text("Inscription par paire de " + event['paires']);


                let tmp = "";

                switch (parseInt(event['type']))
                {
                    case 1:
                        // Tournoi
                        tmp = "Apéro: " + event['apero'] +
                              " / Repas: " + event['repas'];
                        $('#more').text(tmp);
                    break;
                    
                    case 2:
                        // Partie libre
                    break;
                    
                    case 3:

                        // Compétition
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
        //border-bottom: 1px solid black;
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
                
                <h2 id="titre"></h2>
                
                <div id="eventHeader" class="LeftRightHeader">
                    <div id="headerText" class="left">
                        <span id="debut">Commence le: 13/08/2001 à 13h00</span></br>
                        <span id="fin">Fini le: 13/08/2001 à 13h00</span></br>
                        <span id="lieu">Lieu: Loudun, adresse</span></br>
                        <span id="paire">Inscription par paire de 2</span></br>
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

                        <h3>Partenaire favoris: </h3>
                            <table class="table" id="SOS">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Nom</th>
                                        <th scope="col">Prénom</th>
                                        <th scope="col">Inscrire avec</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th scope="row">1</th>
                                        <td>Mark</td>
                                        <td>Otto</td>
                                        <td><input type="checkbox"></input></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">2</th>
                                        <td>Jacob</td>
                                        <td>Thornton</td>
                                        <td><button>Inviter</button></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">3</th>
                                        <td>Larry</td>
                                        <td>the Bird</td>
                                        <td><button>Inviter</button></td>
                                    </tr>
                                </tbody>
                            </table>
                    
                        <h3>SOS Partenaire: </h3>
                        <table class="table" id="SOS">
                            <thead>
                                <tr>
                                <th scope="col">#</th>
                                <th scope="col">First</th>
                                <th scope="col">Last</th>
                                <th scope="col">Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                <th scope="row">1</th>
                                <td>Mark</td>
                                <td>Otto</td>
                                <td><button>Inscrire</button></td>
                                </tr>
                                <tr>
                                <th scope="row">2</th>
                                <td>Jacob</td>
                                <td>Thornton</td>
                                <td><button>Inscrire</button></td>
                                </tr>
                                <tr>
                                <th scope="row">3</th>
                                <td>Larry</td>
                                <td>the Bird</td>
                                <td><button>Inscrire</button></td>
                                </tr>
                            </tbody>
                        </table>
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
                            
                            <h3>Adherents disponibles: </h3>
                                <table class="table" id="SOS">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">First</th>
                                            <th scope="col">Last</th>
                                            <th scope="col">Options</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                    </tbody>
                                </table>

                        </div>
                    </div>
                    
                
                
                    <footer class="footer">
                        <div class="container-fluid">
                            <div class="row text-center">
                                <div style="padding: 1em; "class="col-lg-12">      
                                    <button type="button" class="btn btn-secondary btn-lg btn-mid20">S'inscire</button>
                                </div>
                            </div>
                        </div>        
                 </footer>

                </div>
                

        </div>



        
    </body>
</html>