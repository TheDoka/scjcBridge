<?php 

include('assets/php/utils.php');
include('assets/sql/sql.php');


if (sizeof($_GET) > 0)
{
    if (empty($_GET['logoff'])) 
    {
        setcookie("logged", "", time()-3600);
    }
} else {

    if (logged())
    {
        header("location: index.php");
    }

}

?>

<html>

    <head>

        <!-- Jquery -->    
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
            <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
        <!-- BOOTSTRAP -->
            <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet"  crossorigin="anonymous">
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
        
        <!-- FontAwesome -->    
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

        <!-- LoginPage -->
            <link rel="stylesheet" href="assets/css/login.css">

    </head>

    <script>

            
        $(document).ready(function(){

            // Trigger quand le button connexion est pressé
            $("#connexion").click(function(e) {

                // Récupère les informations saisies et éxéxute la connexion
                    makeLogin($('#licenseId').val(), $('#pass').val())

            });

            function makeLogin(licenseId, pass)
            {

                $.post('assets/sql/interface.php',
                    {
                        function: 'login',
                        licenseId: licenseId,
                        pass: pass
                    }, function(data) {
                        if (data)
                        {
                            $.cookie('logged', data);
                            window.location = 'index.php';
                        } else {
                            $('#logError').show();
                        }
                });

            }


        });


    </script>



    <body>
        

        <div class="sidenav">
                <div class="login-main-text">
                    <h2>Bridge Club du Haut Poitou</h2>
                    <h3>Page de connexion</h3>
                    <p>Veuillez vous connecter pour avoir accès.</p>
                </div>
        </div>

        <div class="main">
            <div class="col-md-6 col-sm-12">
                <div class="login-form">

                    <form>
                        <div class="form-group">
                            <label>Numéro de license</label>
                            <input id="licenseId" type="text" class="form-control" placeholder="Numéro composé de 10 chiffres au maximum">
                        </div>

                        <div class="form-group">
                            <label>Mot de passe</label>
                            <input id="pass" type="password" class="form-control" placeholder="Mot de passe">
                        </div>
                        
                        <span id="logError" style="color: red; display: none;">Connexion impossible, veuillez réessayer<br></span> 

                        <button type="button" id="connexion" class="btn btn-black">Se connecter</button>
                        <button type="button" class="btn btn-secondary">S'enrengistrer</button>
                    </form>

                </div>
            </div>
        </div>

    </body>
</html>