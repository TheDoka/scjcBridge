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
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" integrity="sha256-rByPlHULObEjJ6XQxW/flG2r+22R5dKiAoef+aXWfik=" crossorigin="anonymous" />
       
        <!-- BOOTSTRAP -->
            <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet"  crossorigin="anonymous">
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
        
        <!-- Jquery --> 
            <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>  <!-- Modal forms -->

        <!-- FontAwesome -->    
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

        <!-- NavBar -->    
            <link href="assets/css/navbar.css" rel="stylesheet">

        <!-- DataTable --> 
            <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
            <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">

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
        
            $(document).on('click', '.retirerFavori', function (e) {

                favorisID = e.target.id;
                rowID = $(this).closest('tr').attr('id');

                fnom = $(this).closest("tr").find('td:eq(1)').text();
                fprenom = $(this).closest("tr").find('td:eq(2)').text();

                retirerFavori(favorisID, fnom, fprenom, rowID);
            });

            $(document).on('click', '.ajouterFavori', function (e) {
                favorisID = e.target.id;
                rowID = $(this).closest('tr').attr('id');

                fnom = $(this).closest("tr").find('td:eq(1)').text();
                fprenom = $(this).closest("tr").find('td:eq(2)').text();

                ajouterFavori(favorisID, fnom, fprenom, rowID);
            });

            var tableMesFavoris = $('#tableMesFavoris').DataTable({
               search: false,
               paging: false,
               ordering: false,
               info: false,
               pageLength: 10,
               initComplete: function (oSettings) {
                    $('.dataTables_filter').each(function () {
                        $(this).append('<button id="addExterne" class="btn btn-primary" style="margin-right: 1em; margin-left: 1em;" type="button">Ajouter externe</button>');
                    });
                },
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


            var tableJoueurs = $('#tableJoueurs').DataTable({
                ordering: false,
                info: false,
                pageLength: 50,
                
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

            var aid = <?php echo $_COOKIE['logged'] ?>;
            var user = getUser(aid);
            var statut = user['statut'];
            var anom = user['nom'];
            var admin = user['statut'] == "Administrateur"; 
        
            if (admin)
            {
                $('#gestionBase').show();
            }


            initTables();
            
            function initTables()
            {

                // 1.
                // Populate tables

                    
                var favoris = [];
                // I. Get favoris
                $.ajax({
                    type: "POST",
                    async: false,
                    url: "assets/sql/interface.php",
                    data: {
                        function: 'getPlayerFavorite',
                        aid: aid, 
                        except: JSON.stringify(favoris),
                    },
                    success: function(data)
                    {
                        console.log(data);
                        favoris = JSON.parse(data);

                        if (favoris)
                        {
                            mesFavoris(favoris);
                        }
                    },
                });

                // On se rajoute à l'array, car on ne veut pas être afficher dans la liste de joueurs
                favoris.push([aid]);


                // II. Get Joueurs

                $.post('assets/sql/interface.php',
                    {
                        function: 'getEveryMembers',
                        except: JSON.stringify(favoris),
                    }, function(data) {
                        joueurs = JSON.parse(data);
                        if (data)
                        {

                            listeJoueurs(joueurs);
                        }

                    });

            }



            function retirerFavori(fid, fnom, fprenom, rid)
            {

                if (confirm("Êtes-vous sûr de vouloir retirer ce joueur de vos favoris?"))
                {
                    $.post('assets/sql/interface.php',
                        {
                            function: 'unsetFromFavorite',
                            aid: aid,
                            fid: fid,
                        }, function(data) {
                                console.log(data);
                                if (data)
                                {
                                    alert('Une erreur est survenue!\n' + data);
                                } else {
                                    

                                    tableMesFavoris.row('#' + rid).remove().draw();
                                    tableJoueurs.row.add([
                                            tableJoueurs.rows().count(),
                                            fnom,
                                            fprenom,
                                            `<td><button id="${fid}" type="button" class="btn btn-danger ajouterFavori">Ajouter favori</button></td>`
                                        ]).node().id = tableJoueurs.rows().count();


                                    
                                    tableJoueurs.draw();
                                    
                                }
                               
                        });
                }

            }

            function ajouterFavori(fid, fnom, fprenom, rid)
            {

                if (confirm("Êtes-vous sûr de vouloir ajouter ce joueur à vos favoris?"))
                {
                    $.post('assets/sql/interface.php',
                        {
                            function: 'addToFavorite',
                            aid: aid,
                            fid: fid,
                        }, function(data) {
                                console.log(data);
                                if (data)
                                {
                                    alert('Une erreur est survenue!\n' + data);
                                } else {

                                    
                                    tableJoueurs.row('#' + rid).remove().draw();
                                    tableMesFavoris.row.add([
                                            tableJoueurs.rows().count(),
                                            fnom,
                                            fprenom,
                                            `<td><button id="${fid}" type="button" class="btn btn-danger retirerFavori">Retirer favori</button></td>`
                                        ]).node().id = tableMesFavoris.rows().count();

                                        
                                        tableMesFavoris.draw();
                                        

                                }

                        });
                }
            }
            
            function mesFavoris(data)
            {

                for (let i = 0; i < data.length; i++) {

                    tableMesFavoris.row.add([
                            i+1,
                            data[i]['nom'],
                            data[i]['prenom'],
                            `<td><button id="${data[i][0]}" type="button" class="btn btn-danger retirerFavori">Retirer favori</button></td>`,
                        ]).node().id = i;
                    

                }
                tableJoueurs.draw();

            }

            function listeJoueurs(data)
            {
                for (let i = 0; i < data.length; i++) {
                    tableJoueurs.row.add([
                                            i+1,
                                            data[i]['nom'],
                                            data[i]['prenom'],
                                            `<td><button id="${data[i][0]}" type="button" class="btn btn-danger ajouterFavori">Ajouter favori</button></td>`
                                        ]).node().id = i;
                }
                tableJoueurs.draw();
            }
            
            function addUser()
            {

                valid =($('#lastname').val() + $('#name').val() + $('#email').val() + $('#license').val()).length > 5;

                if (valid)
                {
                    if (confirm("Êtes-vous sûr de vouloir ajouter ce joueur?"))
                    {
                        $.post('assets/sql/interface.php',
                            {
                                function: 'quickInsertUserAndFav',
                                aid: aid,
                                lastname: $('#lastname').val(),
                                name: $('#name').val(),
                                mail: $('#email').val(),
                                license: $('#license').val(),
                            }, function(data) {
                                    if (data)
                                    {
                                        alert('Une erreur est survenue!\n' + data);
                                    } else {
                                        alert('Joueur ajouté!');
                                        document.location.reload(true);
                                    }                      
                            });
                    }
                } else {
                    alert('Veuillez vérifier les champs.')
                }

            }

            var dialog = $( "#dialog-form" ).dialog({
                autoOpen: false,
                height: 350,
                width: 280,
                modal: true,
                resize: false,
                buttons: {
                    "Ajouter l'utilisateur": addUser,
                    Cancel: function() {
                        dialog.dialog( "close" );
                    }
                },
                close: function() {
                    form[ 0 ].reset();
                }
            });

            form = dialog.find( "form" ).on( "submit", function( event ) {
                event.preventDefault();

            });

            $(document).on('click', '#addExterne', function (e) {
                dialog.dialog( "open" );
            });
     
        });





        </script>
            

    </head>


<style>


</style>

    <body>
        
    <div id="dialog-form" title="Ajouter un utilisateur">
        <p class="validateTips">Toutes les champs sont requis.</p>
        
        <form>
            <fieldset>
                <label for="lastname">Nom: </label>
                <input type="text" name="lastname" id="lastname" value="" class="text ui-widget-content ui-corner-all">
                
                <label for="name">Prenom: </label>
                <input type="text" name="name" id="name" value="" class="text ui-widget-content ui-corner-all">

                <label for="email">Mail: </label>
                <input type="text" name="email" id="email" value="" class="text ui-widget-content ui-corner-all">

                <label for="license">Numéro licence: </label>
                <input type="number" name="license" id="license" value="" class="text ui-widget-content ui-corner-all">
            
                <!-- Allow form submission with keyboard without duplicating the dialog button -->
                <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
            </fieldset>	
		</form>
    </div>

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
                        <a href="profil.php"><span class="fa fa-gift mr-3active"></span> Profil / Partenaires </a>
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
                
                <h2 id="titre">Gestion du profil</h2>
                
                <h3>Mes favoris: </h3>
                    <table class="table" id="tableMesFavoris">
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

                    <h3>Liste des joueurs: </h3>
                    <table class="table" id="tableJoueurs">
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



        
    </body>
</html>