<?php

$archivo = $_FILES['adjunto']['name'];
$tipo = $_FILES['adjunto']['type'];
$destino = './tmp/' . $archivo;
if (copy($_FILES['adjunto']['tmp_name'], $destino))
    echo "Archivo Cargado Con Éxito\n";
else {
    echo'<script type="text/javascript">
            alert("Error al cargar archivo.");
            window.location.href="../viewPhp/excelGrupoAlumno.php"
            </script>';
    return;
}

include '../conexion/conexion.php';

require_once 'Excel/reader.php';

$data = new Spreadsheet_Excel_Reader();
$data->setOutputEncoding('CP1251');
$data->read($destino);

/* $asignatura = $data->sheets[0]['cells'][5][1];
$asignatura = substr($asignatura, strpos($asignatura, ':') + 2);
// echo("Asignatura ".$asignatura);
if ($asignatura == '') {
    echo'<script type="text/javascript">
            alert("Archivo inválido");
            window.location.href="../vistaPhp/excelGrupoAlumno.php"
            </script>';
    return;
}
$idCurso = getidCurso($asignatura);

// Obtener semestre actual
$idSemestreActual = getSemestreActual();

$idDocente = '-1';

// Nombre grupo
$grupo = utf8_encode($data->sheets[0]['cells'][6][1]);
$grupo = substr($grupo, strpos($grupo, 'GRUPO :') + 8);

$idGrupo = verificarGrupo($idSemestreActual, $idCurso, $grupo, $idDocente); */

for ($i = 7; $i <= count($data->sheets[0]['cells']); $i++) {

    $cui = $data->sheets[0]['cells'][$i][1];
	
    $idAlumno = verificarCui($cui);
	
	echo($idAlumno." -> ".$cui."<br>");
	
    if ($idAlumno == -1) { // No registrado
        $nombreAlumno = $data->sheets[0]['cells'][$i][2];

        $apellidos = getApellidos($nombreAlumno);
        $nombre = getNombres($nombreAlumno);
		
		echo($apellidos." - ".$nombre."<br>");
		
		$nombreEscuela = $data->sheets[0]['cells'][$i][3];
		
        $idAlumno = registrarAlumno($cui, $apellidos, $nombre, $nombreEscuela);
        // echo("<p>Se agregó a " . $cui . ": " . $nombre . " " . $apellidos . " (" . $idAlumno . ")");
    }
    echo("<p>Se agregó a " . $cui);
    // $estado = $data->sheets[0]['cells'][$i][4];
    // registrarAlumnoCurso($idAlumno, $idGrupo, $estado);
}
echo'<script type="text/javascript">
  alert("Alumnos registrados.");
  window.location.href="../viewPhp/gestionCargaAlumno.php"
  </script>';

//----------------------------------------------------------------------------------------------
////----------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------
// FUNCIONES
function getSemestreActual() {
    $sql = "SELECT idSemestre FROM `semestre` WHERE actual=1";
    $c = new Conexion();
    $con = $c->getConexion();
    $result = $con->query($sql);

    $resp = '-1';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $resp = $row['idSemestre'];
        }
    }
    return $resp;
}

function getidCurso($asignatura) {
    $sql = "SELECT idCurso FROM curso WHERE nombre LIKE \"%" . $asignatura . "%\"";
    $c = new Conexion();
    $con = $c->getConexion();
    $result = $con->query($sql);

    $resp = '-1';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $resp = $row['idCurso'];
        }
    } else { // registrar curso
        $sql = "INSERT INTO `curso` (`idCurso`, `codigoCurso`, `nombre`, `estadoReg`) VALUES (NULL, '0000000', '" . $asignatura . "', '1');";
        $c = new Conexion();
        $con = $c->getConexion();
        $con->query($sql);
        $resp = getidCurso($asignatura);
    }

    return $resp;
}

