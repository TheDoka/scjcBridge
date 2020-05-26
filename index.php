<?php 

include('assets/php/utils.php');
$_POST['statut'] = "admin";
if (!logged())
{
    echo "<script>alert(\"Vous n'êtes pas connecté, vous allez être redirigé vers la page de connexion.\"); window.location = 'login.php'; </script>";
}

?>

<style>

#content{
    background-color: #6e91b9;
}

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
            <link href="assets/css/navbar.css" rel="stylesheet" crossorigin="anonymous">

        <!-- dthmlx scheduler --> 
            <link rel="stylesheet" href="assets/css/scheduler/dhtmlxscheduler_material.css" type="text/css" charset="utf-8">

            <script src='assets/js/scheduler/dhtmlxscheduler.js' type="text/javascript" charset="utf-8"></script>
	        <script src='assets/js/scheduler/ext/dhtmlxscheduler_timeline.js' type="text/javascript" charset="utf-8"></script>
            <script src="assets/js/scheduler/ext/dhtmlxscheduler_recurring.js" type="text/javascript"></script>

            <script src="assets/js/scheduler/locale/locale_fr.js" type="text/javascript" charset="utf-8"></script>

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

            window.addEventListener("DOMContentLoaded", function(){

                scheduler.locale.labels.matrix_tab = "Vue simplifiée"
                scheduler.locale.labels.section_custom="Section";
                scheduler.config.details_on_create=true;
                scheduler.config.details_on_dblclick=true;
                scheduler.config.multi_day = true;


                //===============
                //Configuration
                //===============
                var sections=[
                    {key:1, label:"Matin"},
                    {key:2, label:"Après-Midi"},
                    {key:3, label:"Soirée"}
                ];

                scheduler.createTimelineView({
                    name:	"matrix",
                    x_unit:	"day",
                    x_date:	"%D %d %M",
                    x_step:	1,
                    x_size: 15,
                    y_unit:	sections,
                    y_property:	"section_id",
                    render:"bar"
                });

   
                //===============
                //Data loading
                //===============
                scheduler.config.lightbox.sections=[	
                    {name:"description", height:130, map_to:"text", type:"textarea" , focus:true},
                    {name:"custom", height:23, type:"select", options:sections, map_to:"section_id" },
                    {name:"time", height:72, type:"time", map_to:"auto"}
                ]

                scheduler.init('scheduler_here',new Date(2020,5,30),"matrix");
                //scheduler.load("./data/units.json");
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
                
                <div id="scheduler_here" class="dhx_cal_container" style='width:100%; height:84vh;'>
                    <div class="dhx_cal_navline">
                        
                        <div class="dhx_cal_prev_button">&nbsp;</div>
                        <div class="dhx_cal_next_button">&nbsp;</div>
                        <div class="dhx_cal_today_button"></div>

                        <div class="dhx_cal_date"></div>
                        <div class="dhx_cal_tab" name="day_tab" style="right:204px;"></div>
                        <div class="dhx_cal_tab" name="week_tab" style="right:140px;"></div>
                        <div style="width: 200px;" class="dhx_cal_tab" name="matrix_tab" style="right:300px;"></div>
                        <div class="dhx_cal_tab" name="month_tab" style="right:76px;"></div>

                        
                    </div>
                    <div class="dhx_cal_header">
                    </div>
                    <div class="dhx_cal_data">
                    </div>		
                </div>

            </div>
        </div>

    </body>
</html>