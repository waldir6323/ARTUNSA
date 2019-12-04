<!DOCTYPE html>
<head>
    <title>Cargar lista de alumnos</title>
</head>
<div class='content-wrapper'>
    <div class='container-fluid'>
        <!-- Breadcrumbs-->
        <ol class='breadcrumb'>
            <li class='breadcrumb-item'>
                <a id="primero" href='../controller/ControladorAlumno.php'>GESTION ALUMNO</a>
            </li>
        </ol>

        <!-- Area para Registrare-->
        <div class="container" id="registro">
            <div class="card card-register mx-auto mt-5">
                <div id="cabecera" class="card-header">CARGAR ALUMNOS</div>
                <div class="card-body">
                    <form method='POST' action="../excel/index.php" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="nombreTaller">Seleccione el archivo:</label>
                            <input class='form-control' type="file" name="adjunto" accept=".xls">
                        </div>      

                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-md-12">
                                    <button type='submit' id="botonGuardar" class="btn btn-success btn-block ">Cargar</button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <!-- Example DataTables Card-->
    </div>
</div>    
