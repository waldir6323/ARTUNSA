<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ModeloAlumnoGrupo extends Modelo implements Operations {

    public function __construct() {
        parent::__construct();
    }

    public function addRegistro($registro) {
        $temp = new AlumnoGrupo();
        $temp = $registro;
        $sql = "INSERT INTO `alumnogrupo`("
                . " `IdGrupo`,"
                . " `IdAlumno`,"
                . " `AlumnoGrupoEstado`)"
                . " VALUES (" . $temp->getGrupo()->getId()
                . "," . $temp->getAlumno()->getId()
                . "," . $temp->getEstado() . ")";
        parent::getConn()->query($sql);
        return $sql;
    }
    
    public function addRegistroNoRepetido($registro) {
        $temp = new AlumnoGrupo();
        $temp = $registro;
        $sql = "INSERT INTO `alumnogrupo`("
                . " `IdGrupo`,"
                . " `IdAlumno`,"
                . " `AlumnoGrupoEstado`)"
                . " VALUES (" . $temp->getGrupo()->getId()
                . "," . $temp->getAlumno()->getId()
                . "," . $temp->getEstado() . ")";
        parent::getConn()->query($sql);
        return $sql;
    }

    public function delRegistroPorId($id) {

        $sql = "UPDATE `alumnogrupo` SET `AlumnoGrupoEstado`=0 WHERE `IdAlumnoGrupo`=" . $id;
        parent::getConn()->query($sql);
        return $sql;
    }
    public function updEstRegPorId($id,$estado) {

        $sql = "UPDATE `alumnogrupo` SET `AlumnoGrupoEstado`=".$estado." WHERE `IdAlumnoGrupo`=" . $id;
        parent::getConn()->query($sql);
        return $sql;
    }
    public function getListaJoin($idProfesor,$idPeriodo) {
        $sql = "SELECT grupo.IdGrupo, "
                . "curso.CursoNombre , "
                . "grupo.GrupoNombre"
                . " FROM grupo "
                . "INNER JOIN curso ON grupo.IdCurso= curso.IdCurso "
                . "where grupo.GrupoEstado=1 and grupo.IdPeriodo=".$idPeriodo." AND grupo.IdDocente = " . $idProfesor;
        $result = parent::getConn()->query($sql);
        $outp = array();
        $outp = $result->fetch_all(MYSQLI_ASSOC);
        
        return json_encode($outp);
    }
    
    public function getListaJoinGrupoAlumnoAll($idPeriodo,$estado) {
        $sql = "select alumnogrupo.IdAlumnoGrupo,"
                . "grupo.IdGrupo,"
                . "alumno.IdAlumno,"
                . "alumno.AlumnoCodigo,"
                . "alumno.AlumnoNombre,"
                . "alumno.AlumnoApellido,"
                . " grupo.GrupoCodigo,"
                . "grupo.GrupoNombre,"
                . "curso.CursoNombre "
                . "from alumnogrupo INNER JOIN alumno ON alumnogrupo.IdAlumno=alumno.IdAlumno"
                . " INNER JOIN grupo on alumnogrupo.IdGrupo=grupo.IdGrupo"
                . " INNER JOIN curso on grupo.IdCurso=curso.IdCurso"
                . " where alumnogrupo.AlumnoGrupoEstado=".$estado
                . " and grupo.IdPeriodo=" . $idPeriodo;

        $result = parent::getConn()->query($sql);
        $outp = array();
        $outp = $result->fetch_all(MYSQLI_ASSOC);
        
        return json_encode($outp);
    }

    //getListaAlumno es igual que  getAsistenciaGrupo("esta completo")
    public function getListaAlumno($idGrupo) {
        $sql = "SELECT alu.AlumnoNombre , alu.AlumnoApellido, alu.AlumnoCodigo , alu.AlumnoEstado FROM alumnogrupo LEFT JOIN alumno as alu ON alumnogrupo.IdAlumno = alu.IdAlumno   WHERE alumnogrupo.AlumnoGrupoEstado= 1 AND alumnogrupo.IdGrupo = " . $idGrupo;
        $result = parent::getConn()->query($sql);
        $outp = array();
        $outp = $result->fetch_all(MYSQLI_ASSOC);

        return json_encode($outp);
    }

    public function getAsistenciaGrupo($id) {
        $sql = "SELECT  alumno.AlumnoNombre , alumno.AlumnoApellido ,"
                . " alumno.AlumnoCodigo,asistenciaalumno.AsistenciaAlumnoEstado,"
                . "asistenciaalumno.AsistenciaAlumnoEstado as entrada,"
                . "asistenciaalumno.AsistenciaAlumnoNumero as fecha,"
                . " grupo.IdGrupo from"
                . "((asistenciaalumno INNER JOIN alumno on  asistenciaalumno.IdAlumno= alumno.IdAlumno)"
                . "INNER JOIN grupo  on asistenciaalumno.IdGrupo=grupo.IdGrupo )"
                . "  where grupo.IdGrupo= " . $id . " ORDER by alumno.`IdAlumno` ,asistenciaalumno.`AsistenciaAlumnoNumero` ";
        $result = parent::getConn()->query($sql);
        $outp = array();

        $fechas = array();
        $fechasSalida = array();
        $codigo = -1;
        $i = -1;

        $Nombre = "";
        $Apellido = "";
        $fechasTem = "";
        while ($fila = mysqli_fetch_array($result, MYSQLI_ASSOC)) {

            if ($codigo != $fila["AlumnoCodigo"]) {
                if ($i >= 0) {
                    $salida = array('codigo' => $codigo, 'nombre' => $Nombre, 'apellido' => $Apellido, 'fechasAsistidas' => $fechas);
                    $outp[] = $salida;
                }

                $codigo = $fila ["AlumnoCodigo"];
                $Nombre = $fila ["AlumnoNombre"];
                $Apellido = $fila ["AlumnoApellido"];
                $i++;
                $fechasTem = $fechas;
                $fechas = array();
            }
            if ($i < 1) {
                //$fechasSalida[]=$fila["fecha"];
                $date = date_create($fila["fecha"]);
            }
            $fechas [] = $fila ["entrada"];
        }


        $salida = array('codigo' => $codigo, 'nombre' => $Nombre, 'apellido' => $Apellido, 'fechasAsistidas' => $fechas);

        $outp[] = $salida;
        $f = array();
        $j = 1;
        while ($j <= 16) {
            $f[] = $j;
            $j++;
        }
        $finalOut = array('fechas' => $f, 'Alumnos' => $outp);
        $finalOut['consulta'] = $sql;
        $finalOut ['alu'] = $i;
        return json_encode($finalOut);
    }

    public function getLista() {
        $sql = "SELECT grupo.IdGrupo,
         curso.CursoNombre ,
         grupo.GrupoNombre 
         FROM grupo INNER JOIN curso ON grupo.IdCurso= curso.IdCurso where grupo.GrupoEstado=1";
        $result = parent::getConn()->query($sql);
        $outp = array();
        $outp = $result->fetch_all(MYSQLI_ASSOC);
        return json_encode($outp);
    }

    public function getRegistroPorId($id) {
        $sql = "select alumnogrupo.IdAlumnoGrupo,"
                . "grupo.IdGrupo,"
                . "alumno.IdAlumno,"
                . "alumno.AlumnoCodigo,"
                . "alumno.AlumnoNombre,"
                . "alumno.AlumnoApellido,"
                . " grupo.GrupoCodigo,"
                . "grupo.GrupoNombre,"
                . "curso.CursoNombre"
                . " from alumnogrupo INNER JOIN alumno ON alumnogrupo.IdAlumno=alumno.IdAlumno"
                . " INNER JOIN grupo on alumnogrupo.IdGrupo=grupo.IdGrupo"
                . " INNER JOIN curso on grupo.IdCurso=curso.IdCurso"
                . " where alumnogrupo.AlumnoGrupoEstado=1 and alumnogrupo.IdAlumnoGrupo=" . $id;

        $result = $this->getConn()->query($sql);

        $outp = array();
        $outp = $result->fetch_all(MYSQLI_ASSOC);

        
        return json_encode($outp[0]);
    }
   public function getRegistroPorAlumno($id) {
        $sql = "select alumnogrupo.IdAlumnoGrupo,"
                . "grupo.IdGrupo,"
                . "alumno.IdAlumno,"
                . "alumno.AlumnoCodigo,"
                . "alumno.AlumnoNombre,"
                . "alumno.AlumnoApellido,"
                . " grupo.GrupoCodigo,"
                . "grupo.GrupoNombre,"
                . "curso.CursoNombre"
                . " from alumnogrupo INNER JOIN alumno ON alumnogrupo.IdAlumno=alumno.IdAlumno"
                . " INNER JOIN grupo on alumnogrupo.IdGrupo=grupo.IdGrupo"
                . " INNER JOIN curso on grupo.IdCurso=curso.IdCurso"
                . " where alumnogrupo.AlumnoGrupoEstado=1 and alumno.IdAlumno=" . $id;

        $result = $this->getConn()->query($sql);

        $outp = array();
        $outp = $result->fetch_all(MYSQLI_ASSOC);

        
        return json_encode($outp[0]);
    }

    
    public function updRegistro($registro) {
        $temp = new AlumnoGrupo();
        $temp = $registro;
        $sql = "UPDATE `alumnogrupo` "
                . "SET `IdGrupo`=" . $temp->getGrupo()->getId() . ","
                . "`IdAlumno`=" . $temp->getAlumno()->getId() . ","
                . "`AlumnoGrupoEstado`=" . $temp->getEstado() . ""
                . " WHERE `IdAlumnoGrupo`=" . $temp->getId();

        parent::getConn()->query($sql);
        return $sql;
    }
    public function updRegistroConRegistro($registro) {
        $temp = new AlumnoGrupo();
        $temp = $registro;
        $sql = "UPDATE `alumnogrupo` "
                . "`AlumnoGrupoEstado `=" . $temp->getEstado() . " "                
                . " WHERE `IdAlumno`=" . $temp->getAlumno()->getId() . " and  `IdGrupo` = ".$temp->getGrupo()->getId()  ;

        parent::getConn()->query($sql);
    }

    public function getMatriculasCodAlumno($codigoAlumno, $idPeriodo) {
        $modeloAlumno = parent::getListaPorCampo("alumno", "AlumnoCodigo", "\"" . $codigoAlumno . "\"");
        $idAlumno = $modeloAlumno[0]['IdAlumno'];

        $sql = "SELECT COUNT(*)  as matriculas FROM `alumnogrupo` WHERE `IdGrupo`"
                . " IN (SELECT IdGrupo FROM grupo where idPeriodo=".$idPeriodo.")"
                . " and AlumnoGrupoEstado=1 and IdAlumno=".$idAlumno;
        
        $result = parent::getConn()->query($sql);
        $outp = array();
        $outp = $result->fetch_all(MYSQLI_ASSOC);
        return json_encode($outp);
    }

}
