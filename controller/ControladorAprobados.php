<?php
require __DIR__.'/../model/Modelo.php';
require __DIR__.'/../model/ModeloAlumnoGrupo.php';
require 'OperationsController.php';

class ControllerAprobados implements OperationsController {
    public $model;

    public function __construct() {
        $this->model = new ModeloAlumnoGrupo();
    }

    public function invoke() {
        $estaFormularioLleno = isset($_POST['idAlumno']) && isset($_POST['idCurso']);
        $estaFiltrando = isset($_POST['id']);
        $estaModificandoOEliminando = isset($_POST['modo']);
        
        
        if (!$estaFiltrando && !$estaFormularioLleno && !$estaModificandoOEliminando) {
            $this->inicio();
        } elseif (!$estaFiltrando && $estaFormularioLleno && !$estaModificandoOEliminando) {
            $id = ($_POST['idAlumnoGrupo']);            
            if (!is_numeric($id)) {
                $this->agregar();
            } else {
                $this->modificar();
            }
        } elseif ($estaFiltrando && !$estaFormularioLleno && $estaModificandoOEliminando) {
            $id = $_POST['id'];
            
            if ($_POST['modo'] == 1) {
               
                $this->eliminar();
            }
            if ($_POST['modo'] == 2) {
               $this->activar($id);
             }
 
        }
    }
    public function conseguir() {
        $id = $_POST['id'];
        echo $this->model->getRegistroPorId($id);
    }

    public function agregar() {
    }

    public function eliminar() {
        $id = $_POST['id'];
        $this->model->updEstRegPorId($id,0);
    }

    public function inicio() {
        $modeloGrupo = new ModeloAlumnoGrupo();
        $idPeriodo=$_SESSION['IdPeriodo'];
        //estado 4 para los aprobados
        $info = $modeloGrupo->getListaJoinGrupoAlumnoAll($idPeriodo,4);
        
        include '../viewPhp/gestionAprobados.php';
    }

    public function modificar() {
      
    }
  
}

if (!isset($_SESSION)) {
    session_start();
}
//echo "viene al contro";
$controller = new ControllerAprobados();
$controller->invoke();

