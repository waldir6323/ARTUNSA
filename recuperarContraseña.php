
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        
        <title>Sistema de Asistencia de EPIS</title>
        <!-- Bootstrap core CSS-->
        <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <!-- Custom fonts for this template-->
        <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <!-- Custom styles for this template-->
        <link href="css/sb-admin.css" rel="stylesheet">
        <script>
            function valida() {
                var usu = $('#usuario').val;
                var ape = $('#contrasenia').val;
                if (isNumber(usu)) {
                    alert('fake');

                    return true;
                } else {
                    alert('Solo dígitos en el usuario');
                    return false;
                }
            } 
            $("#recuperarC").click(function(){
                alert("HOla");
            });


        </script>
    </head>

    <body class="bg-dark">
        <div class="container">
		<br>
		
		<br>
		 <center> <h1 class="navbar-brand" href="#" style="font-size:25px; color:white;" > SISTEMA ADMINISTRATIVO ARTUNSA </h1></center>
			<div class="card card-login mx-auto mt-5">
                <center><div class="card-header">RECUPERAR CONTRASEÑA</div></center>
                <div class="card-body">
                    <form action="controller/ControladorRecuperarC.php" method="POST" >
                        <div class="form-group">
                            <label for="exampleInputEmail1">Correo Electrónico</label>
                            <input class="form-control" id="emailUser" type="email"  name="emailUser" aria-describedby="emailHelp" placeholder="Correo Electrónico">
                        </div>
                        
                        
                        <button type="submit" class="btn btn-primary btn-block" >ENVIAR A CORREO</button>
                    </form>
                    <div class="text-center">
                        <?php
                        if(isset($msg)){
                            echo "<a class='d-block small mt-3' style=color:red;  >" . $msg . "</a>";
                        }
                        ?>
                    </div>
                    <div class="text-center">
                        <br>
						<a id=recuperarC class="btn btn-primary btn-block" href="controller/ControladorLogin.php">ATRAS</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Bootstrap core JavaScript-->
        <script src="../vendor/jquery/jquery.min.js"></script>
        <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <!-- Core plugin JavaScript-->
        <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
        <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <!-- Custom fonts for this template-->
        <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <!-- Custom styles for this template-->
        <link href="../css/sb-admin.css" rel="stylesheet">
    </body>

</html>
