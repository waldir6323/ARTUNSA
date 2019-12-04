<?php
require __DIR__.'/../model/Modelo.php';
require __DIR__.'/../model/ModeloParteDiario.php';
class Controller {

    public $model;

    public function __construct() {
        $this->model = new ModeloParteDiario();
    }

    public function invoke() {

        $estaFormularioLleno = isset($_POST['fecha']) && isset($_POST['modo']) && isset($_POST['dia']);
        if(!$estaFormularioLleno) {
            include '../viewPhp/partediario.php';
        }
        else {
            $fecha = $_POST['fecha'];
            $modo = $_POST['modo'];
            $diaStr = $_POST['dia'];
            // echo $modo;
            // echo $fecha;
            //Buscar por fecha
            if($modo == 1) {
                $anio = substr($fecha, 6, 4);
                $mes = substr($fecha, 3, 2);
                $dia = substr($fecha, 0, 2);
                $rows = $this->model->getListaByDate($anio.'-'.$mes.'-'.$dia, $diaStr);
                header('Content-Type: application/json');
                echo json_encode($rows);
                //include '../viewPhp/partediario.php';
            }
            else {
               echo 'modoX';
            }
        }

        //include '../viewPhp/partediario.php';
    }

}

$controller = new Controller();
$controller->invoke();
