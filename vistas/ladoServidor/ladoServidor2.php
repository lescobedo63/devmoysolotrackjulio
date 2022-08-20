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

    case 'listarOrdenCompra':{
      // DB table to use
      $table = 'docpagotercero';
      // Table's primary key
      $primaryKey = 'docPagoTercero';

      $joinQuery = "";
      $dt = 0;
      $where = NULL;
      $whereDesp = " 1 ";
      $minimo = $_GET['min'];
      $maximo = $_GET['max'];

      $minimoDesp = $_GET['minDesp'];
      $maximoDesp = $_GET['maxDesp'];

      if ($minimo <> ""){
        if ($maximo == "") $where = " oc.fchCreacion >= '$minimo' ";
        else $where = " oc.fchCreacion BETWEEN '$minimo' AND '$maximo'";
      } else if ($maximo != "") $where = " oc.fchCreacion  <= '$maximo'";

      if ($minimoDesp <> ""){
        if ($maximoDesp == "") $whereDesp = " fchDespacho >= '$minimoDesp' ";
        else $whereDesp = " fchDespacho BETWEEN '$minimoDesp' AND '$maximoDesp'";
      } else if ($maximoDesp != "") $whereDesp = " fchDespacho  <= '$maximoDesp'";

    $joinQuery = " FROM (SELECT  t2.cant, t2.fchCreacion, t2.docPagoTercero, t2.docPagoTerTipo, t2.total, t2.igv, t2.totalOC , if(t2.totalOC >= 400 AND t2.tipoDocumento = 'Factura', round(0.04*t2.totalOC), 0  ) AS detraccion,  if(t2.totalOC >= 400 AND t2.tipoDocumento = 'Factura', t2.totalOC - round(0.04*t2.totalOC), t2.totalOC  ) AS totalDoc , t2.estado, t2.tercero, clasificacion, notaCred_Fact, if(t2.totalOC >= 400 AND t2.tipoDocumento = 'Factura', t2.totalOC - round(0.04*t2.totalOC), t2.totalOC  ) - notaCred_Fact AS pagLiq , tipoDocumento 

FROM (SELECT  t1.cant, t1.fchCreacion, t1.docPagoTercero, t1.docPagoTerTipo, t1.total, t1.igv, if(tipoDocumento = 'Factura', t1.totalOC, t1.totalOC - t1.igv ) AS totalOC , t1.estado, t1.tercero, clasificacion, if(tipoDocumento = 'Factura',notaCred_Fact , notaCred_Fact/1.18) AS notaCred_Fact ,  tipoDocumento

FROM ( SELECT docPagoTercero, fchDespacho, correlativo FROM despachovehiculotercero  WHERE 1 AND docPagoTercero IS NOT NULL group by docPagoTercero ) AS desterveh, 
     (
       SELECT sum(cant) AS cant, fchCreacion, docPagoTercero, docPagoTerTipo, sum(costo) AS total, sum(costo * 0.18) AS igv, sum(1.18 *costo) AS totalOC, estado, clasificacion , tercero, sum(ncr_fac) AS notaCred_Fact, tipoDocumento  
         FROM (   SELECT count(*) AS cant, docpag.fchCreacion, desvehter.docPagoTercero, desvehter.docPagoTerTipo, sum(desvehter.costoDia) AS costo, docpag.estado, concat(vehdue.documento ,' - ',vehdue.nombreCompleto) AS tercero, clasificacion, 'desveh' AS origen, 0 as ncr_fac , vehdue.tipoDocumento 
                  FROM `despachovehiculotercero` AS desvehter, vehiculodueno AS vehdue, docpagotercero AS docpag WHERE desvehter.docPagoTercero = docpag.docPagoTercero AND desvehter.docTercero = vehdue.documento AND desvehter.docPagoTerTipo = 'FACT' GROUP BY desvehter.`docPagoTercero`, desvehter.`docPagoTerTipo`  

UNION
      SELECT count(*) AS cant, '' AS fchCreacion,  `docPagoTercero`, `docPagoTerTipo`, sum( if (tipoMisc = 'Dsct', -1*monto, monto )) AS monto , '' AS estado, 'tercero' AS tercero, '' AS clasificacion, 'liqter' AS origen, 0 as ncr_fac , '' AS tipoDocumento FROM `liqterceromisc` WHERE 1 AND docPagoTerTipo = 'FACT'  GROUP BY `docPagoTercero`, `docPagoTerTipo`  
UNION
      SELECT count(*) AS cant,  '' AS fchCreacion, movim.`docPagoTercero`, movim.`docPagoTerTipo`,sum(`movim`.monto),  '' AS estado, 'tercero' AS tercero, '' AS clasificacion , 'movim' AS origen, 0 as ncr_fac, '' AS tipoDocumento FROM `ccmovimencab` AS movenc , `ccmovimientos` AS movim WHERE `movenc`.idMovimEncab = `movim`.nroOrden AND movim.docPagoTerTipo = 'FACT'  GROUP BY `docPagoTercero`, `docPagoTerTipo`   
UNION 
      SELECT count(*) AS cant, '' AS fchCreacion, `docPagoTercero`, `docPagoTerTipo`, sum( `total`) AS monto, '' AS estado, 'tercero' AS tercero,  '' AS clasificacion ,'combus' AS origen, 0 as ncr_fac, '' AS tipoDocumento FROM `combustible` WHERE 1  AND docPagoTerTipo = 'FACT'  GROUP BY `docPagoTercero`, `docPagoTerTipo`  
UNION
      SELECT count(*) AS cant, '' AS fchCreacion, `docPagoTercero`, `docPagoTerTipo`, sum( if(tipoConcepto = 'A favor proveedor',  `montoTotal`, -1*montoTotal)) AS monto, '' AS estado, 'tercero' AS tercero, '' AS clasificacion ,'ocurrter' AS origen, 0 as ncr_fac, '' AS tipoDocumento FROM `ocurrenciatercero` WHERE 1 AND docPagoTerTipo = 'FACT' GROUP BY `docPagoTercero`, `docPagoTerTipo` 
UNION
      SELECT 0 AS cant, '' AS fchCreacion,  `docPagoTercero`, 'FACT' AS aux, 0 AS monto , '' AS estado, 'tercero' AS tercero, '' AS clasificacion, 'ncr_fac' AS origen, sum(if(docPagoTerTipo = 'NCRD', 1.18*monto, 1.18*monto)) as ncr_fac , '' AS tipoDocumento FROM `liqterceromisc` WHERE 1 AND docPagoTerTipo IN ('NCRD','FMOY')  GROUP BY `docPagoTercero`, 'FACT'  
       ) AS oc 
       GROUP BY `docPagoTercero`, `docPagoTerTipo`
     ) AS t1 WHERE t1.docPagoTercero = desterveh.docPagoTercero
) AS t2) AS oc
 ";

      $columns = array(
        array('db' => 'oc.docPagoTercero', 'dt' => $dt++,  'field' => 'docPagoTercero'),
        array('db' => 'oc.fchCreacion', 'dt' => $dt++,  'field' => 'fchCreacion'),
        array('db' => 'oc.docPagoTercero', 'dt' => $dt++,  'field' => 'docPagoTercero'),
        array('db' => 'oc.estado', 'dt' => $dt++,  'field' => 'estado'),
        array('db' => 'oc.clasificacion', 'dt' => $dt++,  'field' => 'clasificacion'),
        array('db' => 'oc.tercero', 'dt' => $dt++,  'field' => 'tercero'),
        array('db' => 'oc.totalOC', 'dt' =>$dt++,  'field' => 'totalOC'), 
        array('db' => 'oc.detraccion', 'dt' =>$dt++,  'field' => 'detraccion'),
        array('db' => 'oc.pagLiq', 'dt' =>$dt++,  'field' => 'pagLiq'),
        array('db' => 'oc.docPagoTercero', 'dt' =>$dt++,  'field' => 'docPagoTercero',
          'formatter' => function( $d, $row ) {
              //  return $d;
            return "<img border='0' src='imagenes/lapiz.png' class = 'editar' width='14' height='14' align='left' guiaPorte=$d><img border='0' src='imagenes/menos.png' class = 'eliminar' width='14' height='14' align='left' guiaPorte=$d>";
        }),       
      );
    break;
  }



  case 'listarLogAcciones':
    // DB table to use
    $table = 'logaccion';

    // Table's primary key
    $primaryKey = 'id';

    // Array of database columns which should be read and sent back to DataTables.
    // The `db` parameter represents the column name in the database, while the `dt`
    // parameter represents the DataTables column identifier. In this case simple
    // indexes

    $dt = 0;

    $columns = array(
      array('db' => 'id', 'dt' => $dt++),
      array('db' => 'descripcion', 'dt' => $dt++),
      array('db' => 'idTrabajador', 'dt' => $dt++),
      array('db' => 'placa', 'dt' => $dt++),
      array('db' => 'usuario', 'dt' => $dt++),
      array('db' => 'fecha', 'dt' => $dt++), 
    );
    break;

  case 'repoEficVehiculo':{
      // DB table to use
      $table = 'combustible';
      // Table's primary key
      $primaryKey = 'idPlaca';

      $joinQuery = "";
      $dt = 0;
      $where = NULL;
      //$whereDesp = " 1 ";
      $minimo = $_GET['min'];
      $maximo = $_GET['max'];

      if ($minimo <> ""){
        if ($maximo == "") $where = " t1.fchCreacion >= '$minimo' ";
        else $where = " t1.fchCreacion BETWEEN '$minimo' AND '$maximo'";
      } else if ($maximo != "") $where = " t1.fchCreacion  <= '$maximo'";

      
    $joinQuery = " FROM (SELECT cmb.idPlaca, substring(cmb.fchCreacion,1,7) AS anhioMes, cmb.fchCreacion, kmActual, recorrido, nroVale, precioGalon, cmb.total, round(total/recorrido,3) AS eficSoles, galones, grifo, dniConductor, producto, autoriza, veh.estado, veh.rznSocial, veh.m3Facturable FROM combustible AS cmb, vehiculo AS veh WHERE cmb.idPlaca = veh.idPlaca ) AS t1 ";

      $columns = array(
        array('db' => 't1.anhioMes', 'dt' => $dt++,  'field' => 'anhioMes'),
        array('db' => 't1.fchCreacion', 'dt' => $dt++,  'field' => 'fchCreacion'),
        array('db' => 't1.idPlaca', 'dt' => $dt++,  'field' => 'idPlaca'),
        /*array('db' => 't1.estado', 'dt' => $dt++,  'field' => 'estado'),
        array('db' => 't1.clasificacion', 'dt' => $dt++,  'field' => 'clasificacion'),
        array('db' => 't1.tercero', 'dt' => $dt++,  'field' => 'tercero'),
        array('db' => 't1.totalOC', 'dt' =>$dt++,  'field' => 'totalOC'), 
        array('db' => 't1.detraccion', 'dt' =>$dt++,  'field' => 'detraccion'),
        array('db' => 't1.pagLiq', 'dt' =>$dt++,  'field' => 'pagLiq'),
        array('db' => 't1.docPagoTercero', 'dt' =>$dt++,  'field' => 'docPagoTercero',
          'formatter' => function( $d, $row ) {
              //  return $d;
            return "<img border='0' src='imagenes/lapiz.png' class = 'editar' width='14' height='14' align='left' guiaPorte=$d><img border='0' src='imagenes/menos.png' class = 'eliminar' width='14' height='14' align='left' guiaPorte=$d>";
        }),
        */       
      );
    break;
  }


