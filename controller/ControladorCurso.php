<?php
require __DIR__.'/../model/Modelo.php';
require __DIR__.'/../model/ModeloCurso.php';
class Controller {
    public $model;

    public function __construct() {
        $this->model = new ModeloCurso();
    }

    public function invoke() {
        $estaFormularioLleno = isset($_POST['nombreCurso']) && isset($_POST['creditoCurso']);
        $estaFiltrando = isset($_POST['id']);
        $estaModificandoOEliminando = isset($_POST['modo']);
          
        
        if (!$estaFiltrando && !$estaFormularioLleno && !$estaModificandoOEliminando) {
            $cursos = $this->model->getLista();
            include '../viewPhp/gestionCurso.php';
        } elseif (!$estaFiltrando && $estaFormularioLleno && !$estaModificandoOEliminando) {
            $id = ($_POST['idCurso']);
            $nombre = $_POST['nombreCurso'];
            $creditos = $_POST['creditoCurso'];
            $curso = new Curso($id, $nombre, $creditos);
            if (!is_numeric($id)&& isset($_POST['guardar'])) {
                $this->model->addRegistro($curso);
                $cursos = $this->model->getLista();

                include '../viewPhp/gestionCurso.php';
            } else {
                $this->model->updRegistro($curso);
                $cursos = $this->model->getLista();

                include '../viewPhp/gestionCurso.php';
            }
        } elseif ($estaFiltrando && !$estaFormularioLleno && $estaModificandoOEliminando) {
            $id = $_POST['id'];
            if ($_POST['modo'] == 1) {

                $cursos = $this->model->getLista();
                $curso = $this->model->getRegistroPorId($id);
                echo $curso;
            }
            if ($_POST['modo'] == 2) {
                $this->model->delRegistroPorId($id);
                $cursos = $this->model->getLista();
                echo $cursos;
            }
            
        }
    }
    public function eliminar() {
        $id = ($_POST['id']);
        $this->model->delRegistroPorId($id);
    }
    }

$controller = new Controller();
$controller->invoke();

