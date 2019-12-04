<?php

require __DIR__ . '/../model/Modelo.php';
require __DIR__ . '/../model/ModeloAlumno.php';

require __DIR__ . '/../model/ModeloCurso.php';

require "OperationsController.php";

class ControllerAlumno implements OperationsController {

    public $model;
    
    public function __construct() {
        $this->model = new ModeloAlumno();
    }

    public function invoke() {
        $estaFormularioLleno = isset($_POST['AlumnoNombre']) &&
                isset($_POST['AlumnoApellido']) && isset($_POST['AlumnoCodigo']) &&
                isset($_POST['IdEscuela1'])&&
                isset($_POST['AlumnoCorreo']) && isset($_POST['AlumnoCelular']);
        $estaFiltrando = isset($_POST['id']);
        $estaModificandoOEliminando = isset($_POST['modo']);
        if (!$estaFiltrando && !$estaFormularioLleno && !$estaModificandoOEliminando) {
            $this->inicio();
        } elseif (!$estaFiltrando && $estaFormularioLleno && !$estaModificandoOEliminando) {
            $id = $_POST['IdAlumno'];
            if (!is_numeric($id)  ) {
                $this->agregar();
            } else {
                $this->modificar();
            }
        } elseif ($estaFiltrando && !$estaFormularioLleno && $estaModificandoOEliminando) {
            $id = $_POST['id'];
            if ($_POST['modo'] == 1) {
                $this->conseguir();
            }
            if ($_POST['modo'] == 2) {
                $this->eliminar();
            }
             if ($_POST['modo'] == 3) {
                $idee =$_POST['id'];
                
                echo $this->model->getIdByCUICompleto($idee);
                
            }

        }
    }

    public function agregar() {
        $nombre = $_POST['AlumnoNombre'];
        $apellido = $_POST['AlumnoApellido'];
        $cui = $_POST['AlumnoCodigo'];
        $idescuela1 = $_POST['IdEscuela1'];
        $correo = $_POST['AlumnoCorreo'];
        $celular = $_POST['AlumnoCelular'];
        
        $builder = new BuilderAlumno();

        $buildAlumno=new BuilderAlumno("", $idescuela1,$nombre, $apellido,$cui);
        $buildAlumno->celular($celular);
        $buildAlumno->contrasenia("");
        $buildAlumno->correo($correo);

        $alumno = new Alumno($buildAlumno);
        $this->model->addRegistro($alumno);
        
        $this->inicio();
    }

    public function conseguir() {
        $id = $_POST['id'];
        echo $this->model->getRegistroPorId($id);
    }
    
    public function eliminar() { 
        $id = ($_POST['id']);
        $this->model->delRegistroPorId($id);
    }

    public function inicio() {
        $ModeloAlumno = new ModeloAlumno();
       
        $alumnos = $ModeloAlumno->getLista();

        $escuelas = json_encode($ModeloAlumno->getListaPorCampo("ESCUELA", "EscuelaEstado", "1"));
      
        include '../viewPhp/gestionAlumno.php';
    }

    public function modificar() {
        $id = $_POST['IdAlumno'];
        $nombre = $_POST['AlumnoNombre'];
        $apellido = $_POST['AlumnoApellido'];
        $cui = $_POST['AlumnoCodigo'];
        $idescuela1 = $_POST['IdEscuela1'];
        $correo = $_POST['AlumnoCorreo'];
        $celular = $_POST['AlumnoCelular'];
        
        $builder = new BuilderAlumno();
        $buildAlumno=new BuilderAlumno($id,$idescuela1, $nombre, $apellido,$cui);
        $buildAlumno->celular($celular);
        $buildAlumno->contrasenia("");
        $buildAlumno->correo($correo);
        

        $alumno = new Alumno($buildAlumno);
        $this->model->updRegistro($alumno);
        $this->inicio();
    }
    
}

$controller = new ControllerAlumno();
$controller->invoke();
$_POST = array();
