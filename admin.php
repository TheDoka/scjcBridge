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

         <!-- Papaparse -->     
            <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.1.0/papaparse.min.js" crossorigin="anonymous"></script>


        <script type="text/javascript">


            let imported_ = [];

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

                var type = 0;

                $('#confirmImport').on('click',function(e){

                        $.post('assets/sql/interface.php',
                            {
                                function: 'importEvents',
                                events: imported_,
                                type: type
                            }, function(data) {
                                if (data.length > 0)
                                {
                                    alert("Une erreur est survenue: \n" + data);
                                    console.log(data);
                                } else {
                                    alert("Import éffectué avec succès!")
                                }
                        });
                
    


                });

                $("input:file").change(function ()
                {
                    checkParseAndDraw();
                });

                function checkParseAndDraw()
                {
                        
                    if ($("#import-form")[0].checkValidity() )
                        {
                            $('#files').parse({
                                config: {
                                    delimiter: "auto",
                                    complete: displayHTMLTable,
                                },
                                error: function(err, file)
                                {
                                    alert('Une erreur est survenue, veuillez verifier le fichier. \,' + err + '\n' + file);
                                }
                            });
                        } else {
                            alert('Aucun fichier importé.');
                        }

                }
                
                function displayHTMLTable(results){
                    var tdata = "";
                    var data = results.data;

                    // On sauvegarde le resultat dans une autre variable pour pouvoir réutiliser lors de l'importation
                        imported_ = results;

                    // On importe les header (doit toujours être la première ligne)
                        tdata+= "<thead><tr>";
                        var row = data[0];
                        var cells = row.join(",").split(",");
                            
                        for(j=0;j<cells.length;j++){
                            tdata+= "<td>";
                            tdata+= cells[j];
                            tdata+= "</th>";
                        }
                        tdata+= "</tr></thead>";

                    // On importe les données du CSV
                    for(i=1;i<data.length-1;i++){
                        
                        if (data[i][0] != ",,,,,")
                        {
                            tdata+= "<tr>";
                            var row = data[i];
                            
                            var cells = row.join(",").split(",");

                            for(j=0;j<cells.length;j++){
                                tdata+= "<td>";
                                tdata+= cells[j];
                                tdata+= "</th>";
                            }
                            tdata+= "</tr>";
                        }
                    }
                    // La table prends les données
                    $("#parsed_csv_list").html(tdata);

                    switch(data[0][0].split(',').length)
                    {
                        case 12: // compétition
                            type = 3;
                        break;
                        case 7: // partie libre
                            type = 2;
                        break;
                        case 11: // tournoi
                            type = 1;
                        break;
                    }
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

                    <li>
                        <a href="index.php"><span class="fa fa-home mr-3"></span> Agenda</a>
                    </li>

                    <li>
                        <a href="profil.php"><span class="fa fa-gift mr-3"></span> Profil / Partenaires </a>
                    </li>

                    <?php if ($_POST['statut'] == "admin")
                    {
                        echo '<li class="active">
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
                
                <h2>Gestion de l'agenda</h2>

                <form id="import-form" class="form-inline">
 
                    <div style="display: felx;">
                        <div class="custom-file">
                            <label style="width:400px" class="custom-file-label" for="files">Importer fichier .CSV</label>
                            <input type="file" id="files" class="form-control custom-file-input" accept=".csv" required />
                            <button style="margin-left: 100px;" id="confirmImport" class="btn form-control btn-warning" type="button">Confirmer l'importation dans la base.</button>
                        </div>

                    </div>

                        

                    
                </form>

                

                <table class='table table-striped table-bordered' id="parsed_csv_list">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Date début</th>
                            <th>Heure de début</th>
                            <th>Date fin</th>
                            <th>Heure de fin</th>
                            <th>Type</th>
                            <th>Lieu</th>
                        </tr>
                    </thead>
                    <tbody></tbody>

                </table>      

            </div>

   
        </div>

    </body>
</html>