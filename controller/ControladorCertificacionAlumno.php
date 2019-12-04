<?php

require __DIR__ . '/../model/Modelo.php';
require __DIR__ . '/../model/ModeloCertificacionAlumno.php';

class ControllerCertificacionAlumno{


    public function __construct() {
    }

    public function invoke() {
        $modelo = new ModeloCertificacionAlumno();
        


        $modo = isset($_POST['modo']);
        if(!$modo){
            include '../viewPhp/gestionCertificacionAlumno.php';
        	//$modelo->crearCertificacionAlumnos();
        }elseif($modo && ($_POST['modo']==1)){
        }elseif($modo && ($_POST['modo']==2)){
        }elseif($modo && ($_POST['modo']==3)){
            
            
            echo "".$modelo->getTalleresAlumno($_POST['cui']);
        }elseif($modo && ($_POST['modo']==4)){
            
            $idPeriodo=$_SESSION['IdPeriodo'];
            echo "".$modelo->getTalleres($idPeriodo);
        }

        //echo "ggwp";
    }
}
session_start();
$controller = new ControllerCertificacionAlumno();
$controller->invoke();