case 'repoVehiculoEficSolesKm':{
    $_SESSION['fecha'] = Date("Y-m-d");

    // DB table to use
    $table = 'vehiculoeficsoleskm';

    // Table's primary key
    $primaryKey = 'id';
    $where = " 1 ";

    $joinQuery = " FROM (SELECT id, tipoCombust, nombre, descripcion, valorEsperado, usuario, fchCreacion FROM vehiculoeficsoleskm) AS eficisk";

    $dt = 0;
    $columns = array(
      array('db' => 'eficisk.id', 'dt' => $dt++, 'field' => 'id'),
      array('db' => 'eficisk.tipoCombust', 'dt' => $dt++, 'field' => 'tipoCombust'),
      array('db' => 'eficisk.nombre', 'dt' => $dt++, 'field' => 'nombre'),
      array('db' => 'eficisk.descripcion', 'dt' => $dt++, 'field' => 'descripcion'),
      array('db' => 'eficisk.valorEsperado', 'dt' => $dt++, 'field' => 'valorEsperado'),
      array('db' => 'eficisk.usuario', 'dt' => $dt++, 'field' => 'usuario'),
      array('db' => 'eficisk.fchCreacion', 'dt' => $dt++, 'field' => 'fchCreacion'),
      
      array('db' => 'eficisk.id', 'dt' => $dt++,'field' => 'id', 'formatter' => function( $d, $row ) {
        return "<img border='0' src='imagenes/lapiz.png' class = 'editar' width='18' height='18' align='left'><img border='0' src='imagenes/menos.png' class = 'eliminar' width='18' height='18' align='left'>";
      }),
    );

  break;
}



  case 'planillasCronograma':
    // DB table to use
    $table = 'quincena';

    // Table's primary key
    $primaryKey = 'quincena';

    $joinQuery = "";
    $where = NULL;


    // Array of database columns which should be read and sent back to DataTables.
    // The `db` parameter represents the column name in the database, while the `dt`
    // parameter represents the DataTables column identifier. In this case simple
    // indexes

    $dt = 0;

    $columns = array(
      array('db' => 'quincena', 'dt' => $dt++),
      array('db' => 'estadoQuincena', 'dt' => $dt++),
      array('db' => 'quinFchIni', 'dt' => $dt++),
      array('db' => 'quinFchFin', 'dt' => $dt++),
      array('db' => 'usuario', 'dt' => $dt++),
      array('db' => 'fchCreacion', 'dt' => $dt++), 
      array('db' => 'ultCambioUsuario', 'dt' => $dt++), 
      //array('db' => '', 'dt' => $dt++),

      array('db' => 'quincena', 'dt' => $dt++, 'formatter' => function( $d, $row ) {
        $cadena = "";
        if($row["estadoQuincena"] == "Siguiente" || $row["estadoQuincena"] == "Md" ){
          //$cadena .= "<img border='0' src='imagenes/lapiz.png' class = 'editar' quincena = '".$d."'  width='15' height='15' align='left'>";
          $cadena .= "<img border='0' quincena = '".$d."'  estado = '".$row["estadoQuincena"]."' src='imagenes/planillaTranspAuto.png' class = 'planillaAuto' width='15' height='15' align='left'>";


          if($row["estadoQuincena"] == "Md"){
            $cadena .= "<img border='0' quincena = '".$d."'  estado = '".$row["estadoQuincena"]."' src='imagenes/verFotos.png' class = 'verConsolidado' width='15' height='15' align='left'>";
          }  
        }
        if($row["estadoQuincena"] == "Cerrado" || $row["estadoQuincena"] == "Md" ){
          $cadena .= "<img border='0' quincena = '".$d."'  estado = '".$row["estadoQuincena"]."' src='imagenes/planillaTranspPago.png' class = 'planillaPagos' width='15' height='15' align='left'><img border='0' quincena = '".$d."'  estado = '".$row["estadoQuincena"]."' src='imagenes/planillaTranspConta.png' class = 'planillaContab' width='15' height='15' align='left'>";
        }
        return $cadena;
      }),

    );
    break;


  case 'listarPolizas':
    // DB table to use
    $table = 'polizas';

    // Table's primary key
    $primaryKey = 'idPoliza';

    $joinQuery = "";
    $where = NULL;


    // Array of database columns which should be read and sent back to DataTables.
    // The `db` parameter represents the column name in the database, while the `dt`
    // parameter represents the DataTables column identifier. In this case simple
    // indexes

    $dt = 0;

    $columns = array(
      array('db' => 'idPoliza', 'dt' => $dt++),
      array('db' => 'nombPoliza', 'dt' => $dt++),
      array('db' => 'valorPoliza', 'dt' => $dt++),
      array('db' => 'estadoPoliza', 'dt' => $dt++),
      array('db' => 'descripPoliza', 'dt' => $dt++),
      array('db' => 'creacUsuario', 'dt' => $dt++), 
      array('db' => 'creacFch', 'dt' => $dt++), 
      array('db' => 'idPoliza', 'dt' => $dt++,'formatter' => function( $d, $row ) {
        //$fecha = $_SESSION['fecha'];
        $cadena = "";
        if ($_GET["edicion"] == "Si"){
          $cadena .= "<img src='imagenes/lapiz.png' class = 'editaPoliza' width='15' height='15' idPoliza = '$d' >";
        }
        if ($_GET["admin"] == "Si"){
          $cadena .= "<img  src='imagenes/menos.png' class = 'eliminaPoliza' width='15' height='15' idPoliza = '$d' >";        
        }
        return $cadena;
      } ),
    );
    break;

  case 'listarCtasPendTrabaj':
    // DB table to use
    $table = 'trabajador';

    // Table's primary key
    $primaryKey = 'idTrabajador';

    $joinQuery = "";
    $where = NULL;
    $dt = 0;


    if (!empty($_GET['columns'][7]['search']['value'])){
      $where = " estadoTrabajador = '".$_GET['columns'][7]['search']['value']."'";
    };

    $columns = array(
      array('db' => 'idTrabajador', 'dt' => $dt++),
      array('db' => 'tipoDocTrab', 'dt' => $dt++),
      array('db' => 'apPaterno', 'dt' => $dt++),
      array('db' => 'apMaterno', 'dt' => $dt++),
      array('db' => 'nombres', 'dt' => $dt++),
      array('db' => 'categTrabajador', 'dt' => $dt++),
      array('db' => 'tipoTrabajador', 'dt' => $dt++),
      array('db' => 'estadoTrabajador', 'dt' => $dt++),
      array('db' => 'remuneracionBasica', 'dt' => $dt++, 'formatter' => create_function('$d,$row', 'return number_format($d,"2", ".", ",");')),
      array('db' => 'asignacionFamiliar', 'dt' => $dt++, 'formatter' => create_function('$d,$row', 'return number_format($d,"2", ".", ",");')),
      array('db' => 'idTrabajador', 'dt' => $dt++,'formatter' => function( $d, $row ) {
        $cadena = "";
        
        return $cadena;
      } ),

    );
    break;

  case 'listarCtasPendDespTrab':
    // DB table to use
    $table = 'despachopersonal';

    // Table's primary key
    $primaryKey = 'idTrabajador';

    $joinQuery = "";
    $where = NULL;
    $dt = 0;

    $joinQuery = " FROM ( SELECT idTrabajador, des.fchDespacho, des.correlativo, tipoRol, fchPago, valorRol, valorAdicional, valorHraExtra, desper.pagado, cli.nombre AS nombCliente, placa, cuenta, concluido FROM despacho AS des, `despachopersonal` AS desper, cliente AS cli WHERE des.fchDespacho = desper.fchDespacho AND des.correlativo = desper.correlativo AND des.idCliente = cli.idRuc  AND desper.pagado != 'Si' ) AS desper ";


    if (!empty($_GET['idTrabajador'])){
      $idTrabajador = $_GET["idTrabajador"];
      //$where = " DATEDIFF(now(), fchDespacho) < 100 AND pagado != 'Si' AND idTrabajador = '".$_GET['idTRabajador']."'";
      $where = " idTrabajador = '".$_GET['idTrabajador']."'";
    };

    $columns = array(
      array('db' => 'desper.idTrabajador', 'dt' => $dt++, 'field' => 'idTrabajador'),
      array('db' => 'desper.fchDespacho', 'dt' => $dt++, 'field' => 'fchDespacho'),
      array('db' => 'desper.correlativo', 'dt' => $dt++, 'field' => 'correlativo'),
      array('db' => 'desper.concluido', 'dt' => $dt++, 'field' => 'concluido'),
      array('db' => 'desper.tipoRol', 'dt' => $dt++, 'field' => 'tipoRol'),
      array('db' => 'desper.fchPago', 'dt' => $dt++, 'field' => 'fchPago'),
      array('db' => 'desper.valorRol', 'dt' => $dt++, 'field' => 'valorRol', 'formatter' => create_function('$d,$row', 'return number_format($d,"2", ".", ",");')),
      array('db' => 'desper.valorAdicional', 'dt' => $dt++, 'field' => 'valorAdicional', 'formatter' => create_function('$d,$row', 'return number_format($d,"2", ".", ",");')),
      array('db' => 'desper.valorHraExtra', 'dt' => $dt++, 'field' => 'valorHraExtra', 'formatter' => create_function('$d,$row', 'return number_format($d,"2", ".", ",");')),

      array('db' => 'desper.nombCliente', 'dt' => $dt++, 'field' => 'nombCliente'),
      array('db' => 'desper.placa', 'dt' => $dt++, 'field' => 'placa'),
      array('db' => 'desper.cuenta', 'dt' => $dt++, 'field' => 'cuenta'),
      array('db' => 'desper.fchDespacho', 'dt' => $dt++, 'field' => 'fchDespacho','formatter' => function( $d, $row ) {
        $cadena = "";
        //if ($_GET["edicion"] == "Si"){
        if($row["concluido"] == "Si" ){
          $cadena .= "<img src='imagenes/calendarioTransp.png' class = 'editaDespFchPago' width='15' height='15' fchDespacho = '$d' correlativo = '".$row["correlativo"]."' fchPago = '".$row["fchPago"]."' tipoRol = '".$row["tipoRol"]."'  idTrabajador = '".$row["idTrabajador"]."'   >";
        }
        //}
        
        return $cadena;
      } ),
      

    );
    break;

  case 'listarCtasPendOcurrTrab':
    // DB table to use
    $table = 'ocurrenciaconsulta';

    // Table's primary key
    $primaryKey = 'idTrabajador';

    $joinQuery = "";
    $where = NULL;
    $dt = 0;

    //$joinQuery = " FROM ( SELECT `fchDespacho`, `correlativo`, `tipoOcurrencia`, `descripcion`, `montoTotal`, `nroCuota`, `subTipoOcurrencia`, `nroCuotas`, `idTrabajador`, `fchPago`, `tipo`, `montoCuota`, `fchDesmarca`, `pagado` FROM `ocurrenciaconsulta` WHERE DATEDIFF(now(), fchPago) < 100 AND pagado != 'Si' ) AS ocurr ";


    if (!empty($_GET['idTrabajador'])){
      $idTrabajador = $_GET["idTrabajador"];
      //$where = " DATEDIFF(now(), fchDespacho) < 100 AND pagado != 'Si' AND idTrabajador = '".$_GET['idTRabajador']."'";
      $where = " pagado != 'Si' AND idTrabajador = '".$_GET['idTrabajador']."'";
    };

    $columns = array(
      array('db' => 'idTrabajador', 'dt' => $dt++),
      array('db' => 'fchDespacho', 'dt' => $dt++),
      array('db' => 'correlativo', 'dt' => $dt++),
      array('db' => 'descripcion', 'dt' => $dt++),
      array('db' => 'tipoOcurrencia', 'dt' => $dt++),
      array('db' => 'subTipoOcurrencia', 'dt' => $dt++),
      array('db' => 'fchPago', 'dt' => $dt++),
      array('db' => 'pagado', 'dt' => $dt++),
      array('db' => 'montoTotal', 'dt' => $dt++, 'formatter' => create_function('$d,$row', 'return number_format($d,"2", ".", ",");')),
      array('db' => 'nroCuotas', 'dt' => $dt++),
      array('db' => 'nroCuota', 'dt' => $dt++),
      array('db' => 'montoCuota', 'dt' => $dt++, 'formatter' => create_function('$d,$row', 'return number_format($d,"2", ".", ",");')),
      array('db' => 'codOcurrencia', 'dt' => $dt++),
      array('db' => 'fchDespacho', 'dt' => $dt++, 'field' => 'fchDespacho','formatter' => function( $d, $row ) {
        $cadena = "";
        //if ($_GET["edicion"] == "Si"){
          $cadena .= "<img src='imagenes/calendarioTransp.png' class = 'editaOcurFchPago' width='15' height='15' fchDespacho = '$d' correlativo = '".$row["correlativo"]."' nroCuota = '".$row["nroCuota"]."'  idTrabajador = '".$row["idTrabajador"]."' codOcurrencia = '".$row["codOcurrencia"]."' fchPago = '".$row["fchPago"]."'  >  &nbsp;";

          $cadena .= "<img src='imagenes/menos.png' class = 'eliminaOcurrencia' width='15' height='15' fchDespacho = '$d' correlativo = '".$row["correlativo"]."' nroCuota = '".$row["nroCuota"]."'  idTrabajador = '".$row["idTrabajador"]."' codOcurrencia = '".$row["codOcurrencia"]."' fchPago = '".$row["fchPago"]."'  >";
        //}
        
        return $cadena;
      } ),

    );
    break;

    //
  case 'listarCtasPendDsctsTrab':
    // DB table to use
    $table = 'prestamodetalle';

    // Table's primary key
    $primaryKey = 'idTrabajador';

    $joinQuery = "";
    $where = NULL;
    $dt = 0;

    $joinQuery = " FROM ( SELECT presdet.idTrabajador, presdet.fchCreacion, presdet.descripcion, presdet.tipoItem, presdet.subTipoItem, presdet.fchPago, presdet.pagado, presdet.monto, nroCuota, montoCuota, pres.nroCuotas FROM prestamo AS pres, prestamodetalle AS presdet WHERE pres.idTrabajador = presdet.idTrabajador AND pres.descripcion = presdet.descripcion AND pres.tipoItem = presdet.tipoItem AND pres.monto = presdet.monto AND pres.fchCreacion = presdet.fchCreacion ) AS t1  ";

    if (!empty($_GET['idTrabajador'])){
      $idTrabajador = $_GET["idTrabajador"];
      //$where = " DATEDIFF(now(), fchDespacho) < 100 AND pagado != 'Si' AND idTrabajador = '".$_GET['idTRabajador']."'";
      $where = " pagado != 'Si' AND tipoItem IN ('Ajuste', 'Bono', 'Compra', 'DomFeriado', 'DsctoTrabaj', 'DevolGarantia', 'FondoGarantia', 'Movilidad', 'Reembolso', 'ServAdicional', 'DescMedico', 'LicGoce', 'Inasistencia', 'AjusteVacac', 'Adelanto', 'Prestamo')AND idTrabajador = '".$_GET['idTrabajador']."'";
    };

    $columns = array(
      array('db' => 't1.idTrabajador', 'dt' => $dt++, 'field' => 'idTrabajador'),
      array('db' => 't1.fchCreacion', 'dt' => $dt++, 'field' => 'fchCreacion'),
      array('db' => 't1.descripcion', 'dt' => $dt++, 'field' => 'descripcion'),
      array('db' => 't1.tipoItem', 'dt' => $dt++, 'field' => 'tipoItem'),

      array('db' => 't1.subTipoItem', 'dt' => $dt++, 'field' => 'subTipoItem'),
      array('db' => 't1.fchPago', 'dt' => $dt++, 'field' => 'fchPago'),
      array('db' => 't1.pagado', 'dt' => $dt++, 'field' => 'pagado'),
      array('db' => 't1.monto', 'dt' => $dt++, 'field' => 'monto', 'formatter' => create_function('$d,$row', 'return number_format($d,"2", ".", ",");')),

      array('db' => 't1.nroCuotas', 'dt' => $dt++, 'field' => 'nroCuotas'),
      array('db' => 't1.nroCuota', 'dt' => $dt++, 'field' => 'nroCuota'),
      array('db' => 't1.montoCuota', 'dt' => $dt++, 'field' => 'montoCuota', 'formatter' => create_function('$d,$row', 'return number_format($d,"2", ".", ",");')),
      array('db' => 't1.fchPago', 'dt' => $dt++, 'field' => 'fchPago','formatter' => function( $d, $row ) {
        $cadena = "";
        //if ($_GET["edicion"] == "Si"){
        $cadena .= "<img src='imagenes/calendarioTransp.png' class = 'editaDescFchPago' width='15' height='15' fchPago = '$d'  monto = '".$row["monto"]."'  nroCuota = '".$row["nroCuota"]."' idTrab = '".$row["idTrabajador"]."'  descrip = '".$row["descripcion"]."'  tipoItem = '".$row["tipoItem"]."' fchCreac =  '".$row["fchCreacion"]."' >  &nbsp;";
        //}
        $cadena .= "<img src='imagenes/menos.png' class = 'eliminaDescuento' width='15' height='15' fchPago = '$d'  monto = '".$row["monto"]."' idTrab = '".$row["idTrabajador"]."'  descrip = '".$row["descripcion"]."'  tipoItem = '".$row["tipoItem"]."' fchCreac =  '".$row["fchCreacion"]."' >";
        
        return $cadena;
      } ),

      
    );
    break;

}

switch ($opcion) {

    case 'listarOrdenCompra':
    case 'repoEficVehiculo':
    case 'repoVehiculoEficSolesKm':
    case 'planillasCronograma':
    case 'listarPolizas':
    case 'listarCtasPendTrabaj':
    case 'listarCtasPendDespTrab':
    case 'listarCtasPendOcurrTrab':
    case 'listarCtasPendDsctsTrab':

    {
        echo json_encode(
              SSP2::simple($_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery,$where)
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