function verificarGrupo($idSemestre, $idCurso, $nombre, $idDocente) {
    $sql = "SELECT idGrupo FROM grupo WHERE idSemestre=" . $idSemestre . " and idCurso=" . $idCurso . " and nombre='" . $nombre . "'";
    $c = new Conexion();
    $con = $c->getConexion();
    $result = $con->query($sql);

    $resp = '-1';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $resp = $row['idGrupo'];
        }
    } else { // registrar grupo
        registrarGrupo($idSemestre, $idDocente, $idCurso, $nombre);
        $resp = verificarGrupo($idSemestre, $idCurso, $nombre, $idDocente);
    }

    return $resp;
}

function registrarGrupo($idSemestre, $idDocente, $idCurso, $nombre) {
    $sql = "INSERT INTO `grupo` (`idGrupo`, `idSemestre`, `idDocente`, `idCurso`, `nombre`, `estadoReg`) VALUES (NULL, '" . $idSemestre . "', '" . $idDocente . "', '" . $idCurso . "', '" . $nombre . "', '1')";
    $c = new Conexion();
    $con = $c->getConexion();
    $con->query($sql);
}

function getNombres($nombreCompleto) {
    $resp = substr($nombreCompleto, strpos($nombreCompleto, ',') + 2);
    // $resp = str_replace("/", " ", $resp);
    return $resp;
}

function getApellidos($nombreCompleto) {
    $resp = substr($nombreCompleto, 0, strpos($nombreCompleto, ','));
    $resp = str_replace("/", " ", $resp);
    return $resp;
}

function verificarCui($cui) {
    $sql = "SELECT IdAlumno FROM alumno WHERE AlumnoCodigo=" . $cui;
    $c = new Conexion();
    $con = $c->getConexion();
    $result = $con->query($sql);

    $resp = '-1';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $resp = $row['IdAlumno'];
        }
    }
    return $resp;
}

function registrarAlumno($cui, $apellidos, $nombre, $escuela) {
    $sql = "SELECT idEscuela FROM escuela WHERE NombreEscuela='" . $escuela."'";
    $c = new Conexion();
    $con = $c->getConexion();
    $result = $con->query($sql);
    $idEscuela=-1;
	
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $idEscuela = $row['idEscuela'];
        }
    }
	
	// $con->close();
	// con = $c->getConexion();
	
	if ($idEscuela==-1){
		insertarEscuela($escuela);
		registrarAlumno($cui, $apellidos, $nombre, $escuela);
	} else {
		$sql = "INSERT INTO `alumno` (`idAlumno`, `AlumnoCodigo`, `AlumnoNombre`, `AlumnoApellido`, `IdEscuela1`) VALUES (NULL, '" . $cui . "', '" . $nombre . "', '" . $apellidos . "', ".$idEscuela.");";
		$con->query($sql);
		$con->close();
	}
}

function insertarEscuela($nomEscuela){
	$sql = "INSERT INTO `escuela` (`idEscuela`, `idFacultad`, `NombreEscuela`, `EscuelaEstado`) VALUES (NULL, -1, '" . $nomEscuela . "', 1);";
	echo("escuela -> ".$sql);
	$c = new Conexion();
	$con = $c->getConexion();
	$con->query($sql);
	
	$con->close();
}


function registrarAlumnoCurso($idAlumno, $idGrupo, $estado) {
    // si ya existe confirmar estado
    $sql = "SELECT idGrupoAlumno, estadoReg FROM `grupoalumno` WHERE idAlumno=" . $idAlumno . " and idGrupo=" . $idGrupo;
    $c = new Conexion();
    $con = $c->getConexion();
    $result = $con->query($sql);

    $estadoExcel = ($estado == '') ? '1' : '0';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($estadoExcel != $row['estadoReg']) {
                echo("Diferentes " . $row['idGrupoAlumno']);
                actualizarEstadoRegistro($row['idGrupoAlumno'], $estadoExcel);
            }
        }
    } else { // else registrar con nuevo estado
        $sql = "INSERT INTO `grupoalumno` (`idGrupoAlumno`, `idAlumno`, `idGrupo`, `estadoReg`) VALUES (NULL, '" . $idAlumno . "', '" . $idGrupo . "', '" . $estadoExcel . "');";
        $c = new Conexion();
        $con = $c->getConexion();
        $con->query($sql);
    }
}
?>