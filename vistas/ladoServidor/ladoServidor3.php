<?php

/*
 * DataTables example server-side processing script.
 * Please note that this script is intentionally extremely simply to show how server-side processing can be implemented, and probably shouldn't be used as the basis for a large complex system. It is suitable for simple use cases as for learning.
 * See http://datatables.net/usage/server-side for full details on the server-side processing requirements of DataTables.
 * @license MIT - http://datatables.net/license_mit
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $opcion = $_POST['opcion'];
} else {
    $opcion = $_GET['opcion'];
}

require('./conectarLadoServidor.php');
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */
require('../../librerias/DataTables/ladoServidor/ssp.class.php');
require('../../librerias/DataTables/ladoServidor/ssp.customized.class.php');

switch ($opcion) {

    case 'listarUsuarios':
        $_SESSION['fecha'] = Date("Y-m-d");

        // DB table to use
        $table = 'usuario';

        // Table's primary key
        $primaryKey = 'idUsuario';
        $where = " 1 ";

        $joinQuery = "FROM (SELECT `idUsuario`,`admin`, `edicion`, `ingreso`, `consulta`, `nombre`, `dni`,`planilla`, `admCostos`, `admArt`, `admPlanilla`,  `admUnif`, `plame`, `admMovil`, `tipoUsuario`, `cargaDespachos`, `seguridad`, `invitado`, `fchCreacion`, `fchVencimiento` FROM `usuario`) AS usu";

        $dt = 0;
        $columns = array(
          array('db' => 'usu.idUsuario', 'dt' => $dt++, 'field' => 'idUsuario'),
          array('db' => 'usu.nombre', 'dt' => $dt++, 'field' => 'nombre'),
          array('db' => 'usu.dni', 'dt' => $dt++, 'field' => 'dni'),
          array('db' => 'usu.ingreso', 'dt' => $dt++, 'field' => 'ingreso'),
          array('db' => 'usu.edicion', 'dt' => $dt++, 'field' => 'edicion'),
          array('db' => 'usu.consulta', 'dt' => $dt++, 'field' => 'consulta'),
          array('db' => 'usu.admCostos', 'dt' => $dt++, 'field' => 'admCostos'),
          array('db' => 'usu.admArt', 'dt' => $dt++, 'field' => 'admArt'),
          array('db' => 'usu.admin', 'dt' => $dt++, 'field' => 'admin'),
          array('db' => 'usu.planilla', 'dt' => $dt++, 'field' => 'planilla'),
          array('db' => 'usu.admPlanilla', 'dt' => $dt++, 'field' => 'admPlanilla'),
          array('db' => 'usu.admUnif', 'dt' => $dt++, 'field' => 'admUnif'),
          array('db' => 'usu.plame', 'dt' => $dt++, 'field' => 'plame'),
          array('db' => 'usu.admMovil', 'dt' => $dt++, 'field' => 'admMovil'),
          array('db' => 'usu.tipoUsuario', 'dt' => $dt++, 'field' => 'tipoUsuario'),
          array('db' => 'usu.cargaDespachos', 'dt' => $dt++, 'field' => 'cargaDespachos'),
          array('db' => 'usu.seguridad', 'dt' => $dt++, 'field' => 'seguridad'),
          array('db' => 'usu.invitado', 'dt' => $dt++, 'field' => 'invitado'),
          array('db' => 'usu.fchCreacion', 'dt' => $dt++, 'field' => 'fchCreacion'),
          array('db' => 'usu.fchVencimiento', 'dt' => $dt++, 'field' => 'fchVencimiento'),

          array('db' => 'usu.idUsuario', 'dt' => $dt++,'field' => 'idUsuario', 'formatter' => function( $d, $row ) {
            $fecha = $_SESSION['fecha'];

              $cadena = "";

              if ($_GET["admin"] == "Si"){
                $cadena .= "<center>
                              <img border='0' src='imagenes/lapiz.png' class = 'editar' width='15' height='15'>
                              <img border='0' src='imagenes/btnRemoverRojo.png' class = 'eliminar' width='18' height='18'>
                          </center>";
              }
              return $cadena;
          } ),
      );

    break;

    case 'listarInfracciones':
        $_SESSION['fecha'] = Date("Y-m-d");

        // DB table to use
        $table = 'infracciones';

        // Table's primary key
        $primaryKey = 'idinfraccion';
        $where = " 1 ";

        $joinQuery = "FROM (SELECT `idinfraccion`,`falta`, `infraccion`, `calificacion`, `monto`, `descuento`, `sancion`,`puntos`, `medida_preventiva`, `solidario`, `usuario_registro`,`fecha_registro`, `usuario_edicion`, `fecha_edicion` FROM `infracciones`) AS infrac";

        $dt = 0;
        $columns = array(
          array('db' => 'infrac.idinfraccion', 'dt' => $dt++, 'field' => 'idinfraccion'),
          array('db' => 'infrac.falta', 'dt' => $dt++, 'field' => 'falta'),
          array('db' => 'infrac.infraccion', 'dt' => $dt++, 'field' => 'infraccion'),
          array('db' => 'infrac.calificacion', 'dt' => $dt++, 'field' => 'calificacion'),
          array('db' => 'infrac.monto', 'dt' => $dt++, 'field' => 'monto'),
          array('db' => 'infrac.descuento', 'dt' => $dt++, 'field' => 'descuento'),
          array('db' => 'infrac.sancion', 'dt' => $dt++, 'field' => 'sancion'),
          array('db' => 'infrac.puntos', 'dt' => $dt++, 'field' => 'puntos'),
          array('db' => 'infrac.medida_preventiva', 'dt' => $dt++, 'field' => 'medida_preventiva'),
          array('db' => 'infrac.solidario', 'dt' => $dt++, 'field' => 'solidario'),
          array('db' => 'infrac.fecha_registro', 'dt' => $dt++, 'field' => 'fecha_registro'),
          array('db' => 'infrac.usuario_registro', 'dt' => $dt++, 'field' => 'usuario_registro'),
          array('db' => 'infrac.fecha_edicion', 'dt' => $dt++, 'field' => 'fecha_edicion'),
          array('db' => 'infrac.usuario_edicion', 'dt' => $dt++, 'field' => 'usuario_edicion'),

          array('db' => 'infrac.idinfraccion', 'dt' => $dt++,'field' => 'idinfraccion', 'formatter' => function( $d, $row ) {
            $fecha = $_SESSION['fecha'];

            $cadena = "<center>
                        <img border='0' src='imagenes/lapiz.png' class = 'editar' width='15' height='15'>
                    </center>";
            return $cadena;
          } ),
        );

    break;

}

switch ($opcion) {
    case 'listarUsuarios':
    case 'listarInfracciones':
    {
        echo json_encode(
              SSP2::simple($_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery,$where)
        );
        break;
    }

    case 'xxxdespachoPuntos':    //no se utiliza
    {
        echo json_encode(
              SSP2::simple($_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery,$where, $groupBy)
        );
        break;
    }

    default: {
        echo json_encode(
              SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns)
        );
        break;
        }
}
//echo json_encode(
//SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
//);
?>