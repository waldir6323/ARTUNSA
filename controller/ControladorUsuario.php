<?php

require __DIR__ . '/../model/Modelo.php';

require __DIR__ . '/../model/ModeloUsuario.php';

require __DIR__ . '/../model/ModeloCurso.php';

require "OperationsController.php";

class ControllerUsuario implements OperationsController {

    public $model;
    
    public function __construct() {
        $this->model = new ModeloUsuario();
    }

    public function invoke() {
        $estaFormularioLleno = isset($_POST['UsuarioNombre']) &&
                isset($_POST['UsuarioApellido']) && isset($_POST['UsuarioContrasenia']) &&
                isset($_POST['IdTipoUsuario']);
               
        $estaFiltrando = isset($_POST['id']);
        $estaModificandoOEliminando = isset($_POST['modo']);
        if (!$estaFiltrando && !$estaFormularioLleno && !$estaModificandoOEliminando) {
            $this->inicio();
        } elseif (!$estaFiltrando && $estaFormularioLleno && !$estaModificandoOEliminando) {
            $id = $_POST['IdUsuario'];
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

    public function conseguir() {
        $id = $_POST['id'];
        echo $this->model->getRegistroPorId($id);
    }

    public function agregar() {
        
        $tipouser = $_POST['IdTipoUsuario'];
        $unombre = $_POST['UsuarioNombre'];
        $uapellido = $_POST['UsuarioApellido'];
        $ucontrasenia = $_POST['UsuarioContrasenia'];
  
        
      
        $Usuario=new Usuario("",$tipouser, $unombre, $uapellido,$estado="1");
        $Usuario->contrasenia($ucontrasenia);
        
        $this->model->addRegistro($Usuario);
        
        $this->inicio();
    }
    
    public function eliminar() { 
        $id = ($_POST['id']);
        $this->model->delRegistroPorId($id);
    }

    public function inicio() {
        $ModeloUsuario = new ModeloUsuario();
        $Modelo = new Modelo();
        $usuarios = $ModeloUsuario->getLista();
        $tiposusers = $ModeloUsuario->getTodosTiposUsuarios();
        //echo $tiposusers;
      
        include '../viewPhp/gestionUsuario.php';
    }

    public function modificar() {
        $id = $_POST['IdUsuario'];
        $nombre = $_POST['UsuarioNombre'];
        $apellido = $_POST['UsuarioApellido'];
        $idtipoUsuario = $_POST['IdTipoUsuario'];
        $contrasenia = $_POST['UsuarioContrasenia'];
    
        
        $Usuario=new Usuario($id,$idtipoUsuario, $nombre, $apellido,$estado="1");
        $Usuario->contrasenia($contrasenia);
        $this->model->updRegistro($Usuario);
     
        $this->inicio();
    }
    
}

$controller = new ControllerUsuario();
$controller->invoke();
$_POST = array();
