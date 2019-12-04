<?php

require __DIR__ . '/../model/Modelo.php';
require __DIR__ . '/../model/ModeloGrupo.php';

require __DIR__ . '/../model/ModeloCurso.php';
require __DIR__ . '/../model/ModeloAlumnoGrupo.php';
require __DIR__ . '/../model/ModeloAlumno.php';
require __DIR__ . '/../model/ModeloPeriodo.php';


require __DIR__ . '/../model/ModeloHorario.php';
require __DIR__ . '/../model/ModeloDocente.php';
require_once('../PHPMailer-master/src/PHPMailer.php');
require_once('../PHPMailer-master/src/SMTP.php');
require "OperationsController.php";

class ControllerPrematricula implements OperationsController {

    public $model;
    public $elimino;

    public function __construct() {
        $this->model = new ModeloGrupo();
    }

    public function invoke() {

        $estaFormularioLleno = isset($_POST['IdGrupo']) && isset($_POST['IdCurso'])
                && isset($_POST['IdDocente']) && isset($_POST['GrupoNombre'])
                 && isset($_POST['GrupoCodigo']) && isset($_POST['GrupoCapacidad']);

        $estaFiltrando = isset($_POST['id']);
        $estaModificandoOEliminando = isset($_POST['modo']);

        if (!$estaFiltrando && !$estaFormularioLleno && !$estaModificandoOEliminando) {
            $this->inicio();
        } elseif (!$estaFiltrando && $estaFormularioLleno && !$estaModificandoOEliminando) {
            $id = $_POST['IdGrupo'];

            if (!is_numeric($id)) {
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
                $this->getListaGrupo($id);
            }
            if ($_POST['modo'] == 4) {
                $this->getListaHorario($id);
            }
            if ($_POST['modo'] == 5) {

                echo $codigoGenerado=$this->generateRandomString(5,$id);
                $this->enviarCodigoCorreo($id,$codigoGenerado);
            }
            if ($_POST['modo'] == 6) {
                $codigoAlumno=$id;
                $codigoGrupo=$_POST['grup'];
                $this->preMatricular($codigoAlumno, $codigoGrupo);
                }
        }
    }
	public function preMatricular($idAlumno, $idGrupo) {
        $grupo = new Grupo($idGrupo);

        $modelGrupo = new ModeloAlumnoGrupo();
        $_POST = array();

        $modeloAlumno = $this->model->getListaPorCampo("alumno", "AlumnoCodigo", "\"" . $idAlumno . "\"");
        $alumnoo = $modeloAlumno[0]['IdAlumno'];
        $alumno = new Alumno(new BuilderAlumno($alumnoo));
        $json =$modelGrupo->getRegistroPorAlumno($alumno);
        $array= json_decode($json,true);
        $repetido = False; 
        if ($array != NULL) {
            $repetido=True;              
        }
        
        if(!$repetido){
			$alumnoGrupo = new AlumnoGrupo("", $grupo, $alumno, 5);
			$modelGrupo->addRegistro($alumnoGrupo);
        echo "Se realizó la pre-matrícula correctamente!";
        
        }else{
            echo "Ya te has prematriculado.Si deseas cambiar tu prematricula ve a la Oficina de Arte y Cultura";
        }
        //$modelGrupo->addRegistro($alumnoGrupo);
        
    }

    public function agregar() {
        $id = $_POST['IdGrupo'];
        $idCurso = ($_POST['IdCurso']);
        $idDocente = $_POST['IdDocente'];
        $nombre = $_POST['GrupoNombre'];
        
        $capacidad = $_POST['GrupoCapacidad'];
        $codigo=$_POST['GrupoCodigo'];
        //$idPeriodo = $_SESSION['IdPeriodo'] ;
        
        $curso = new Curso($idCurso);
        $docente = new Docente(new BuilderDocente($idDocente));
        $grupo = new Grupo($id, $curso, $docente, $nombre,$capacidad,$codigo);
       // $grupo->setPeriodo($idPeriodo);
        $this->model->addRegistro($grupo);
        $this->inicio();
    }


    public function conseguir() {
        $modeloCurso = new ModeloCurso();
        $modeloDocente = new ModeloDocente();

        $cursos = $modeloCurso->getLista();
        $docentes = $modeloDocente->getLista();
        
        $id = $_POST['id'];
        $grupo = $this->model->getRegistroPorId($id);
        echo $grupo;
    }

    public function eliminar() {
        $id = ($_POST['id']);
        echo $this->model->delRegistroPorId($id);
        $grupos = $this->model->getLista();
        echo $grupos;
    }

    public function inicio() {
        $modelPeriodo = new ModeloPeriodo();

        $periodoActivo = $modelPeriodo->getIdActivo();
        $jsonPeriodoActivo = json_decode($periodoActivo, true);
        $idPeriodoActivo = $jsonPeriodoActivo['IdPeriodo'];
        $modeloCurso = new ModeloCurso();
        $modeloDocente = new ModeloDocente();

        $cursos = $modeloCurso->getLista();
        $docentes = $modeloDocente->getLista();
                
        $grupos = $this->model->getListaJoin($idPeriodoActivo);
        
        //echo $idPeriodo;
        include '../viewPhp/gestionPrematricula.php';
    }

    public function modificar() {
        $id = $_POST['IdGrupo'];
        $idCurso = ($_POST['IdCurso']);
        $idDocente = $_POST['IdDocente'];
        $nombre = $_POST['GrupoNombre'];
        
        $capacidad = $_POST['GrupoCapacidad'];
        $codigo=$_POST['GrupoCodigo'];
        
        $curso = new Curso($idCurso);
        $docente = new Docente(new BuilderDocente($idDocente));
        $grupo = new Grupo($id, $curso, $docente, $nombre,$capacidad,$codigo);
        $this->model->updRegistro($grupo);
        $this->inicio();
    }
     public function cerrarGrupo($idGrupo) {
        
        $this->model->cambiarEstado($idGrupo);
        $this->model->cerrarGrupo($idGrupo);
    }
    public function getListaGrupo($id){
        $modelPeriodo = new ModeloPeriodo();

        $periodoActivo = $modelPeriodo->getIdActivo();
        $jsonPeriodoActivo = json_decode($periodoActivo, true);
        $idPeriodo = $jsonPeriodoActivo['IdPeriodo'];
        echo $this->model->getListaGrupoPorIdCursoConcupos($id,$idPeriodo);

    }
    public function getListaHorario($id){
        $modeloHorario=new ModeloHorario();
        echo $modeloHorario->getListaJoin($id);
    }

    public function generateRandomString($length = 5 , $correo) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        $hoy = getdate();
        $regreso =md5($correo.$hoy['hours']);
        return substr($regreso, -5);
    } 

    public function enviarCodigoCorreo($correo,$codigoGenerado){

        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->IsSMTP(); // enable SMTP
        $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
        $mail->SMTPAuth = true; // authentication enabled
        $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 465; // or 587
        $mail->IsHTML(true);
        $mail->Username = "team.getsoft";
        $mail->Password = "darfrevis";
        $mail->SetFrom('team.getsoft@gmail.com');
        $mail->Subject = "Test";
        $mail->Body = "Hola qué tal, este es el código para tu pre-matricula: ".$codigoGenerado;
        $mail->AddAddress($correo);
        //$mail­->MsgHTML("Hola que tal, esta es tu contraseña: ".$docente['DoncenteContra ']);
        if(!$mail->Send()) {
           // return $mail­->ErrorInfo;
            return "no envié";
        } else {
           
        }
    }


   
}
session_start();
$controller = new ControllerPrematricula();
$controller->invoke();
