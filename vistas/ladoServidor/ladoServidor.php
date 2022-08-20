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
  case 'movFacturas':
  // DB table to use
    $table = 'doccobranza';

    // Table's primary key
    $primaryKey = 'docCobranza';
    
    $where = " 1 ";
    $joinQuery = " FROM `doccobranza` docc LEFT JOIN doccobr_rel_ingbco doccrel ON docc.docCobranza = doccrel.docCobranza AND docc.tipoDoc = doccrel.tipoDoc LEFT JOIN doccobranzaingbanco doccbco ON doccrel.nroOperacion = doccbco.nroOperacion AND doccrel.banco = doccbco.banco ";

    // Array of database columns which should be read and sent back to DataTables.
    // The `db` parameter represents the column name in the database, while the `dt`
    // parameter represents the DataTables column identifier. In this case simple
    // indexes

    $columns = array(
        array('db' => 'docc.docCobranza', 'dt' => 0, 'field' => 'docCobranza'),
        array('db' => 'docc.tipoDoc', 'dt' => 1, 'field' => 'tipoDoc'),
        array('db' => 'docc.estado', 'dt' => 2, 'field' => 'estado'),
        array('db' => 'docc.nombCliente', 'dt' => 3, 'field' => 'nombCliente'),
        array('db' => 'docc.fchEmitida', 'dt' => 4, 'field' => 'fchEmitida'),
        array('db' => 'docc.fchPresentada', 'dt' => 5, 'field' => 'fchPresentada'),
        array('db' => 'docc.fchCancelada', 'dt' => 6, 'field' => 'fchCancelada'),
        array('db' => 'docc.detraccion', 'dt' => 7, 'field' => 'detraccion'),
        array('db' => 'docc.fchDetraccion', 'dt' => 8, 'field' => 'fchDetraccion'),
        array('db' => 'doccrel.nroOperacion', 'dt' => 9, 'field' => 'nroOperacion'),
        array('db' => 'doccrel.banco', 'dt' => 10, 'field' => 'banco'),
        array('db' => 'doccrel.montoPagado', 'dt' => 11, 'field' => 'montoPagado', 'formatter' => create_function('$d,$row', 'return number_format($d,"2", ".", ",");')),
        array('db' => 'docc.cobIdCliente', 'dt' => 12,  'field' => 'cobIdCliente',
            'formatter' => function( $d, $row ) {
            return "<img border='0' src='imagenes/lapiz.png' width='18' height='18' align='right' onclick =  \"abrirventanaflotantesupergrande('index.php?controlador=clientes&accion=registrarIngresoBanco2&id=$d&token=1nv')\">";
        }),
       
    );
    break;

    case 'listarVehiculos':
      // DB table to use
      $table = 'vehiculo';

      // Table's primary key
      $primaryKey = 'idPlaca';
        
      $where = " 1 ";
      $joinQuery = " FROM vehiculo veh left join vehiculodueno vehdue ON  veh.rznSocial = vehdue.documento ";

      // Array of database columns which should be read and sent back to DataTables.
      // The `db` parameter represents the column name in the database, while the `dt`
      // parameter represents the DataTables column identifier. In this case simple
      // indexes

      $columns = array(
        array('db' => 'veh.nroVehiculo', 'dt' => 0, 'field' => 'nroVehiculo'),
        array('db' => 'veh.idPlaca', 'dt' => 1, 'field' => 'idPlaca'),
        array('db' => 'veh.propietario', 'dt' => 2, 'field' => 'propietario'),
        array('db' => 'veh.estado', 'dt' => 3, 'field' => 'estado'),
        array('db' => 'veh.rznSocial', 'dt' => 4, 'field' => 'rznSocial'),
        array('db' => 'vehdue.nombreCompleto', 'dt' => 5, 'field' => 'nombreCompleto'),
        array('db' => 'veh.pesoUtil', 'dt' => 6, 'field' => 'pesoUtil'),
        array('db' => 'veh.pesoUtilReal', 'dt' => 7, 'field' => 'pesoUtilReal'),
        array('db' => 'veh.m3Facturable', 'dt' => 8, 'field' => 'm3Facturable'),
        array('db' => 'veh.usuario', 'dt' => 9, 'field' => 'usuario'),
        array('db' => 'veh.fchCreacion', 'dt' => 10, 'field' => 'fchCreacion'),
        array('db' => 'veh.nroVehiculo', 'dt' => 11,  'field' => 'nroVehiculo',
            'formatter' => function( $d, $row ) {
              //  return $d;
            return "<img border='0' src='imagenes/lapiz.png' width='18' height='18' align='left' onclick =  \"abrirventanaflotantesupergrande('index.php?controlador=clientes&accion=registrarIngresoBanco2&id=$d&token=1nv')\">";
        }),
           
      );
        break;         

    case 'cargaCc': {

            // DB table to use
            $table = 'ccmovimencab';

            // Table's primary key
            $primaryKey = 'idMovimEncab';
            $where = " ccm.tipoEncab like 'Ccto' ";
            $joinQuery = "  FROM `ccmovimencab` ccm LEFT JOIN ccproveedor ccp ON ccm.idProveedor = ccp.idProveedor ";

            $columns = array(
                array('db' => 'ccm.idMovimEncab', 'dt' => 0, 'field' => 'idMovimEncab'),
                array('db' => 'ccm.idProveedor', 'dt' => 1, 'field' => 'idProveedor'),
                array('db' => 'ccp.nombProveedor', 'dt' => 2, 'field' => 'nombProveedor'),
                array('db' => 'ccm.tipoDoc', 'dt' => 3, 'field' => 'tipoDoc'),
                array('db' => 'ccm.nroDoc', 'dt' => 4, 'field' => 'nroDoc'),
                array('db' => 'ccm.fchEvento', 'dt' => 5, 'field' => 'fchEvento'),
                array('db' => 'ccm.tipoMoneda', 'dt' => 6, 'field' => 'tipoMoneda'),
                array('db' => 'ccm.monto', 'dt' => 7, 'field' => 'monto', 'formatter' => create_function('$d,$row', 'return number_format($d,"2", ".", ",");')),
                array('db' => 'ccm.idMovimEncab', 'dt' => 8, 'field' => 'idMovimEncab',
                'formatter' => function( $d, $row ) {
                  //return $row['tipoDoc'];
                return "<img border='0' src='imagenes/lapiz.png' width='13' height='13' align='left' onclick =  \"abrirventanaflotantesupergrande('index.php?controlador=cc&accion=nuevaCargaCc&id=$d&nroDoc=".$row['nroDoc']."&nombProveedor=".$row['nombProveedor']."&fchEvento=".$row['fchEvento']."')\"><img border='0' src='imagenes/menos.png' width='13' height='13' align='left' onclick =  \"abrirventanaflotante('index.php?controlador=cc&accion=eliminaCargaCc&id=$d&token=1nv')\">";
                }),
              
            );

            break;
        }

    case 'listarGrupoItem': {

        // DB table to use
        $table = 'articulogrupoitem';

        // Table's primary key
        $primaryKey = 'idTipoItem';

        // Array of database columns which should be read and sent back to DataTables.
        // The `db` parameter represents the column name in the database, while the `dt`
        // parameter represents the DataTables column identifier. In this case simple
        // indexes

        $columns = array(
            array('db' => 'idTipoItem', 'dt' => 0),
            array('db' => 'tipoItem', 'dt' => 1),
            array('db' => 'nombre', 'dt' => 2),
            array('db' => 'descripcion', 'dt' => 3),
            array('db' => 'idTipoItem', 'dt' => 4,
                'formatter' => function( $d, $row ) {
                  //  return $d;
                return "<img border='0' src='imagenes/lapiz.png' width='13' height='13' align='left' onclick =  \"abrirventanaflotante('index.php?controlador=cc&accion=editaGrupoItem&id=$d&token=1nv')\"><img border='0' src='imagenes/menos.png' width='13' height='13' align='left' onclick =  \"abrirventanaflotante('index.php?controlador=cc&accion=eliminaGrupoItem&id=$d&token=1nv')\">";
            }),
          
        );
        break;
      }

      case 'listarTipoArticulo': {

        // DB table to use
        $table = 'articulotipo';

        // Table's primary key
        $primaryKey = 'idTipoArticulo';

        $where = " 1 ";
        $joinQuery = "  FROM `articulotipo` art LEFT JOIN articulogrupoitem agi ON agi.idTipoItem = substring(art.idTipoArticulo,1,3) LEFT JOIN articulotipoexist ate ON ate.idArtExist = art.idArtExist  ";

        $columns = array(
            array('db' => 'art.idTipoArticulo', 'dt' => 0, 'field' => 'idTipoArticulo'),
            array('db' => 'agi.nombre', 'dt' => 1, 'field' => 'nombre'),
            array('db' => 'art.nombreArt', 'dt' => 2, 'field' => 'nombreArt'),
            array('db' => 'ate.descripExist', 'dt' => 3, 'field' => 'descripExist'),
            array('db' => 'art.descripcion', 'dt' => 4, 'field' => 'descripcion'),
            array('db' => 'art.idTipoArticulo', 'dt' => 5, 'field' => 'idTipoArticulo',
                'formatter' => function( $d, $row ) {
                  //  return $d;
                return "<img border='0' src='imagenes/lapiz.png' width='13' height='13' align='left' onclick =  \"abrirventanaflotante('index.php?controlador=cc&accion=editaTipoArticulo&id=$d&token=1nv')\"><img border='0' src='imagenes/menos.png' width='13' height='13' align='left' onclick =  \"abrirventanaflotante('index.php?controlador=cc&accion=eliminaTipoArticulo&id=$d&token=1nv')\">";
            }),
          
        );

        break;
      }

      case 'listarArticulo': {
        // DB table to use
        $table = 'articulo';
        // Table's primary key
        $primaryKey = 'idArt';

        // Array of database columns which should be read and sent back to DataTables.
        // The `db` parameter represents the column name in the database, while the `dt`
        // parameter represents the DataTables column identifier. In this case simple
        // indexes
        $columns = array(
            array('db' => 'idArt', 'dt' => 0, 'field' => 'idArt'),
            array('db' => 'descripcion', 'dt' => 1, 'field' => 'descripcion'),
            array('db' => 'auxSubgrupo', 'dt' => 2, 'field' => 'auxSubgrupo'),
            array('db' => 'ctoUnitario', 'dt' => 3, 'field' => 'ctoUnitario', 'formatter' => create_function('$d,$row', 'return number_format($d,"2", ".", ",");')),
            array('db' => 'cantidad', 'dt' => 4, 'field' => 'cantidad'),
            array('db' => 'uniMedida', 'dt' => 5, 'field' => 'uniMedida'),
            array('db' => 'tipoExist', 'dt' => 6, 'field' => 'tipoExist'),
            array('db' => 'idArt', 'dt' => 7, 'field' => 'idArt',
                'formatter' => function( $d, $row ) {
                  //  return $d;
                return "<img border='0' src='imagenes/lapiz.png' width='13' height='13' align='left' onclick =  \"abrirventanaflotante('index.php?controlador=articulos&accion=editaArticulo&idArt=$d&token=1nv')\">
                <img border='0' src='imagenes/almacenPeque.png' width='13' height='13' align='left' onclick =  \"abrirventana('index.php?controlador=articulos&accion=verKardex&idArt=$d&token=1nv')\">
                <img border='0' src='imagenes/menos.png' width='13' height='13' align='left' onclick =  \"abrirventanaflotante('index.php?controlador=articulos&accion=eliminaArticulo&idArt=$d&token=1nv')\">";
            }),
          
        );

        break;
      }

      case 'listarDetallesCobrar': {

        // DB table to use
        $table = 'despachodetallesporcobrar';

        // Table's primary key
        $primaryKey = 'fchDespacho';

        $where = NULL;
        $minimo = $_GET['min'];
        $maximo = $_GET['max'];
        if ($minimo <> ""){
          if ($maximo == "") $where = " despCo.fchDespacho >= '$minimo' ";
          else $where = " despCo.fchDespacho BETWEEN '$minimo' AND '$maximo'";
        } else if ($maximo != "") $where = " despCo.fchDespacho  <= '$maximo'";  //.$_GET['max'];


        $joinQuery = "  FROM `despachodetallesporcobrar` despCo LEFT JOIN despacho desp ON desp.fchDespacho = despCo.fchDespacho AND desp.correlativo = despCo.correlativo LEFT JOIN cliente cli ON desp.idCliente = cli.idRuc ";

        $columns = array(
            array('db' => 'despCo.fchDespacho', 'dt' => 0, 'field' => 'fchDespacho'),
            array('db' => 'despCo.correlativo', 'dt' => 1, 'field' => 'correlativo'),
            array('db' => 'cli.nombre', 'dt' => 2, 'field' => 'nombre'),
            array('db' => 'desp.cuenta', 'dt' => 3, 'field' => 'cuenta'),
            array('db' => 'despCo.docCobranza', 'dt' =>4, 'field' => 'docCobranza'),
            array('db' => 'despCo.tipoDoc', 'dt' =>5, 'field' => 'tipoDoc'),
            array('db' => 'despCo.codigo', 'dt' => 6, 'field' => 'codigo'),
            array('db' => 'despCo.costoUnit',
                  'dt' => 7,
                  'field' => 'costoUnit',
                  'formatter' => create_function('$d,$row', 'return number_format($d,"2", ".", ",");')
                ),

            array('db' => 'despCo.cantidad', 'dt' => 8, 'field' => 'cantidad'),
            array('db' => 'despCo.observDetallePorCobrar', 'dt' => 9, 'field' => 'observDetallePorCobrar'),
        
        );

        break;
      }

    case 'listarQuinOtrosConceptos': {

        // DB table to use
        $table = 'quincenadetotrosconcep';

        // Table's primary key
        $primaryKey = 'idConcepto';

        $where = " 1 ";
        $joinQuery = "   FROM `quincenadetotrosconcep` qdoc LEFT JOIN trabajador tra ON `qdoc`.idTrabajador = tra.idTrabajador ";

        $columns = array(
            array('db' => 'qdoc.idConcepto', 'dt' => 0, 'field' => 'idConcepto'),
            array('db' => 'tra.idTrabajador', 'dt' => 1, 'field' => 'idTrabajador'),
            array('db' => 'tra.apPaterno', 'dt' => 2, 'field' => 'apPaterno'),
            array('db' => 'tra.apMaterno', 'dt' => 3, 'field' => 'apMaterno'),
            array('db' => 'tra.nombres', 'dt' => 4, 'field' => 'nombres'),
            array('db' => 'qdoc.idItem', 'dt' => 5, 'field' => 'idItem'),

            array('db' => 'qdoc.monto',
                  'dt' => 6,
                  'field' => 'monto',
                  'formatter' => create_function('$d,$row', 'return number_format($d,"2", ".", ",");')
                ),

            array('db' => 'qdoc.pagado', 'dt' => 7, 'field' => 'pagado'),
            array('db' => 'qdoc.fchPago', 'dt' => 8, 'field' => 'fchPago'),
            array('db' => 'qdoc.fchQuincena', 'dt' => 9, 'field' => 'fchQuincena'),
            array('db' => 'qdoc.observacion', 'dt' => 10, 'field' => 'observacion'),
            array('db' => 'qdoc.creacFch', 'dt' => 11, 'field' => 'creacFch'),
            array('db' => 'qdoc.creacUsuario', 'dt' => 12, 'field' => 'creacUsuario'),
            array('db' => 'qdoc.idConcepto', 'dt' => 13, 'field' => 'idConcepto',
                'formatter' => function( $d, $row ) {
                  //  return $d;
                return "<img border='0' src='imagenes/lapiz.png' width='13' height='13' align='left' onclick =  \"abrirventanaflotantemediana('index.php?controlador=planillas&accion=editaConcepto&id=$d&token=1nv')\"><img border='0' src='imagenes/menos.png' width='13' height='13' align='left' onclick =  \"abrirventanaflotante('index.php?controlador=planillas&accion=eliminaConcepto&id=$d&token=1nv')\">";
            }),
          
        );
        break;
      }

      case 'listarLiquidacionesTrabajador': {
        // DB table to use
        $table = 'liquidaciontrabajadores';

        // Table's primary key
        $primaryKey = 'idLiquidacion';

        $where = " 1 ";
        $joinQuery = "   FROM `liquidaciontrabajadores` lqtr LEFT JOIN trabajador tra ON `lqtr`.idTrabajador = tra.idTrabajador ";
        $columns = array(
            array('db' => 'lqtr.idLiquidacion', 'dt' => 0, 'field' => 'idLiquidacion'),
            array('db' => 'tra.idTrabajador', 'dt' => 1, 'field' => 'idTrabajador'),
            array('db' => 'tra.apPaterno', 'dt' => 2, 'field' => 'apPaterno'),
            array('db' => 'tra.apMaterno', 'dt' => 3, 'field' => 'apMaterno'),
            array('db' => 'tra.nombres', 'dt' => 4, 'field' => 'nombres'),
            array('db' => 'lqtr.fchLiquidacion', 'dt' => 5, 'field' => 'fchLiquidacion'),
            array('db' => 'lqtr.monto', 'dt' => 6, 'field' => 'monto'),
            array('db' => 'lqtr.mntFndDev', 'dt' => 7, 'field' => 'mntFndDev'),
            array('db' => 'lqtr.observacion', 'dt' => 8, 'field' => 'observacion'),

            array('db' => 'lqtr.creacFch', 'dt' => 9, 'field' => 'creacFch'),
            array('db' => 'lqtr.creacUsuario', 'dt' => 10, 'field' => 'creacUsuario'),
            array('db' => 'lqtr.idLiquidacion', 'dt' => 11, 'field' => 'idLiquidacion',
                'formatter' => function( $d, $row ) {
                  //  return $d;
                return "<img border='0' src='imagenes/lapiz.png' width='13' height='13' align='left' onclick =  \"abrirventanaflotantesupergrande2('index.php?controlador=planillas&accion=editaLiquidTrabajador&id=$d&token=1nv')\"><img border='0' src='imagenes/menos.png' width='13' height='13' align='left' onclick =  \"abrirventanaflotante('index.php?controlador=planillas&accion=eliminaLiquidacion&id=$d&token=1nv')\">";
            }),
          
        );

        break;
      }

      case 'listarOcurrenciaTerceroTipos': {

        // DB table to use
        $table = 'ocurrenciatercerotipos';

        // Table's primary key
        $primaryKey = 'tipoOcurrencia';

        $columns = array(
            array('db' => 'tipoOcurrencia', 'dt' => 0),
            array('db' => 'tipoConcepto', 'dt' => 1),
            array('db' => 'descripcion', 'dt' => 2),
            array('db' => 'estadoOcurrencia', 'dt' => 3),
            array('db' => 'creacUsuario', 'dt' => 4),
            array('db' => 'creacFch', 'dt' => 5),
            array('db' => 'tipoOcurrencia', 'dt' => 6,
            'formatter' => function( $d, $row ) {
            if ($row[3] == 'Permanente'){
              return "";
            } else {
              return "<img border='0' src='imagenes/lapiz.png' width='13' height='13' align='left' onclick =  \"abrirventanaflotantemediana('index.php?controlador=terceros&accion=editaTipoOcurrenciaTercero&id=$d&token=1nv')\"><img border='0' src='imagenes/menos.png' width='13' height='13' align='left' onclick =  \"abrirventanaflotante('index.php?controlador=terceros&accion=eliminaTipoOcurrenciaTercero&id=$d&token=1nv')\">"; 
            }   
            }),
        );
        break;
      }

      case 'despachoPuntos': {
        // DB table to use
        $table = 'despachopuntos';
        // Table's primary key
        $primaryKey = 'idProgramacion';
        $where = NULL;
        //$groupBy = NULL;
        if(isset($_GET["idProgramacion"])){
          $idProgramacion = $_GET['idProgramacion'];
          $whereJoin = " despun.idProgramacion = '$idProgramacion' ";
        } else {
          //Para mantener compatibilidad con desarrollo antiguo que no tenía idProgramacion
          $primaryKey = 'fchDespacho';
          $where = NULL;
          $fchDespacho = $_GET['fchDespacho'];
          $correlativo = $_GET['correlativo'];
          $whereJoin = " despun.fchDespacho = '$fchDespacho' AND despun.correlativo = '$correlativo' ";

        }
        //$joinQuery = " FROM despachopuntos despun LEFT JOIN despachopuntosproductos AS despunpro ON despun.idPunto = despunpro.idPunto ";

        $joinQuery = " FROM (SELECT `despun`.idPunto, `despun`.ordenPunto, `despun`.tipoPunto, `despun`.nombComprador, `despun`.distrito, `despun`.provincia, `despun`.direccion, `despun`.guiaCliente, `despun`.idCarga, `despun`.guiaMoy, `despun`.estado, `despun`.subEstado, `despun`.hraLlegada, `despun`.hraSalida, `despun`.observacion, `despun`.adic_01, `despun`.adic_02, `despun`.adic_03, `despun`.fchDespacho, `despun`.idProgramacion, GROUP_CONCAT(concat(`despunpro`.idProducto,':',`despunpro`.nombProducto,'->',`despunpro`.cantProducto ) SEPARATOR '<br>' ) AS infoProductos FROM despachopuntos despun LEFT JOIN despachopuntosproductos AS despunpro ON `despun`.idPunto = `despunpro`.idPunto WHERE $whereJoin GROUP BY `despun`.idPunto) AS despun"; 


        //$groupBy = " despun.idPunto ";
        $dt = 0;
        $columns = array(
            array('db' => 'despun.idPunto', 'dt' => $dt++,  'field' => 'idPunto'),
            array('db' => 'despun.ordenPunto', 'dt' => $dt++,  'field' => 'ordenPunto'),
            array('db' => 'despun.tipoPunto', 'dt' => $dt++, 'field' => 'tipoPunto'),
            array('db' => 'despun.nombComprador', 'dt' => $dt++, 'field' => 'nombComprador'),
            array('db' => 'despun.distrito', 'dt' => $dt++, 'field' => 'distrito'),
            array('db' => 'despun.provincia', 'dt' => $dt++, 'field' => 'provincia'),
            array('db' => 'despun.direccion', 'dt' => $dt++, 'field' => 'direccion'),
            array('db' => 'despun.guiaCliente', 'dt' => $dt++, 'field' => 'guiaCliente'),
            array('db' => 'despun.guiaMoy', 'dt' => $dt++, 'field' => 'guiaMoy'),
            array('db' => 'despun.idCarga', 'dt' => $dt++, 'field' => 'idCarga'),
            array('db' => 'despun.estado', 'dt' => $dt++, 'field' => 'estado'),
            array('db' => 'despun.subEstado', 'dt' => $dt++, 'field' => 'subEstado'),
            array('db' => 'despun.hraLlegada', 'dt' => $dt++, 'field' => 'hraLlegada'),
            array('db' => 'despun.hraSalida', 'dt' => $dt++, 'field' => 'hraSalida'),
            array('db' => 'despun.observacion', 'dt' => $dt++, 'field' => 'observacion'),
            array('db' => 'despun.infoProductos', 'dt' => $dt++, 'field' => 'infoProductos'),
            array('db' => 'despun.fchDespacho', 'dt' => $dt++, 'field' => 'fchDespacho'),
            array('db' => 'despun.idProgramacion', 'dt' => $dt++, 'field' => 'idProgramacion'),
            array('db' => 'despun.idPunto', 'dt' => $dt++,'field' => 'idPunto',
            'formatter' => function( $d, $row ) {
              //return "<span class = 'editar'>$d</span>";
                $cadena = "";
                $cadena .= "<img class = 'editar' border='0' src='imagenes/lapiz.png' width='13' height='13' align='left' idPunto =$d >";
                $cadena .= "<img class = 'eliminar' border='0' src='imagenes/menos.png' width='13' height='13' align='left' idPunto =  $d >";
                $cadena .= "<img class = 'verFotos' border='0' src='imagenes/verFotos.png' width='13' height='13' align='left' idPunto =  $d  idProgramacion = ".$row["idProgramacion"]."  >";
                //$cadena .= "<img class = 'verFotos' border='0' src='imagenes/verFotos.png' width='13' height='13' align='left' idPunto =  $d >";
                if ($row["estado"] == 'Pendiente'){
                  $cadena .= "<img class = 'traspasar' border='0' src='imagenes/intercambiar.png' width='15' height='15' align='left' idPunto = $d >";                    
                }
              return $cadena;
            }),
        );
        break;
      }

      case 'cargaCuentaPdtoOtros': {

        // DB table to use
        $table = 'clientecuentapdtootros';

        // Table's primary key
        $primaryKey = 'idProducto';

        $where = NULL;
        $idProducto = $_GET['idProducto'];
        $where = " clicue.idProducto = '$idProducto' ";
        $joinQuery = " FROM clientecuentapdtootros AS clicue ";

        $columns = array(
            array('db' => 'clicue.correlativo', 'dt' => 0, 'field' => 'correlativo'),
            array('db' => 'clicue.nombre', 'dt' => 1, 'field' => 'nombre'),
            array('db' => 'clicue.tipo', 'dt' => 2, 'field' => 'tipo'),
            array('db' => 'clicue.tolerancia', 'dt' => 3, 'field' => 'tolerancia'),
            array('db' => 'clicue.costoUnidad', 'dt' => 4, 'field' => 'costoUnidad'),
            array('db' => 'clicue.correlativo', 'dt' => 5,'field' => 'correlativo',
            'formatter' => function( $d, $row ) {
              //return "<span class = 'editar'>$d</span>";
              return "<span class = 'editar'><img border='0' src='imagenes/lapiz.png' width='13' height='13' align='left' ></span><span class = 'eliminar'><img border='0' src='imagenes/menos.png' width='13' height='13' align='left' ></span>";
            }),  
        );
        break;
      }


      case 'listarProgram':
      $_SESSION['usuario'] = $_GET['usuario'];
      $_SESSION['admin'] = $_GET['admin'];
      // DB table to use
      $table = 'programdespacho';

      // Table's primary key
      $primaryKey = 'id';

      $where = " 1 ";
      $joinQuery = " FROM (SELECT id, fchDespacho, concat(`programdespacho`.idCliente, '-', cliente.nombre ) As idCliente,  `programdespacho`.cuenta,  `programdespacho`.nombProducto, concat(`programdespacho`.correlCuenta, '|', `programdespacho`.idProducto, '|', `programdespacho`.cuenta, '|', `programdespacho`.nombProducto, '|', `programdespacho`.tipoCuenta, '|', `programdespacho`.valorServicio ) As correlCuenta, placa, hraInicioEsperada, movilAsignado, GROUP_CONCAT( if(tipoRol = 'Conductor', nombCompleto,NULL)) AS idConductor,  group_concat(if(tipoRol = 'Auxiliar',  nombCompleto, NULL )  SEPARATOR '\n') AS idAuxiliares, estadoProgram, `programdespacho`.creacUsuario FROM cliente, `programdespacho` LEFT JOIN (SELECT programdesppers.idProgram, tipoRol, concat( programdesppers.idTrabajador,'-', nombres,' ', apPaterno,' ', apMaterno ) AS nombCompleto FROM programdesppers, trabajador WHERE programdesppers.idTrabajador = trabajador.idTrabajador  ) AS tripulacion ON tripulacion.idProgram = programdespacho.id WHERE `programdespacho`.idCliente = cliente.idRuc GROUP BY id ) AS t1   ";

      $usuario = "";
      $dt = 0;

      $columns = array(
        array('db' => 't1.id', 'dt' => $dt++, 'field' => 'id'),
        array('db' => 't1.idCliente', 'dt' => $dt++, 'field' => 'idCliente'),
        array('db' => 't1.fchDespacho', 'dt' => $dt++, 'field' => 'fchDespacho'),
        array('db' => 't1.hraInicioEsperada', 'dt' => $dt++, 'field' => 'hraInicioEsperada'),
        array('db' => 't1.movilAsignado', 'dt' => $dt++, 'field' => 'movilAsignado'),
        array('db' => 't1.cuenta', 'dt' => $dt++, 'field' => 'cuenta'),
        array('db' => 't1.nombProducto', 'dt' => $dt++, 'field' => 'nombProducto'),
        array('db' => 't1.placa', 'dt' => $dt++, 'field' => 'placa'),
        array('db' => 't1.idConductor', 'dt' => $dt++, 'field' => 'idConductor'),
        array('db' => 't1.idAuxiliares', 'dt' => $dt++, 'field' => 'idAuxiliares'),
        array('db' => 't1.estadoProgram', 'dt' => $dt++, 'field' => 'estadoProgram'),
        array('db' => 't1.creacUsuario', 'dt' => $dt++, 'field' => 'creacUsuario'),
        array('db' => 't1.estadoProgram', 'dt' => $dt++,  'field' => 'estadoProgram',
            'formatter' => function( $d, $row ) {
          $arrAdmin = explode(",",$_SESSION['admin']);
          $usuarioMayusc = strtoupper($_SESSION['usuario']);
          if (in_array($usuarioMayusc, $arrAdmin))  $esAdmin = "Si"; else $esAdmin = "No";
          if ($d == 'Procesado') $cadena = "";
          else if (strtoupper($_SESSION['usuario']) == strtoupper($row['creacUsuario']) || $esAdmin == 'Si' ) $cadena = "<img border='0' src='imagenes/menos.png' class = 'eliminar' width='18' height='18' align='right'><img border='0' src='imagenes/lapiz.png' class = 'editar' width='18' height='18' align='right'>";
          else $cadena = "";
          return $cadena;
        }), 
      );
      break;


      case 'listarProveeVehiculo':
      // DB table to use
      $table = 'vehiculodueno';

      // Table's primary key
      $primaryKey = 'documento';

      // Array of database columns which should be read and sent back to DataTables.
      // The `db` parameter represents the column name in the database, while the `dt`
      // parameter represents the DataTables column identifier. In this case simple
      // indexes

      $dt = 0;

      $columns = array(
        array('db' => 'documento', 'dt' => $dt++),
        array('db' => 'tipoDocumento', 'dt' => $dt++),
        array('db' => 'nombreCompleto', 'dt' => $dt++),
        array('db' => 'nroTelefono', 'dt' => $dt++),
        array('db' => 'eMail', 'dt' => $dt++),
        array('db' => 'estadoTercero', 'dt' => $dt++),
        array('db' => 'clasificacion', 'dt' => $dt++),
        array('db' => 'usuario', 'dt' => $dt++),
        array('db' => 'fchCreacion', 'dt' => $dt++),
        array('db' => 'editaUsuario', 'dt' => $dt++),
        array('db' => 'editaFch', 'dt' => $dt++),
        array('db' => 'eMail', 'dt' =>$dt++,
          'formatter' => function( $d, $row ) {
              //  return $d;
            return "<img border='0' src='imagenes/lapiz.png' class = 'editar' width='18' height='18' align='left'><img border='0' src='imagenes/menos.png' class = 'eliminar' width='18' height='18' align='left'>";
        }), 
      );

      break;

      case 'listarTelefonos':
      // DB table to use
      $table = 'telefono';

      // Table's primary key
      $primaryKey = 'idNroTelefono';

      // Array of database columns which should be read and sent back to DataTables.
      // The `db` parameter represents the column name in the database, while the `dt`
      // parameter represents the DataTables column identifier. In this case simple
      // indexes

      $dt = 0;

      $columns = array(
        array('db' => 'idNroTelefono', 'dt' => $dt++),
        array('db' => 'considPropio', 'dt' => $dt++),
        array('db' => 'operador', 'dt' => $dt++),
        array('db' => 'plan', 'dt' => $dt++),
        array('db' => 'marca', 'dt' => $dt++),
        array('db' => 'modelo', 'dt' => $dt++),
        array('db' => 'imei', 'dt' => $dt++),
        array('db' => 'estado', 'dt' => $dt++),
        array('db' => 'conductor', 'dt' => $dt++),
        array('db' => 'unidad', 'dt' => $dt++),
        array('db' => 'fchEntrega', 'dt' => $dt++),
        array('db' => 'usuario', 'dt' => $dt++),
        array('db' => 'fchCreacion', 'dt' => $dt++),
        array('db' => 'usuarioUltimoCambio', 'dt' => $dt++),
        array('db' => 'fchUltimoCambio', 'dt' => $dt++),
        
        array('db' => 'idNroTelefono', 'dt' =>$dt++,
          'formatter' => function( $d, $row ) {
              //  return $d;
            return "<img border='0' src='imagenes/lapiz.png' class = 'editar' width='18' height='18' align='left'><img border='0' src='imagenes/menos.png' class = 'eliminar' width='18' height='18' align='left'>";
        }), 
      );

      break;


      case 'listarTrabajadores':
        $_SESSION['fecha'] = Date("Y-m-d");

        // DB table to use
        $table = 'trabajador';

        // Table's primary key
        $primaryKey = 'idTrabajador';
        $where = " 1 ";

        if (!empty($_GET['columns'][5]['search']['value'])){
          $where = " trab.estadoTrabajador = '".$_GET['columns'][5]['search']['value']."'";
        };

        $joinQuery = " FROM (SELECT idTrabajador, tipoDocTrab, apPaterno, apMaterno, nombres, tipoTrabajador, categTrabajador, estadoTrabajador, modoContratacion, imgTrabajador, usuario, fchCreacion,  concat(apPaterno, ' ', apMaterno,', ', nombres) as nombCompleto FROM trabajador) AS trab";

        $dt = 0;
        $columns = array(
          array('db' => 'trab.tipoDocTrab', 'dt' => $dt++, 'field' => 'tipoDocTrab'),
          array('db' => 'trab.idTrabajador', 'dt' => $dt++, 'field' => 'idTrabajador'),
          array('db' => 'trab.nombCompleto', 'dt' => $dt++, 'field' => 'nombCompleto'),
          array('db' => 'trab.tipoTrabajador', 'dt' => $dt++, 'field' => 'tipoTrabajador'),
          array('db' => 'trab.categTrabajador', 'dt' => $dt++, 'field' => 'categTrabajador'),
          array('db' => 'trab.estadoTrabajador', 'dt' => $dt++, 'field' => 'estadoTrabajador'),
          array('db' => 'trab.modoContratacion', 'dt' => $dt++, 'field' => 'modoContratacion'),
          array('db' => 'trab.usuario', 'dt' => $dt++, 'field' => 'usuario'),
          array('db' => 'trab.fchCreacion', 'dt' => $dt++, 'field' => 'fchCreacion'),
          array('db' => 'trab.imgTrabajador', 'dt' => $dt++, 'field' => 'imgTrabajador'),
          array('db' => 'trab.idTrabajador', 'dt' => $dt++,'field' => 'idTrabajador',
            'formatter' => function( $d, $row ) {
              $fecha = $_SESSION['fecha'];

              $cadena = "";

              if ($_GET["edicion"] == "Si")
                $cadena .= "<img src='imagenes/lapiz.png' width='15' height='15' onclick =  \"abrirventana('index.php?controlador=trabajadores&accion=editatrabajador&dni=$d') \"  >";

              if ($_GET["admin"] == "Si"){
                $cadena .= "<img  src='imagenes/menos.jpg' width='15' height='15' onclick =  \"abrirventanaflotante('index.php?controlador=trabajadores&accion=eliminatrabajador&dni=$d&nombcompleto=".$row["nombCompleto"]."')\">";

                if($row["categTrabajador"] == "5ta"){
                  $cadena .= "<img  src='imagenes/icoVacac.jpg' width='15' height='15' onclick = \"abrirventana('index.php?controlador=trabajadores&accion=addvacaciones&dni=$d')\">";
                }

                if($row["imgTrabajador"] != ""){
                  $cadena .= "<img src='imagenes/icoFoto.png' width='15' height='15' onclick =  \"abrirventanaflotantemediana('vistas/pdfFotoCheck.php?id=$d')\">";
                }                
              }

              if($row["modoContratacion"] != "tercero"){
                $cadena .= "<img  src='imagenes/prestamo.jpg' width='15' height='15' onclick =  \"abrirventana('index.php?controlador=trabajadores&accion=prestamo&dni=$d')\">";

                $cadena .= "<img src='imagenes/reporte.jpg' width='15' height='15' onclick =  \"abrirventana('index.php?controlador=trabajadores&accion=detallePagoRango&dni=$d&fchInicio=$fecha&fchFin=$fecha')\">";
               }   

              return $cadena;

              //return $row["nroDocTrab"];
             // return !empty($_GET['columns'][5]['search']['value'])? $_GET['columns'][5]['search']['value']: "No";
          }),
        );

      break;


      case 'listarMercaderia':
        $_SESSION['fecha'] = Date("Y-m-d");

        // DB table to use
        $table = 'incidencias_mercaderia';

        // Table's primary key
        $primaryKey = 'idmerca';
        $where = " 1 ";

        if (!empty($_GET['columns'][9]['search']['value'])){
          $where = " merca.estado_actual = '".$_GET['columns'][9]['search']['value']."'";
        };

        $joinQuery = "FROM (SELECT m.idmerca, m.fecha_danio, m.cliente, m.producto, m.cod_producto, m.guia_cliente, m.tipo_danio, m.responsable, 
                      m.observaciones, m.estado_actual, m.fecha_arreglo, m.tipo_arreglo, m.dias_transcurridos, m.monto_final, 
                      m.fecha_registro, 
                      CONCAT(t.apPaterno, ' ', t.apMaterno, ', ', t.nombres) As NomCompleto1, 
                      m.fecha_edicion, 
                      CONCAT(s.apPaterno, ' ', s.apMaterno, ', ', s.nombres) As NomCompleto2 
                      FROM 
                      incidencias_mercaderia m 
                      left join trabajador t ON m.idTrabajador = t.idTrabajador 
                      left join trabajador s ON m.idTrabajador_editor = s.idTrabajador) AS merca";

        $dt = 0;
        $columns = array(
          array('db' => 'merca.idmerca', 'dt' => $dt++, 'field' => 'idmerca'),
          array('db' => 'merca.fecha_danio', 'dt' => $dt++, 'field' => 'fecha_danio'),
          array('db' => 'merca.cliente', 'dt' => $dt++, 'field' => 'cliente'),
          array('db' => 'merca.producto', 'dt' => $dt++, 'field' => 'producto'),
          array('db' => 'merca.cod_producto', 'dt' => $dt++, 'field' => 'cod_producto'),
          array('db' => 'merca.guia_cliente', 'dt' => $dt++, 'field' => 'guia_cliente'),
          array('db' => 'merca.tipo_danio', 'dt' => $dt++, 'field' => 'tipo_danio'),
          array('db' => 'merca.responsable', 'dt' => $dt++, 'field' => 'responsable'),
          array('db' => 'merca.observaciones', 'dt' => $dt++, 'field' => 'observaciones'),
          array('db' => 'merca.estado_actual', 'dt' => $dt++, 'field' => 'estado_actual'),
          array('db' => 'merca.fecha_arreglo', 'dt' => $dt++, 'field' => 'fecha_arreglo'),
          array('db' => 'merca.tipo_arreglo', 'dt' => $dt++, 'field' => 'tipo_arreglo'),
          array('db' => 'merca.dias_transcurridos', 'dt' => $dt++, 'field' => 'dias_transcurridos'),

          array('db' => 'merca.monto_final', 'dt' => $dt++, 'field' => 'monto_final','formatter' => create_function('$d,$row', 'return number_format($d,"2", ".", ",");')),

          array('db' => 'merca.fecha_registro', 'dt' => $dt++, 'field' => 'fecha_registro'),
          array('db' => 'merca.NomCompleto1', 'dt' => $dt++, 'field' => 'NomCompleto1'),
          array('db' => 'merca.fecha_edicion', 'dt' => $dt++, 'field' => 'fecha_edicion'),
          array('db' => 'merca.NomCompleto2', 'dt' => $dt++, 'field' => 'NomCompleto2'),
          
          array('db' => 'merca.idmerca', 'dt' => $dt++,'field' => 'idmerca', 'formatter' => function( $d, $row ) {
            $fecha = $_SESSION['fecha'];

              $cadena = "";

              if ($_GET["admin"] == "Si"){
                $cadena .= "<center><img src='imagenes/foto.png' width='25' height='25' onclick =  \"abrirventanaflotante('index.php?controlador=movil&accion=verFotoMercaderia1&id=$d') \"  ></center>";
                $cadena .= "<center><img src='imagenes/foto.png' width='25' height='25' onclick =  \"abrirventanaflotante('index.php?controlador=movil&accion=verFotoMercaderia2&id=$d') \"  ></center>";  
                $cadena .= "<center><img src='imagenes/foto.png' width='25' height='25' onclick =  \"abrirventanaflotante('index.php?controlador=movil&accion=verFotoMercaderia3&id=$d') \"  ></center>"; 
              }

              return $cadena;
          } ),
      );

      break;

      case 'listarChoque':
        $_SESSION['fecha'] = Date("Y-m-d");

        // DB table to use
        $table = 'incidencias_vehiculo';

        // Table's primary key
        $primaryKey = 'idincidencia';
        $where = " 1 ";

        if (!empty($_GET['columns'][9]['search']['value'])){
          $where = " choque.estado_actual = '".$_GET['columns'][9]['search']['value']."'";
        };

        $joinQuery = "FROM (SELECT c.idincidencia, c.fecha_choque, c.placa, c.conductor, c.responsabilidad, c.monto_arreglo, c.distrito, 
                      c.direccion, c.comentario, c.estado_actual, c.fecha_registro, 
                      CONCAT(t.apPaterno, ' ', t.apMaterno, ', ', t.nombres) As NomCompleto 
                      FROM 
                      incidencias_vehiculo c 
                      left join trabajador t ON c.idTrabajador = t.idTrabajador) AS choque";

        $dt = 0;
        $columns = array(
          array('db' => 'choque.idincidencia', 'dt' => $dt++, 'field' => 'idincidencia'),
          array('db' => 'choque.fecha_choque', 'dt' => $dt++, 'field' => 'fecha_choque'),
          array('db' => 'choque.placa', 'dt' => $dt++, 'field' => 'placa'),
          array('db' => 'choque.conductor', 'dt' => $dt++, 'field' => 'conductor'),
          array('db' => 'choque.responsabilidad', 'dt' => $dt++, 'field' => 'responsabilidad'),

          array('db' => 'choque.monto_arreglo', 'dt' => $dt++, 'field' => 'monto_arreglo','formatter' => create_function('$d,$row', 'return number_format($d,"2", ".", ",");')),

          array('db' => 'choque.distrito', 'dt' => $dt++, 'field' => 'distrito'),
          array('db' => 'choque.direccion', 'dt' => $dt++, 'field' => 'direccion'),
          array('db' => 'choque.comentario', 'dt' => $dt++, 'field' => 'comentario'),
          array('db' => 'choque.estado_actual', 'dt' => $dt++, 'field' => 'estado_actual'),
          array('db' => 'choque.fecha_registro', 'dt' => $dt++, 'field' => 'fecha_registro'),
          array('db' => 'choque.NomCompleto', 'dt' => $dt++, 'field' => 'NomCompleto'),
          
          array('db' => 'choque.idincidencia', 'dt' => $dt++,'field' => 'idincidencia', 'formatter' => function( $d, $row ) {
            $fecha = $_SESSION['fecha'];

              $cadena = "";

              if ($_GET["admin"] == "Si"){
                $cadena .= "<center><img src='imagenes/foto.png' width='25' height='25' onclick =  \"abrirventanaflotante('index.php?controlador=movil&accion=verFotoChoque1&id=$d') \"  ></center>";  
                $cadena .= "<center><img src='imagenes/foto.png' width='25' height='25' onclick =  \"abrirventanaflotante('index.php?controlador=movil&accion=verFotoChoque2&id=$d') \"  ></center>";  
                $cadena .= "<center><img src='imagenes/foto.png' width='25' height='25' onclick =  \"abrirventanaflotante('index.php?controlador=movil&accion=verFotoChoque3&id=$d') \"  ></center>";  
              }

              /*$cadena .= "<center><img src='imagenes/foto.png' width='20' height='20' onclick =  \"abrirventanaflotante('index.php?controlador=movil&accion=verFotoChoque&id=$d') \"  ></center>";
              $cadena .= "<center><img src='imagenes/foto.png' width='20' height='20' onclick =  \"abrirventanaflotante('index.php?controlador=movil&accion=verFotoChoque&id=$d') \"  ></center>";
              $cadena .= "<center><img src='imagenes/foto.png' width='20' height='20' onclick =  \"abrirventanaflotante('index.php?controlador=movil&accion=verFotoChoque&id=$d') \"  ></center>"; */             

              return $cadena;
          } ),
      );

      break;
      

      case 'listarTelefonoR':
        $_SESSION['fecha'] = Date("Y-m-d");

        // DB table to use
        $table = 'incidencias_telefono';

        // Table's primary key
        $primaryKey = 'idtelefono';
        $where = " 1 ";

        if (!empty($_GET['columns'][9]['search']['value'])){
          $where = " telefonor.estado_actual = '".$_GET['columns'][9]['search']['value']."'";
        };

        $joinQuery = "FROM (SELECT m.idtelefono, m.fecha_robo, m.cliente, m.distrito_r, m.direccion, m.telefono, m.estado_actual, m.fecha_denuncia, 
                      m.distrito_d, m.comisaria, 
                      m.fecha_registro, 
                      CONCAT(t.apPaterno, ' ', t.apMaterno, ', ', t.nombres) As NomCompleto1, 
                      m.fecha_edicion, 
                      CONCAT(s.apPaterno, ' ', s.apMaterno, ', ', s.nombres) As NomCompleto2 
                      FROM 
                      incidencias_telefono m 
                      left join trabajador t ON m.idTrabajador = t.idTrabajador 
                      left join trabajador s ON m.idTrabajador_editor = s.idTrabajador) AS telefonor";

        $dt = 0;
        $columns = array(
          array('db' => 'telefonor.idtelefono', 'dt' => $dt++, 'field' => 'idtelefono'),
          array('db' => 'telefonor.fecha_robo', 'dt' => $dt++, 'field' => 'fecha_robo'),
          array('db' => 'telefonor.cliente', 'dt' => $dt++, 'field' => 'cliente'),
          array('db' => 'telefonor.distrito_r', 'dt' => $dt++, 'field' => 'distrito_r'),
          array('db' => 'telefonor.direccion', 'dt' => $dt++, 'field' => 'direccion'),
          array('db' => 'telefonor.telefono', 'dt' => $dt++, 'field' => 'telefono'),
          array('db' => 'telefonor.fecha_denuncia', 'dt' => $dt++, 'field' => 'fecha_denuncia'),
          array('db' => 'telefonor.distrito_d', 'dt' => $dt++, 'field' => 'distrito_d'),
          array('db' => 'telefonor.comisaria', 'dt' => $dt++, 'field' => 'comisaria'),
          array('db' => 'telefonor.estado_actual', 'dt' => $dt++, 'field' => 'estado_actual'),
          array('db' => 'telefonor.fecha_registro', 'dt' => $dt++, 'field' => 'fecha_registro'),
          array('db' => 'telefonor.NomCompleto1', 'dt' => $dt++, 'field' => 'NomCompleto1'),
          array('db' => 'telefonor.fecha_edicion', 'dt' => $dt++, 'field' => 'fecha_edicion'),
          array('db' => 'telefonor.NomCompleto2', 'dt' => $dt++, 'field' => 'NomCompleto2'),
          
          array('db' => 'telefonor.idtelefono', 'dt' => $dt++,'field' => 'idtelefono', 'formatter' => function( $d, $row ) {
            $fecha = $_SESSION['fecha'];

              $cadena = "";

              if ($_GET["admin"] == "Si"){
                $cadena .= "<center><img src='imagenes/foto.png' width='25' height='25' onclick =  \"abrirventanaflotante('index.php?controlador=movil&accion=verFotoTelefono&id=$d') \"  ></center>"; 
              }            

              return $cadena;
          } ),
      );

      break;

      case 'listarPapeleta':
        $_SESSION['fecha'] = Date("Y-m-d");

        // DB table to use
        $table = 'incidencias_infraccion';

        // Table's primary key
        $primaryKey = 'idinfraccion';
        $where = " 1 ";

        /*if (!empty($_GET['columns'][9]['search']['value'])){
          $where = " abast.estado_actual = '".$_GET['columns'][9]['search']['value']."'";
        };*/

        $joinQuery = "FROM (SELECT i.idinfraccion, i.fecha_infraccion, i.placa, i.conductor, i.distrito, i.direccion, i.cuenta, i.infraccion, 
                      i.descripcion, i.monto, i.descuento,
                      i.fecha_registro, 
                      CONCAT(t.apPaterno, ' ', t.apMaterno, ', ', t.nombres) As NomCompleto1
                      FROM 
                      incidencias_infraccion i 
                      left join trabajador t ON i.idTrabajador = t.idTrabajador) AS infrac";

        $dt = 0;
        $columns = array(
          array('db' => 'infrac.idinfraccion', 'dt' => $dt++, 'field' => 'idinfraccion'),
          array('db' => 'infrac.fecha_infraccion', 'dt' => $dt++, 'field' => 'fecha_infraccion'),
          array('db' => 'infrac.placa', 'dt' => $dt++, 'field' => 'placa'),
          array('db' => 'infrac.conductor', 'dt' => $dt++, 'field' => 'conductor'),
          array('db' => 'infrac.distrito', 'dt' => $dt++, 'field' => 'distrito'),
          array('db' => 'infrac.direccion', 'dt' => $dt++, 'field' => 'direccion'),
          array('db' => 'infrac.cuenta', 'dt' => $dt++, 'field' => 'cuenta'),
          array('db' => 'infrac.infraccion', 'dt' => $dt++, 'field' => 'infraccion'),
          array('db' => 'infrac.descripcion', 'dt' => $dt++, 'field' => 'descripcion'),

          array('db' => 'infrac.monto', 'dt' => $dt++, 'field' => 'monto','formatter' => create_function('$d,$row', 'return number_format($d,"2", ".", ",");')),
          array('db' => 'infrac.descuento', 'dt' => $dt++, 'field' => 'descuento','formatter' => create_function('$d,$row', 'return number_format($d,"2", ".", ",");')),

          array('db' => 'infrac.fecha_registro', 'dt' => $dt++, 'field' => 'fecha_registro'),
          array('db' => 'infrac.NomCompleto1', 'dt' => $dt++, 'field' => 'NomCompleto1'),
          
          array('db' => 'infrac.idinfraccion', 'dt' => $dt++,'field' => 'idinfraccion', 'formatter' => function( $d, $row ) {
            $fecha = $_SESSION['fecha'];

              $cadena = "";

              if ($_GET["admin"] == "Si"){
                $cadena .= "<center><img src='imagenes/foto.png' width='25' height='25' onclick =  \"abrirventanaflotante('index.php?controlador=movil&accion=verFotoPapeleta&id=$d') \"  ></center>";
              }

              return $cadena;
          } ),
      );

      break;

      case 'listarAbastecimiento':
        $_SESSION['fecha'] = Date("Y-m-d");

        // DB table to use
        $table = 'incidencias_abastecimiento';

        // Table's primary key
        $primaryKey = 'idabastecimiento';
        $where = " 1 ";

        /*if (!empty($_GET['columns'][9]['search']['value'])){
          $where = " abast.estado_actual = '".$_GET['columns'][9]['search']['value']."'";
        };*/

        $joinQuery = "FROM (SELECT a.idabastecimiento, a.fecha, a.placa, a.cliente, a.conductor, a.grifo, a.tipo_combustible, a.kilometraje_anterior, a.kilometraje_actual, a.und_medida, 
                      a.cantidad, a.importe,
                      a.fecha_registro, 
                      CONCAT(t.apPaterno, ' ', t.apMaterno, ', ', t.nombres) As NomCompleto1
                      FROM 
                      incidencias_abastecimiento a 
                      left join trabajador t ON a.idTrabajador = t.idTrabajador) AS abast";

        $dt = 0;
        $columns = array(
          array('db' => 'abast.idabastecimiento', 'dt' => $dt++, 'field' => 'idabastecimiento'),
          array('db' => 'abast.fecha', 'dt' => $dt++, 'field' => 'fecha'),
          array('db' => 'abast.placa', 'dt' => $dt++, 'field' => 'placa'),
          array('db' => 'abast.cliente', 'dt' => $dt++, 'field' => 'cliente'),
          array('db' => 'abast.conductor', 'dt' => $dt++, 'field' => 'conductor'),
          array('db' => 'abast.grifo', 'dt' => $dt++, 'field' => 'grifo'),
          array('db' => 'abast.tipo_combustible', 'dt' => $dt++, 'field' => 'tipo_combustible'),
          array('db' => 'abast.kilometraje_anterior', 'dt' => $dt++, 'field' => 'kilometraje_anterior'),
          array('db' => 'abast.kilometraje_actual', 'dt' => $dt++, 'field' => 'kilometraje_actual'),
          array('db' => 'abast.und_medida', 'dt' => $dt++, 'field' => 'und_medida'),

          array('db' => 'abast.cantidad', 'dt' => $dt++, 'field' => 'cantidad','formatter' => create_function('$d,$row', 'return number_format($d,"2", ".", ",");')),
          array('db' => 'abast.importe', 'dt' => $dt++, 'field' => 'importe','formatter' => create_function('$d,$row', 'return number_format($d,"2", ".", ",");')),

          array('db' => 'abast.fecha_registro', 'dt' => $dt++, 'field' => 'fecha_registro'),
          array('db' => 'abast.NomCompleto1', 'dt' => $dt++, 'field' => 'NomCompleto1'),
          
          array('db' => 'abast.idabastecimiento', 'dt' => $dt++,'field' => 'idabastecimiento', 'formatter' => function( $d, $row ) {
            $fecha = $_SESSION['fecha'];

              $cadena = "";

              if ($_GET["admin"] == "Si"){
                $cadena .= "<center><img src='imagenes/foto.png' width='18' height='18' onclick =  \"abrirventanaflotante('index.php?controlador=movil&accion=verFotoAbastecimiento&id=$d')\"><img border='0' src='imagenes/lapiz.png' class = 'editar' width='15' height='15' align='left'></center>";
              }
              return $cadena;
          } ),
      );

      break;

      case 'listarServicios':
        $_SESSION['fecha'] = Date("Y-m-d");

        // DB table to use
        $table = 'servicios';

        // Table's primary key
        $primaryKey = 'id_servicio';
        $where = " 1 ";

        $joinQuery = "FROM (SELECT id_servicio, des_servicio, clase_servicio, tipo_mantenimiento, usuario, fecha_creacion FROM servicios) AS serv";

        $dt = 0;
        $columns = array(
          array('db' => 'serv.id_servicio', 'dt' => $dt++, 'field' => 'id_servicio'),
          array('db' => 'serv.des_servicio', 'dt' => $dt++, 'field' => 'des_servicio'),
          array('db' => 'serv.clase_servicio', 'dt' => $dt++, 'field' => 'clase_servicio'),
          array('db' => 'serv.tipo_mantenimiento', 'dt' => $dt++, 'field' => 'tipo_mantenimiento'),
          array('db' => 'serv.usuario', 'dt' => $dt++, 'field' => 'usuario'),
          array('db' => 'serv.fecha_creacion', 'dt' => $dt++, 'field' => 'fecha_creacion'),

          array('db' => 'serv.id_servicio', 'dt' => $dt++,'field' => 'id_servicio', 'formatter' => function( $d, $row ) {
            $fecha = $_SESSION['fecha'];

              $cadena = "";

              if ($_GET["admin"] == "Si"){
                $cadena .= "<center>
                              <img border='0' src='imagenes/lapiz.png' class = 'editar' width='15' height='15' align='center'>
                              <img border='0' src='imagenes/eliminar.png' class = 'eliminar' width='18' height='18' align='center'>
                            </center>";
              }
              return $cadena;
          } ),
      );

      break;


      case 'listarPlacasCheckList':
        $_SESSION['fecha'] = Date("Y-m-d");

        // DB table to use
        $table = 'vehiculo';

        // Table's primary key
        $primaryKey = 'idPlaca';
        $where = " 1 ";

        $joinQuery = "FROM (SELECT p.fchDespacho, v.idPlaca, IF(p.placa=c.placa, 'COMPLETADO', 'PENDIENTE') AS checklist, 
                      pd.NombresO AS trabAsignado,
                      IF(RIGHT((c.fecha_registro),8) IS NULL, '00:00:00', RIGHT((c.fecha_registro),8)) AS Hora_Registro,
                      c.km_anterior_checklist_mantenimiento,
                      c.km_actual_checklist_mantenimiento,
                      c.km_actual_checklist_mantenimiento - c.km_anterior_checklist_mantenimiento AS diferencia,
                      IF(concat(t.apPaterno,' ',t.apMaterno,', ',t.nombres) IS NULL, '', concat(t.apPaterno,' ',t.apMaterno,', ',t.nombres)) AS trabCompletado
                      FROM vehiculo v 
                      INNER JOIN programdespacho p ON v.idPlaca = p.placa 
                      JOIN (SELECT pd.idProgram, pd.idTrabajador, 
                      IF(concat(t.apPaterno,' ',t.apMaterno,', ',t.nombres) IS NULL, '', concat(t.apPaterno,' ',t.apMaterno,', ',t.nombres)) AS NombresO 
                      FROM  programdesppers pd INNER JOIN trabajador t ON t.idTrabajador = pd.idTrabajador WHERE tipoRol = 'Conductor') AS pd ON pd.idProgram = p.id
                      LEFT JOIN checkListMantenimiento c ON p.placa = c.placa AND p.fchDespacho = LEFT((c.fecha_registro),10) 
                      LEFT JOIN trabajador t ON t.idTrabajador = c.idTrabajador  
                      WHERE 
                      v.estado = 'Activo' AND v.considerarPropio = 'Si' AND p.fchDespacho <= curdate() AND p.fchDespacho >= CURDATE() - INTERVAL 30 DAY) AS plachcklist";

        $dt = 0;
        $columns = array(
          array('db' => 'plachcklist.fchDespacho', 'dt' => $dt++, 'field' => 'fchDespacho'),
          array('db' => 'plachcklist.idPlaca', 'dt' => $dt++, 'field' => 'idPlaca'),
          array('db' => 'plachcklist.checklist', 'dt' => $dt++, 'field' => 'checklist'),
          array('db' => 'plachcklist.trabAsignado', 'dt' => $dt++, 'field' => 'trabAsignado'),
          array('db' => 'plachcklist.Hora_Registro', 'dt' => $dt++, 'field' => 'Hora_Registro'),
          array('db' => 'plachcklist.km_anterior_checklist_mantenimiento', 'dt' => $dt++, 'field' => 'km_anterior_checklist_mantenimiento'),
          array('db' => 'plachcklist.km_actual_checklist_mantenimiento', 'dt' => $dt++, 'field' => 'km_actual_checklist_mantenimiento'),
          array('db' => 'plachcklist.diferencia', 'dt' => $dt++, 'field' => 'diferencia'),
          array('db' => 'plachcklist.trabCompletado', 'dt' => $dt++, 'field' => 'trabCompletado'),

          array('db' => 'plachcklist.idPlaca', 'dt' => $dt++,'field' => 'idPlaca', 'formatter' => function( $d, $row ) {
            $fecha = $_SESSION['fecha'];

            $cadena = "<center>";

            if ($_GET["admin"] == "Si"){
              $cadena .= "<img border='0' src='imagenes/lapiz.png' title='Editar Kilometraje' class = 'editar' width='15' height='15' align='center'>";
            }
            $cadena .= "<img border='0' src='imagenes/details.png' title='Mostrar detalle' class = 'detalle' width='15' height='15' align='center'>
                        </center>";
            return $cadena;
          } ),
      );

      break;


      //////////////
      case 'listarRespuestasCheckList':
        $_SESSION['fecha'] = Date("Y-m-d");

        // DB table to use
        $table = 'pregunta_respuestaMantenimiento';

        // Table's primary key
        $primaryKey = 'id_respuesta';
        $where = " 1 ";

        $joinQuery = "FROM (SELECT clm.placa, LEFT((clm.fecha_registro),10) AS fecha, pcm.pregunta, prm.respuesta, prm.observacion 
                            FROM pregunta_respuestaMantenimiento prm 
                            INNER JOIN preguntasCheckMantenimiento pcm ON prm.idpregunta = pcm.idpregunta 
                            INNER JOIN checkListMantenimiento clm ON clm.id_checklist_mantenimiento = prm.id_checklist_mantenimiento) AS reschcklist";

        $dt = 0;
        $columns = array(
          array('db' => 'reschcklist.placa', 'dt' => $dt++, 'field' => 'placa'),
          array('db' => 'reschcklist.fecha', 'dt' => $dt++, 'field' => 'fecha'),
          array('db' => 'reschcklist.pregunta', 'dt' => $dt++, 'field' => 'pregunta'),
          array('db' => 'reschcklist.respuesta', 'dt' => $dt++, 'field' => 'respuesta'),
          array('db' => 'reschcklist.observacion', 'dt' => $dt++, 'field' => 'observacion'),

          array('db' => 'reschcklist.placa', 'dt' => $dt++,'field' => 'placa', 'formatter' => function( $d, $row ) {
            $fecha = $_SESSION['fecha'];

              $cadena = "";

              if ($_GET["admin"] == "Si"){
                $cadena .= "<center>
                              <img border='0' src='imagenes/lapiz.png' class = 'editar' width='15' height='15' align='center'>
                            </center>";
              }
              return $cadena;
          } ),
      );

      break;

      case 'listarDetalleRespuestasCheckList':
        $_SESSION['fecha'] = Date("Y-m-d");
        $fecha = $_GET["fecha"];
        $placa = $_GET["placa"];

        // DB table to use
        $table = 'pregunta_respuestaMantenimiento';

        // Table's primary key
        $primaryKey = 'id_respuesta';
        $where = " 1 ";

        $joinQuery = "FROM (SELECT clm.placa, LEFT((clm.fecha_registro),10) AS fecha, pcm.pregunta, prm.respuesta, prm.observacion 
                            FROM pregunta_respuestaMantenimiento prm 
                            INNER JOIN preguntasCheckMantenimiento pcm ON prm.idpregunta = pcm.idpregunta 
                            INNER JOIN checkListMantenimiento clm ON clm.id_checklist_mantenimiento = prm.id_checklist_mantenimiento 
                            WHERE LEFT((clm.fecha_registro),10) = '$fecha' AND clm.placa = '$placa') AS reschcklist";

        $dt = 0;
        $columns = array(
          array('db' => 'reschcklist.placa', 'dt' => $dt++, 'field' => 'placa'),
          array('db' => 'reschcklist.fecha', 'dt' => $dt++, 'field' => 'fecha'),
          array('db' => 'reschcklist.pregunta', 'dt' => $dt++, 'field' => 'pregunta'),
          array('db' => 'reschcklist.respuesta', 'dt' => $dt++, 'field' => 'respuesta'),
          array('db' => 'reschcklist.observacion', 'dt' => $dt++, 'field' => 'observacion'),

          array('db' => 'reschcklist.placa', 'dt' => $dt++,'field' => 'placa', 'formatter' => function( $d, $row ) {
            $fecha = $_SESSION['fecha'];

              $cadena = "";
              
              return $cadena;
          } ),
      );

      break;


      case 'listarUsuariosOcurrencia':
        $_SESSION['fecha'] = Date("Y-m-d");

        // DB table to use
        $table = 'usuario_incidencia';

        // Table's primary key
        $primaryKey = 'id';
        $where = " 1 ";

        $joinQuery = "FROM (SELECT ui.id, ui.usuario, ui.password, ui.email, ui.idTrabajador, ui.nombres, ui.tipoTrabajador, ui.estado
                      FROM 
                      usuario_incidencia ui) AS usui";

        $dt = 0;
        $columns = array(
          array('db' => 'usui.id', 'dt' => $dt++, 'field' => 'id'),
          array('db' => 'usui.usuario', 'dt' => $dt++, 'field' => 'usuario'),
          array('db' => 'usui.password', 'dt' => $dt++, 'field' => 'password'),
          array('db' => 'usui.email', 'dt' => $dt++, 'field' => 'email'),
          array('db' => 'usui.idTrabajador', 'dt' => $dt++, 'field' => 'idTrabajador'),
          array('db' => 'usui.nombres', 'dt' => $dt++, 'field' => 'nombres'),
          array('db' => 'usui.tipoTrabajador', 'dt' => $dt++, 'field' => 'tipoTrabajador'),
          array('db' => 'usui.estado', 'dt' => $dt++, 'field' => 'estado'),
          
          array('db' => 'usui.id', 'dt' => $dt++,'field' => 'id', 'formatter' => function( $d, $row ) {
            $fecha = $_SESSION['fecha'];

              $cadena = "";

              if ($_GET["admin"] == "Si"){
                $cadena .= "<center><img border='0' src='imagenes/lapiz.png' class = 'editar' width='15' height='15'></center>";
              }
              return $cadena;
          } ),
      );

      break;

      case 'listarCotizaciones':
        // DB table to use
        $table = 'cotizacion';

        // Table's primary key
        $primaryKey = 'id_cotizacion';
        $where = " 1 ";

        $joinQuery = "FROM (SELECT id_cotizacion, cliente, ruc_dni, direccion, condicion_pago, contacto, telefono, email, fecha, subtotal, igv, total, usuario FROM `cotizacion`) AS cotiz";

        $dt = 0;
        $columns = array(
          array('db' => 'cotiz.id_cotizacion', 'dt' => $dt++, 'field' => 'id_cotizacion'),
          array('db' => 'cotiz.cliente', 'dt' => $dt++, 'field' => 'cliente'),
          array('db' => 'cotiz.ruc_dni', 'dt' => $dt++, 'field' => 'ruc_dni'),
          array('db' => 'cotiz.direccion', 'dt' => $dt++, 'field' => 'direccion'),
          array('db' => 'cotiz.condicion_pago', 'dt' => $dt++, 'field' => 'condicion_pago'),
          array('db' => 'cotiz.contacto', 'dt' => $dt++, 'field' => 'contacto'),
          array('db' => 'cotiz.telefono', 'dt' => $dt++, 'field' => 'telefono'),
          array('db' => 'cotiz.email', 'dt' => $dt++, 'field' => 'email'),
          array('db' => 'cotiz.fecha', 'dt' => $dt++, 'field' => 'fecha'),
          array('db' => 'cotiz.subtotal', 'dt' => $dt++, 'field' => 'subtotal'),
          array('db' => 'cotiz.igv', 'dt' => $dt++, 'field' => 'igv'),
          array('db' => 'cotiz.total', 'dt' => $dt++, 'field' => 'total'),
          array('db' => 'cotiz.usuario', 'dt' => $dt++, 'field' => 'usuario'),

          array('db' => 'cotiz.id_cotizacion', 'dt' => $dt++,'field' => 'id_cotizacion', 'formatter' => function( $d, $row ) {
            $cadena = "<center>
                        <img border='0' src='imagenes/lapiz.png' title='Editar detalles de la cotizacion.' class='editar' width='15' height='15'>
                        <img border='0' src='imagenes/details.png' title='Mostrar detalles de la cotizacion.' class='detalle' width='15' height='15''>
                        <img border='0' src='imagenes/descargar.png' title='Descargar detalles de la cotizacion.' class='descargar' width='15' height='15'>
                      </center>";
              return $cadena;
          }),
      );

      break;

      case 'listarGestionSSTCovid':
        // DB table to use
        $table = 'gestion_sst_covid';

        // Table's primary key
        $primaryKey = 'id';
        $where = "1";

        $joinQuery = " FROM (SELECT concat(id, '_', descripcion) AS dataIncluye, descripcion FROM `gestion_sst_covid` WHERE estado = 'Activo') AS gest";

        $dt = 0;
        $columns = array(
          array('db' => 'gest.dataIncluye', 'dt' => $dt++, 'field' => 'dataIncluye'),
          array('db' => 'gest.descripcion', 'dt' => $dt++, 'field' => 'descripcion'),
        );
      break;

      case 'listarDataOcurrencia':
        // DB table to use
        $table = 'incidencias_generales';

        // Table's primary key
        $primaryKey = 'idgeneral';
        $where = "1";

        $joinQuery = " FROM (SELECT ig.idgeneral, CONCAT(c.idRuc, '-', ig.cuenta) AS cuenta, ig.placa, ig.cliente, ig.guia, ig.producto, ig.modelo, ig.sku, ti.incidencia, ti.prioridad, ig.observaciones, ig.estado, ig.fecha_registro, CONCAT(t.apPaterno, ' ', t.apMaterno, ', ', t.nombres) AS trabajador, ig.fecha_edicion, IF(ig.fecha_edicion = '0000-00-00 00:00:00', DATEDIFF(now(), ig.fecha_registro), DATEDIFF(ig.fecha_edicion, ig.fecha_registro)) AS dias_transcurridos, ig.usuario FROM incidencias_generales ig INNER JOIN cliente c ON ig.cuenta = c.nombre INNER JOIN tipo_incidencia ti ON ig.idincidencia = ti.idincidencia INNER JOIN trabajador t ON ig.idTrabajador = t.idTrabajador) AS ogeneral";

        $dt = 0;
        $columns = array(
          array('db' => 'ogeneral.idgeneral', 'dt' => $dt++, 'field' => 'idgeneral'),
          array('db' => 'ogeneral.cuenta', 'dt' => $dt++, 'field' => 'cuenta'),
          array('db' => 'ogeneral.placa', 'dt' => $dt++, 'field' => 'placa'),
          array('db' => 'ogeneral.cliente', 'dt' => $dt++, 'field' => 'cliente'),
          array('db' => 'ogeneral.guia', 'dt' => $dt++, 'field' => 'guia'),
          array('db' => 'ogeneral.producto', 'dt' => $dt++, 'field' => 'producto'),
          array('db' => 'ogeneral.modelo', 'dt' => $dt++, 'field' => 'modelo'),
          array('db' => 'ogeneral.sku', 'dt' => $dt++, 'field' => 'sku'),
          array('db' => 'ogeneral.incidencia', 'dt' => $dt++, 'field' => 'incidencia'),
          array('db' => 'ogeneral.prioridad', 'dt' => $dt++, 'field' => 'prioridad'),
          array('db' => 'ogeneral.observaciones', 'dt' => $dt++, 'field' => 'observaciones'),
          array('db' => 'ogeneral.estado', 'dt' => $dt++, 'field' => 'estado'),
          array('db' => 'ogeneral.fecha_registro', 'dt' => $dt++, 'field' => 'fecha_registro'),
          array('db' => 'ogeneral.trabajador', 'dt' => $dt++, 'field' => 'trabajador'),
          array('db' => 'ogeneral.fecha_edicion', 'dt' => $dt++, 'field' => 'fecha_edicion'),
          array('db' => 'ogeneral.dias_transcurridos', 'dt' => $dt++, 'field' => 'dias_transcurridos'),
          array('db' => 'ogeneral.usuario', 'dt' => $dt++, 'field' => 'usuario'),

          array('db' => 'ogeneral.idgeneral', 'dt' => $dt++,'field' => 'idgeneral', 'formatter' => function( $d, $row ) {
            $cadena = "<center>
                          <img border='0' src='imagenes/lapiz.png' title='Editar detalles de la cotizacion.' class='editar' width='15' height='15'>
                      </center>";
              return $cadena;
          }),
        );
      break;
      //////////////


      case 'listarPagoTerceroPrincipal':
        $_SESSION['fecha'] = Date("Y-m-d");

        // DB table to use
        $table = 'despachovehiculotercero';

        // Table's primary key
        $primaryKey = 'fchDespacho';
        //$where = " 1 ";

        $docTercero = $_GET["docTercero"];

        $where = "1"; // " `t1`.`docTercero` = '$docTercero'";

        $joinQuery = " FROM (SELECT  concat( `desvehter`.`fchDespacho`,'-' , `desvehter`.`correlativo`,'-0','-','Despacho','-',costoDia  ) AS id,  `desvehter`.`docTercero`, `desvehter`.`fchDespacho`, `desvehter`.`correlativo`, concat(`desvehter`.`correlativo`,'-0') AS correl02 , 'Despacho' AS tipo, `desvehter`.`placa`, `despacho`.`idCliente`, `cliente`.`nombre`, `despacho`.cuenta, `despacho`.m3, `despacho`.tipoDestino, `despacho`.ptoDestino, `desvehter`.`costoDia`, `desvehter`.`fchPago`, `desvehter`.`pagado`, `desvehter`.docPagoTercero, despacho.observacion,

         GROUP_CONCAT(CONCAT(substring(concat(`trabajador`.`apPaterno`,' ',`trabajador`.`apMaterno`, ' ', trabajador.nombres),1, 23),' ', `despachopersonal`.tipoRol,'->',' ', trabajador.categTrabajador) SEPARATOR '<br>') as tripulacion

         , SUM(if(`despachopersonal`.tipoRol = 'Conductor' AND `despachopersonal`.idTrabajador != '00000001' ,1,0)) AS cantConductor, SUM(if(`despachopersonal`.tipoRol = 'Auxiliar',1,0)) AS cantAuxiliar, clientecuentaproducto.nombProducto FROM `despachovehiculotercero` AS desvehter, `cliente`, clientecuentaproducto, `despacho`
          LEFT JOIN despachopersonal ON despacho.fchDespacho = despachopersonal.fchDespacho AND  despacho.correlativo = despachopersonal.correlativo  AND `despachopersonal`.`tipoRol` != 'Coordinador' 
          LEFT JOIN trabajador ON despachopersonal.idTrabajador = trabajador.idTrabajador
          WHERE despacho.idProducto = clientecuentaproducto.idProducto AND `desvehter`.`fchDespacho` = `despacho`.`fchDespacho` AND `desvehter`.`correlativo` = `despacho`.`correlativo` AND `desvehter`.`pagado` = 'No' AND `despacho`.`idCliente` = `cliente`.`idRuc` AND docPagoTercero is null AND `desvehter`.`docTercero` = '$docTercero' GROUP BY `desvehter`.fchDespacho, `desvehter`.correlativo

          UNION

          SELECT 'id' AS id, idTercero AS docTercero,  ocuter.`fchDespacho`, ocuter.`correlativo`, concat(ocuter.`correlativo`,'-', `corrOcurrencia`) AS correl02 , ocuter.`tipoOcurrencia`, des.placa, des.idCliente, cliente.nombre, des.cuenta, des.m3, `des`.tipoDestino, `des`.ptoDestino, ocuter.`montoTotal`, 'fchPago' AS fchPago, 'pagado' AS pagado, ocuter.docPagoTercero, ocuter.`descripcion`,  `tipoConcepto`, 'cantConductor', 'cantAuxilar', 'nombProducto' FROM `ocurrenciatercero` AS ocuter, despacho AS des, cliente WHERE des.fchDespacho = ocuter.fchDespacho AND des.correlativo = ocuter.correlativo AND des.idCliente = cliente.idRuc AND ocuter.docPagoTercero IS NULL AND idTercero = '$docTercero'
           ) AS t1   ";

        $dt = 0;
        $columns = array(
          array('db' => 't1.fchDespacho', 'dt' => $dt++, 'field' => 'fchDespacho'),
          //array('db' => 't1.prueba', 'dt' => $dt++, 'field' => 'prueba'),
          array('db' => 't1.correl02', 'dt' => $dt++, 'field' => 'correl02'),
          array('db' => 't1.tipo', 'dt' => $dt++, 'field' => 'tipo'),
          array('db' => 't1.placa', 'dt' => $dt++, 'field' => 'placa'),
          array('db' => 't1.nombre', 'dt' => $dt++, 'field' => 'nombre'),
          array('db' => 't1.cuenta', 'dt' => $dt++, 'field' => 'cuenta'),
          array('db' => 't1.nombProducto', 'dt' => $dt++, 'field' => 'nombProducto'),
          array('db' => 't1.tripulacion', 'dt' => $dt++, 'field' => 'tripulacion'),
          array('db' => 't1.observacion', 'dt' => $dt++, 'field' => 'observacion'),
          /*array('db' => 't1.costoDia',
                  'dt' => $dt++,
                  'field' => 'costoDia',
                  'formatter' => create_function('$d,$row', 'return number_format($d,"2", ".", ",");')
                ),*/
          array('db' => 't1.costoDia', 'dt' => $dt++,'field' => 'costoDia',
            'formatter' => function( $d, $row ) {

              $cadena = "<span class = 'monto dblClicMonto'>". number_format($d,"2", ".", ",") ."</span><input class = 'editarMonto' type = 'text' style='padding: 3px; display: none;width: 55px;' value = '".number_format($d,"2", ".", ",")."'><img class = 'icoGuardar' src='imagenes/guardar.png' width='15' height= '15' style='display: none;'>" ;

              return $cadena;

          }),

          array('db' => 't1.fchDespacho', 'dt' => $dt++,'field' => 'fchDespacho',
            'formatter' => function( $d, $row ) {
              $fecha = $_SESSION['fecha'];

              $cadena = "";

              if ($row["tipo"] == "Despacho")
                $cadena .= "<img class = 'icoMas' src='imagenes/mas.png' width='15' height= '15'>";
              else
                $cadena .= "<img class = 'icoMenos' src='imagenes/menos.png' width='15' height= '15'>";

              $cadena .= "<img class = 'icoInterc'  src='imagenes/intercambiar2.png' width='15' height= '15'>";
              //$cadena .= "<img class = 'icoEditar'  src='imagenes/lapiz.png' width='15' height= '15'>";
              //$cadena .= "<img class = 'icoGuardar' src='imagenes/guardar.png' width='15' height= '15' style='display: none;'>";

              return $cadena;

          }),

        );

      break;   


      case 'listarPagoTerceroAbastOtros':
        $_SESSION['fecha'] = Date("Y-m-d");

        // DB table to use
        $table = 'combustible';

        // Table's primary key
        $primaryKey = 'idPlaca';
        //$where = " 1 ";

        $docTercero = $_GET["docTercero"];

        $where = "1"; // " `t1`.`docTercero` = '$docTercero'";

        $joinQuery = " FROM (SELECT `ccmovimencab`.`fchEvento` AS fecha, `ccmovimientos`.`idPlaca`,  `ccmovimientos`.`descripcion`, `ccmovimencab`.`tipoDoc`, `ccmovimencab`.`nroDoc`, `ccmovimientos`.`monto`, concat(`ccmovimientos`.nroOrden, '@' ,`ccmovimientos`.correlativo) AS idAux FROM `vehiculo`, `ccmovimencab`, `ccmovimientos` LEFT JOIN `ccitem` ON `ccmovimientos`.`item` = `ccitem`.`idItem` WHERE `ccmovimencab`.idMovimEncab = `ccmovimientos`.nroOrden AND `ccmovimientos`.`idPlaca` = `vehiculo`.`idPlaca` AND `ccmovimientos`.`docPagoTercero` IS NULL AND `vehiculo`.`rznSocial` = '$docTercero' 
          UNION
          SELECT `combustible`.`fchCreacion` AS fecha,  `combustible`.`idPlaca`, concat(`chofer`, ' ', `grifo`) AS descripcion, 'Vale' AS tipoDoc, nroVale AS nroDoc ,  `galones`*`precioGalon` as `monto`, concat(combustible.fchCreacion,'@', combustible.hraCreacion,'@', combustible.idPlaca) As idAux FROM `combustible`, `vehiculo` WHERE `combustible`.`idPlaca` = `vehiculo`.`idPlaca` AND `vehiculo`.`rznSocial` = '$docTercero' AND docPagoTercero is null)  AS t1   ";

        $dt = 0;
        $columns = array(
          array('db' => 't1.fecha', 'dt' => $dt++, 'field' => 'fecha'),
          array('db' => 't1.idPlaca', 'dt' => $dt++, 'field' => 'idPlaca'),
          array('db' => 't1.descripcion', 'dt' => $dt++, 'field' => 'descripcion'),
          array('db' => 't1.tipoDoc', 'dt' => $dt++, 'field' => 'tipoDoc'),
          array('db' => 't1.nroDoc', 'dt' => $dt++, 'field' => 'nroDoc'),
          /*array('db' => 't1.monto',
                  'dt' => $dt++,
                  'field' => 'monto',
                  'formatter' => create_function('$d,$row', 'return number_format($d,"2", ".", ",");')
                ),
*/

          array('db' => 't1.monto', 'dt' => $dt++,'field' => 'monto',
            'formatter' => function( $d, $row ) {

              $cadena = "<span class = 'monto'>". number_format($d,"2", ".", ",") ."</span><input class = 'editarMonto' type = 'text' style='padding: 3px; display: none;width: 55px;' value = '".number_format($d,"2", ".", ",")."'>" ;

              return $cadena;

          }),


          array('db' => 't1.idAux', 'dt' => $dt++, 'field' => 'idAux'),

          array('db' => 't1.fecha', 'dt' => $dt++,'field' => 'fecha',
            'formatter' => function( $d, $row ) {
              //$fecha = $_SESSION['fecha'];

              $cadena = "";
              $cadena .= "<img class = 'icoMenos' src='imagenes/menos.png' width='15' height= '15'>";
              return $cadena;

          }),

        );
      break;


      case 'listarPagoTerceroMiscelaneos':
        $_SESSION['fecha'] = Date("Y-m-d");

        // DB table to use
        $table = 'liqterceromisc';

        // Table's primary key
        $primaryKey = 'id';
        //$where = " 1 ";

        $docTercero = $_GET["docTercero"];

        $where = "1"; // " `t1`.`docTercero` = '$docTercero'";

        $joinQuery = " FROM (SELECT `id`, `fchEvento`, `liqterceromisc`.`idPlaca`, `liqterceromisc`.`descripcion`, `monto`, `liqterceromisc`.tipoMisc , `liqterceromisc`.docPagoTercero, `liqterceromisc`.docAnexo FROM `liqterceromisc`, `vehiculo`  WHERE  `liqterceromisc`.`idPlaca` = `vehiculo`.`idPlaca` AND `liqterceromisc`.`docPagoTercero` IS NULL  AND `vehiculo`.`rznSocial` =  '$docTercero' )  AS t1   ";

        $dt = 0;
        $columns = array(
          array('db' => 't1.fchEvento', 'dt' => $dt++, 'field' => 'fchEvento'),
          array('db' => 't1.idPlaca', 'dt' => $dt++, 'field' => 'idPlaca'),
          array('db' => 't1.descripcion', 'dt' => $dt++, 'field' => 'descripcion'),
          array('db' => 't1.id', 'dt' => $dt++, 'field' => 'id'),
          array('db' => 't1.tipoMisc', 'dt' => $dt++, 'field' => 'tipoMisc'),
          /*   array('db' => 't1.monto',
                  'dt' => $dt++,
                  'field' => 'monto',
                  'formatter' => create_function('$d,$row', 'return number_format($d,"2", ".", ",");')
                ),
          */
          array('db' => 't1.monto', 'dt' => $dt++,'field' => 'monto',
            'formatter' => function( $d, $row ) {
              $cadena = "<span class = 'monto dblClicMonto'>". number_format($d,"2", ".", ",") ."</span><input class = 'editarMonto' type = 'text' style='padding: 3px; display: none;width: 55px;' value = '".number_format($d,"2", ".", ",")."'><img class = 'icoGuardar' src='imagenes/guardar.png' width='15' height= '15' style='display: none;'>" ;

              return $cadena;

          }),



          array('db' => 't1.fchEvento', 'dt' => $dt++,'field' => 'fchEvento',
            'formatter' => function( $d, $row ) {
              //$fecha = $_SESSION['fecha'];
              $cadena = "";
              //$cadena .= "<img src='imagenes/mas.png' width='13' height= '13'>";
              $cadena .= "<img class = 'icoMenos' src='imagenes/menos.png' width='15' height= '15'>";

              return $cadena;

          }),

        );
      break;


    case 'listarDocsIdentidad':
        $_SESSION['fecha'] = Date("Y-m-d");
        // DB table to use
        $table = 'trabajdocumentos';
        // Table's primary key
        $primaryKey = 'nroDocTrab';
        $where = " docums.idTrabajador ='".$_GET["idTrabajador"]."'";
        $joinQuery = " FROM trabajdocumentos docums ";

        $dt = 0;
        $columns = array(
          array('db' => 'docums.tipoDocTrab', 'dt' => $dt++, 'field' => 'tipoDocTrab'),
          array('db' => 'docums.nroDocTrab', 'dt' => $dt++, 'field' => 'nroDocTrab'),
          array('db' => 'docums.fchIni', 'dt' => $dt++, 'field' => 'fchIni'),
          array('db' => 'docums.fchFin', 'dt' => $dt++, 'field' => 'fchFin'),
          array('db' => 'docums.estado', 'dt' => $dt++, 'field' => 'estado'),
          array('db' => 'docums.adjunto', 'dt' => $dt++, 'field' => 'adjunto'),
          array('db' => 'docums.nroDocTrab', 'dt' => $dt++,'field' => 'nroDocTrab',
            'formatter' => function( $d, $row ) {
              $fecha = $_SESSION['fecha'];

              $cadena = "";

              if (($_GET["edicion"] == "Si" && $row["estado"] != 'Inactivo') || $_GET["admin"] == "Si" ){
                $cadena .= "<img src='imagenes/lapiz.png' width='15' height='15' class = 'editaDocIdentidad' tipoDoc=".$row["tipoDocTrab"]."  nroDoc = $d fchIni = ".$row["fchIni"]."  >";
              }

              if ($_GET["admin"] == "Si" &&  $row["estado"] != 'Inactivo'  ){ 
                $cadena .= "<img src='imagenes/menos.png' width='15' height='15' class = 'eliminaDocIdentidad' tipoDoc=".$row["tipoDocTrab"]."  nroDoc = $d fchIni = ".$row["fchIni"]."  >";
              }
              $cadena .= "<img  class='sinTitulo' title='Descargar el documento escaneado'  src='imagenes/descargar.png' width='15' height='15' class = 'descargaDocIdentidad' onclick = \"abrirventanaflotante('vistas/descargarEscaneoTrab.php?id=".$row["adjunto"]."')\"  >";
              return $cadena;

              //return $row["nroDocTrab"];
             // return !empty($_GET['columns'][5]['search']['value'])? $_GET['columns'][5]['search']['value']: "No";

              /*

              <img class="sinTitulo" title="Descargar el documento escaneado" border="0" src="imagenes/descargar.png" width="13" height="13" onclick =  "abrirventanaflotante('vistas/descargarEscaneoTrab.php?id=<?php echo $escaneo ?>')">


              */


          }),

        );
      break;


    case 'listarPagoTerceroOrdenes':
        $_SESSION['fecha'] = Date("Y-m-d");
        // DB table to use
        $table = 'docpagotercero';
        // Table's primary key
        $primaryKey = 'docPagoTercero';
        $where = " docTercero =".$_GET["docTercero"];
        $joinQuery = "";

        $dt = 0;
        $columns = array(
          array('db' => 'docPagoTercero', 'dt' => $dt++),
          array('db' => 'tipoDocLiq', 'dt' => $dt++),
          array('db' => 'nroDocLiq', 'dt' => $dt++),
          array('db' => 'nroNotaCredito', 'dt' => $dt++),
          array('db' => 'nroFactMoy', 'dt' => $dt++),
          array('db' => 'fchCreacion', 'dt' => $dt++),
          array('db' => 'estado', 'dt' => $dt++),
          array('db' => 'docPagoTercero', 'dt' => $dt++,
            'formatter' => function( $d, $row ) {
              $fecha = $_SESSION['fecha'];
              $cadena = "";

             /* if (($_GET["edicion"] == "Si" && $row["estado"] != 'Inactivo') || $_GET["admin"] == "Si" ){
                $cadena .= "<img src='imagenes/hoja.png' width='15' height='15' class = 'descargarOCTercero' id=$d>";
            }*/

              if ($_GET["admin"] == "Si" &&  $row["estado"] != 'cancelado' )
                $cadena .= "<img src='imagenes/menos.png' width='15' height='15' class = 'eliminaOCTercero' id=$d>";
              return $cadena;
          }),
        );
      break;

      case 'listarPagoTerceroOrdenesTodas':
        $_SESSION['fecha'] = Date("Y-m-d");

        // DB table to use
        $table = 'docpagotercero';

        // Table's primary key
        $primaryKey = 'docPagoTercero';

        $where = "1";

        $joinQuery = " FROM (SELECT concat(UPPER(substring(`vehiculodueno`.tipoDocumento,1,2)),' ', `docTercero`,' ',  vehiculodueno.nombreCompleto) AS docTercero, `vehiculodueno`.tipoDocumento, `docPagoTercero`, `tipoDocLiq`, `nroDocLiq`, `fchDocLiq`,  `docpagotercero`.`estado`,  `nroNotaCredito`, `nroFactMoy`, `fchCancelacion`, `formaPago`, `nro_Oper_o_Cheque`, `docpagotercero`.`fchCreacion`, `docpagotercero`.`hraCreacion` FROM `docpagotercero`, vehiculodueno WHERE `docpagotercero`.docTercero = vehiculodueno.documento )  AS t1   ";

        $dt = 0;
        $columns = array(
          array('db' => 't1.docTercero', 'dt' => $dt++, 'field' => 'docTercero'),
          //array('db' => 't1.tipoDocumento', 'dt' => $dt++, 'field' => 'tipoDocumento'),
          array('db' => 't1.docPagoTercero', 'dt' => $dt++, 'field' => 'docPagoTercero'),
          array('db' => 't1.tipoDocLiq', 'dt' => $dt++, 'field' => 'tipoDocLiq'),
          array('db' => 't1.nroDocLiq', 'dt' => $dt++, 'field' => 'nroDocLiq'),
          array('db' => 't1.nroNotaCredito', 'dt' => $dt++, 'field' => 'nroNotaCredito'),
          array('db' => 't1.nroFactMoy', 'dt' => $dt++, 'field' => 'nroFactMoy'),
          array('db' => 't1.fchCreacion', 'dt' => $dt++, 'field' => 'fchCreacion'),
          array('db' => 't1.estado', 'dt' => $dt++, 'field' => 'estado'),
          array('db' => 't1.docPagoTercero', 'dt' => $dt++,  'field' => 'docPagoTercero',
            'formatter' => function( $d, $row ) {
              $fecha = $_SESSION['fecha'];

              $cadena = "";

              if (($_GET["edicion"] == "Si" && $row["estado"] != 'cancelado') || $_GET["admin"] == "Si" && $row["estado"] != 'cancelado'){
                $cadena .= "<img src='imagenes/lapiz.png' width='15' height='15' class = 'editaDocPagoTercero' id=$d>";
            //    $cadena .= "<img src='imagenes/lapiz.png' width='15' height='15' class = 'editaDocIdentidad' onclick =  \"abrirventanaflotante('index.php?controlador=trabajadores&accion=editaDocumIdentidad&ndt=$d&td=".$row["tipoDocTrab"]."') \"  >";

            }

              if ($_GET["admin"] == "Si" &&  $row["estado"] != 'cancelado' )
                $cadena .= "<img src='imagenes/menos.png' width='15' height='15' class = 'eliminaOCTercero' id=$d>";            
              return $cadena;

          }),

        );
      break;

      
      case 'listarPagoTerceroPrincipalTodas':

        $_SESSION['fecha'] = Date("Y-m-d");

        // DB table to use
        $table = 'despachovehiculotercero';

        // Table's primary key
        $primaryKey = 'fchDespacho';
        //$where = " 1 ";

        //$docTercero = $_GET["docTercero"];

        $where = "1"; // " `t1`.`docTercero` = '$docTercero'";

        $joinQuery = " FROM (SELECT  concat( `desvehter`.`fchDespacho`,'-' , `desvehter`.`correlativo`,'-0','-','Despacho','-',costoDia  ) AS id,  `desvehter`.`docTercero`, `desvehter`.`fchDespacho`, `desvehter`.`correlativo`, concat(`desvehter`.`correlativo`,'-0') AS correl02 , 'Despacho' AS tipo, `desvehter`.`placa`, `despacho`.`idCliente`, `cliente`.`nombre`, `despacho`.cuenta, `despacho`.m3, `despacho`.tipoDestino, `despacho`.ptoDestino, `desvehter`.`costoDia`, `desvehter`.`fchPago`, `desvehter`.`pagado`, `desvehter`.docPagoTercero, despacho.observacion, `desvehter`.`docPagoTerTipo`,

         GROUP_CONCAT(CONCAT(substring(concat(`trabajador`.`apPaterno`,' ',`trabajador`.`apMaterno`, ' ', trabajador.nombres),1, 23),' ', `despachopersonal`.tipoRol,'->',' ', trabajador.categTrabajador) SEPARATOR '<br>') as tripulacion

         , SUM(if(`despachopersonal`.tipoRol = 'Conductor' AND `despachopersonal`.idTrabajador != '00000001' ,1,0)) AS cantConductor, SUM(if(`despachopersonal`.tipoRol = 'Auxiliar',1,0)) AS cantAuxiliar, clientecuentaproducto.nombProducto FROM `despachovehiculotercero` AS desvehter, `cliente`, clientecuentaproducto, `despacho`
          LEFT JOIN despachopersonal ON despacho.fchDespacho = despachopersonal.fchDespacho AND  despacho.correlativo = despachopersonal.correlativo  AND `despachopersonal`.`tipoRol` != 'Coordinador' 
          LEFT JOIN trabajador ON despachopersonal.idTrabajador = trabajador.idTrabajador
          WHERE despacho.idProducto = clientecuentaproducto.idProducto AND `desvehter`.`fchDespacho` = `despacho`.`fchDespacho` AND `desvehter`.`correlativo` = `despacho`.`correlativo` AND `desvehter`.`pagado` = 'No' AND `despacho`.`idCliente` = `cliente`.`idRuc`   GROUP BY `desvehter`.fchDespacho, `desvehter`.correlativo

          UNION

          SELECT 'id' AS id, idTercero AS docTercero,  ocuter.`fchDespacho`, ocuter.`correlativo`, concat(ocuter.`correlativo`,'-', `corrOcurrencia`) AS correl02 , ocuter.`tipoOcurrencia`, des.placa, des.idCliente, cliente.nombre, des.cuenta, des.m3, `des`.tipoDestino, `des`.ptoDestino, ocuter.`montoTotal`, 'fchPago' AS fchPago, 'pagado' AS pagado, ocuter.docPagoTercero, ocuter.`descripcion`,  `ocuter`.`docPagoTerTipo`, `tipoConcepto`, 'cantConductor', 'cantAuxilar', 'nombProducto' FROM `ocurrenciatercero` AS ocuter, despacho AS des, cliente WHERE des.fchDespacho = ocuter.fchDespacho AND des.correlativo = ocuter.correlativo AND des.idCliente = cliente.idRuc  
           ) AS t1   ";

        $dt = 0;
        $columns = array(
          array('db' => 't1.fchDespacho', 'dt' => $dt++, 'field' => 'fchDespacho'),
          //array('db' => 't1.prueba', 'dt' => $dt++, 'field' => 'prueba'),
          array('db' => 't1.correl02', 'dt' => $dt++, 'field' => 'correl02'),
          array('db' => 't1.tipo', 'dt' => $dt++, 'field' => 'tipo'),
          array('db' => 't1.placa', 'dt' => $dt++, 'field' => 'placa'),
          array('db' => 't1.nombre', 'dt' => $dt++, 'field' => 'nombre'),
          array('db' => 't1.cuenta', 'dt' => $dt++, 'field' => 'cuenta'),
          array('db' => 't1.nombProducto', 'dt' => $dt++, 'field' => 'nombProducto'),
          array('db' => 't1.tripulacion', 'dt' => $dt++, 'field' => 'tripulacion'),
          array('db' => 't1.observacion', 'dt' => $dt++, 'field' => 'observacion'),
          /*array('db' => 't1.costoDia',
                  'dt' => $dt++,
                  'field' => 'costoDia',
                  'formatter' => create_function('$d,$row', 'return number_format($d,"2", ".", ",");')
                ),*/
          array('db' => 't1.costoDia', 'dt' => $dt++,'field' => 'costoDia',
            'formatter' => function( $d, $row ) {

              $cadena = "<span class = 'monto dblClicMonto'>". number_format($d,"2", ".", ",") ."</span><input class = 'editarMonto' type = 'text' style='padding: 3px; display: none;width: 55px;' value = '".number_format($d,"2", ".", ",")."'><img class = 'icoGuardar' src='imagenes/guardar.png' width='15' height= '15' style='display: none;'>" ;

              return $cadena;

          }),
          array('db' => 't1.docPagoTercero', 'dt' => $dt++, 'field' => 'docPagoTercero'),
          array('db' => 't1.docPagoTerTipo', 'dt' => $dt++, 'field' => 'docPagoTerTipo'),
          array('db' => 't1.docTercero', 'dt' => $dt++, 'field' => 'docTercero'),


        );

      break;




      case 'listarPagoTerceroMiscelaneosTodas':
        $_SESSION['fecha'] = Date("Y-m-d");

        // DB table to use
        $table = 'liqterceromisc';

        // Table's primary key
        $primaryKey = 'id';
        //$where = " 1 ";

        //$docTercero = $_GET["docTercero"];

        $where = "1"; // " `t1`.`docTercero` = '$docTercero'";

        $joinQuery = " FROM (SELECT `id`, `fchEvento`, `liqterceromisc`.`idPlaca`, `liqterceromisc`.`descripcion`, `monto`, `liqterceromisc`.tipoMisc , `liqterceromisc`.docPagoTercero, `liqterceromisc`.docAnexo, `liqterceromisc`.`docPagoTerTipo` , vehiculo.rznSocial AS docTercero FROM `liqterceromisc`, `vehiculo`  WHERE  `liqterceromisc`.`idPlaca` = `vehiculo`.`idPlaca` )  AS t1   ";

        $dt = 0;
        $columns = array(
          array('db' => 't1.fchEvento', 'dt' => $dt++, 'field' => 'fchEvento'),
          array('db' => 't1.idPlaca', 'dt' => $dt++, 'field' => 'idPlaca'),
          array('db' => 't1.descripcion', 'dt' => $dt++, 'field' => 'descripcion'),
          array('db' => 't1.id', 'dt' => $dt++, 'field' => 'id'),
          array('db' => 't1.tipoMisc', 'dt' => $dt++, 'field' => 'tipoMisc'),
          array('db' => 't1.monto', 'dt' => $dt++,'field' => 'monto',
            'formatter' => function( $d, $row ) {

              $cadena = "<span class = 'monto'>". number_format($d,"2", ".", ",") ."</span><input class = 'editarMonto' type = 'text' style='padding: 3px; display: none;width: 55px;' value = '".number_format($d,"2", ".", ",")."'>" ;

              return $cadena;

          }),
          array('db' => 't1.docPagoTercero', 'dt' => $dt++, 'field' => 'docPagoTercero'),
          array('db' => 't1.docPagoTerTipo', 'dt' => $dt++, 'field' => 'docPagoTerTipo'),
          array('db' => 't1.docTercero', 'dt' => $dt++, 'field' => 'docTercero'),


        );
      break;

      case 'listarPagoTerceroAbastOtrosTodas':
        $_SESSION['fecha'] = Date("Y-m-d");

        // DB table to use
        $table = 'combustible';

        // Table's primary key
        $primaryKey = 'idPlaca';
        //$where = " 1 ";k

        $where = "1"; // " `t1`.`docTercero` = '$docTercero'";

        $joinQuery = " FROM (SELECT `ccmovimencab`.`fchEvento` AS fecha, `ccmovimientos`.`idPlaca`,  `ccmovimientos`.`descripcion`, `ccmovimencab`.`tipoDoc`, `ccmovimencab`.`nroDoc`, `ccmovimientos`.`monto`, concat(`ccmovimientos`.nroOrden, '@' ,`ccmovimientos`.correlativo) AS idAux, `ccmovimientos`.`docPagoTercero`, `ccmovimientos`.`docPagoTerTipo`, `vehiculo`.`rznSocial` AS docTercero FROM `vehiculo`, `ccmovimencab`, `ccmovimientos` LEFT JOIN `ccitem` ON `ccmovimientos`.`item` = `ccitem`.`idItem` WHERE `ccmovimencab`.idMovimEncab = `ccmovimientos`.nroOrden AND `ccmovimientos`.`idPlaca` = `vehiculo`.`idPlaca` AND `vehiculo`.`rznSocial` != 'INVERSIONES MOY S.A.C.' 
          UNION
          SELECT `combustible`.`fchCreacion` AS fecha,  `combustible`.`idPlaca`, concat(`chofer`, ' ', `grifo`) AS descripcion, 'Vale' AS tipoDoc, nroVale AS nroDoc ,  `galones`*`precioGalon` as `monto`, concat(combustible.fchCreacion,'@', combustible.hraCreacion,'@', combustible.idPlaca) As idAux , `combustible`.`docPagoTercero`, `combustible`.`docPagoTerTipo`, `vehiculo`.`rznSocial` AS docTercero FROM `combustible`, `vehiculo` WHERE `combustible`.`idPlaca` = `vehiculo`.`idPlaca` AND `vehiculo`.`rznSocial` != 'INVERSIONES MOY S.A.C.' )  AS t1   ";

        $dt = 0;
        $columns = array(
          array('db' => 't1.fecha', 'dt' => $dt++, 'field' => 'fecha'),
          array('db' => 't1.idPlaca', 'dt' => $dt++, 'field' => 'idPlaca'),
          array('db' => 't1.descripcion', 'dt' => $dt++, 'field' => 'descripcion'),
          array('db' => 't1.tipoDoc', 'dt' => $dt++, 'field' => 'tipoDoc'),
          array('db' => 't1.nroDoc', 'dt' => $dt++, 'field' => 'nroDoc'),
          array('db' => 't1.monto', 'dt' => $dt++,'field' => 'monto',
            'formatter' => function( $d, $row ) {
              $cadena = "<span class = 'monto'>". number_format($d,"2", ".", ",") ."</span><input class = 'editarMonto' type = 'text' style='padding: 3px; display: none;width: 55px;' value = '".number_format($d,"2", ".", ",")."'>" ;
              return $cadena;
          }),

          array('db' => 't1.idAux', 'dt' => $dt++, 'field' => 'idAux'),
          array('db' => 't1.docPagoTercero', 'dt' => $dt++, 'field' => 'docPagoTercero'),
          array('db' => 't1.docPagoTerTipo', 'dt' => $dt++, 'field' => 'docPagoTerTipo'),
          array('db' => 't1.docTercero', 'dt' => $dt++, 'field' => 'docTercero'),

        );
      break;

      case 'listarDespachos':
        $_SESSION['fecha'] = Date("Y-m-d");
        $estadoDespacho = $_GET["estadoDespacho"];
        // DB table to use
        $table = 'despacho';
        // Table's primary key
        $primaryKey = 'fchDespacho';
        //$where = " docTercero =".$_GET["docTercero"];
        $joinQuery = "";

        $where = " 1 AND estadoDespacho = '$estadoDespacho' ";
        if($estadoDespacho == 'Terminado') $where .= "AND concluido = 'No' ";
        else if($estadoDespacho == 'Validado') $where = $where = " 1 AND estadoDespacho = 'Terminado' AND concluido = 'Si' ";
        $joinQuery = " FROM ( SELECT desp.`idProgramacion`, desp.`fchDespacho`, desp.`correlativo`, `hraInicio`, `fchDespachoFinCli`, `hraFin`, `placa`, movilAsignado, usuarioAsignado ,  desp.`idCliente`, `cuenta`, `correlCuenta`, `tipoServicioPago`, cli.nombre, concat(desp.idCliente ,'-',cli.nombre ) AS nombCliente, estadoDespacho, clicuepro.nombProducto, desp.concluido, desp.fchCreacion, desp.usuario FROM `despacho` AS desp LEFT JOIN cliente cli ON desp.idCliente = cli.idRuc LEFT JOIN clientecuentaproducto AS clicuepro ON clicuepro.idProducto = desp.idProducto  ) AS t1 ";

        $dt = 0;
        $columns = array(
          //if($estadoDespacho == 'Programado')
          array('db' => 't1.idProgramacion', 'dt' => $dt++, 'field' => 'idProgramacion'),
          array('db' => 't1.idProgramacion', 'dt' => $dt++, 'field' => 'idProgramacion'),
          array('db' => 't1.fchDespacho', 'dt' => $dt++, 'field' => 'fchDespacho'),
          array('db' => 't1.correlativo', 'dt' => $dt++, 'field' => 'correlativo'),
          array('db' => 't1.placa', 'dt' => $dt++, 'field' => 'placa'),
          array('db' => 't1.movilAsignado', 'dt' => $dt++, 'field' => 'movilAsignado'),
          array('db' => 't1.usuarioAsignado', 'dt' => $dt++, 'field' => 'usuarioAsignado'),

          array('db' => 't1.nombCliente', 'dt' => $dt++, 'field' => 'nombCliente'),
          array('db' => 't1.cuenta', 'dt' => $dt++, 'field' => 'cuenta'),
          array('db' => 't1.nombProducto', 'dt' => $dt++, 'field' => 'nombProducto'),
          //array('db' => 't1.correlCuenta', 'dt' => $dt++, 'field' => 'correlCuenta'),
          array('db' => 't1.tipoServicioPago', 'dt' => $dt++, 'field' => 'tipoServicioPago'),
          array('db' => 't1.fchCreacion', 'dt' => $dt++, 'field' => 'fchCreacion'),
          array('db' => 't1.usuario', 'dt' => $dt++, 'field' => 'usuario'),
          array('db' => 't1.idProgramacion', 'dt' => $dt++, 'field' => 'idProgramacion',
            'formatter' => function( $d, $row ) {
              $fecha = $_SESSION['fecha'];
              $cadena = "";
              if (($_GET["edicion"] == "Si" || $_GET["admin"] == "Si") && ($_GET["estadoDespacho"] == 'Terminado' || $_GET["estadoDespacho"] == 'Validado') ){
                $cadena .= "<img src='imagenes/lapiz.png' width='13' height='13' class = 'editarDespacho' fchDespacho = '".$row["fchDespacho"]."' correlativo = '".$row["correlativo"]."' id=$d>";

                $cadena .= "&nbsp; <img src='imagenes/notas.png' width='13' height='13' class = 'ocurrenciaDespacho' fchDespacho = '".$row["fchDespacho"]."' correlativo = '".$row["correlativo"]."' id=$d>";

              };

              $cadena .= "&nbsp; <img src='imagenes/hoja.png' width='13' height='13' class = 'descargaInfoDespacho'  fchDespacho = '".$row["fchDespacho"]."' correlativo = '".$row["correlativo"]."' id=$d>";
              

              if (($_GET["admin"] == "Si" ) && $_GET["estadoDespacho"] == 'Terminado' )
                $cadena .= "&nbsp; <img src='imagenes/menos.png' width='13' height='13' class = 'eliminarDespacho'  fchDespacho = '".$row["fchDespacho"]."' correlativo = '".$row["correlativo"]."' id=$d>";

              if($_GET["estadoDespacho"] == 'Programado'){
                $cadena .= "&nbsp; <img src='imagenes/enRuta2.png' width='13' height='13' class = 'pasarAEnRuta' id=$d>";
              }

              if($_GET["estadoDespacho"] == 'EnRuta'){
                $cadena .= "&nbsp; <img src='imagenes/cerrarForzoso.png' width='13' height='13' class = 'cerrarForzoso' id=$d>";
              }

              return $cadena;
          }),
        );
      break;

      case 'nuevolistarVehiculos':
        $_SESSION['fecha'] = Date("Y-m-d");

        // DB table to use
        $table = 'vehiculo';

        // Table's primary key
        $primaryKey = 'idPlaca';
        $where = " 1 ";

        if (!empty($_GET['columns'][3]['search']['value'])){
          $where = " vehicu.estado = '".$_GET['columns'][3]['search']['value']."'";
        };

        $joinQuery = "FROM (SELECT vehiculo.idPlaca, vehiculo.nroVehiculo, vehiculo.propietario, vehiculo.estado, vehiculodueno.nombreCompleto, vehiculo.pesoUtil, vehiculo.pesoUtilReal, (vehiculo.dimInteriorLargo * vehiculo.dimInteriorAncho * vehiculo.dimInteriorAlto) AS capCombustible, vehiculo.m3Facturable, vehiculo.usuario, vehiculo.fchCreacion FROM vehiculo left join vehiculodueno ON vehiculo.rznSocial = vehiculodueno.documento) AS vehicu";

        $dt = 0;
        $columns = array(
          array('db' => 'vehicu.nroVehiculo', 'dt' => $dt++, 'field' => 'nroVehiculo'),
          array('db' => 'vehicu.idPlaca', 'dt' => $dt++, 'field' => 'idPlaca'),
          array('db' => 'vehicu.propietario', 'dt' => $dt++, 'field' => 'propietario'),
          array('db' => 'vehicu.estado', 'dt' => $dt++, 'field' => 'estado'),
          array('db' => 'vehicu.nombreCompleto', 'dt' => $dt++, 'field' => 'nombreCompleto'),
          array('db' => 'vehicu.pesoUtil', 'dt' => $dt++, 'field' => 'pesoUtil'),
          array('db' => 'vehicu.pesoUtilReal', 'dt' => $dt++, 'field' => 'pesoUtilReal'),
          array('db' => 'vehicu.capCombustible', 'dt' => $dt++, 'field' => 'capCombustible','formatter' => create_function('$d,$row', 'return number_format($d,"2", ".", ",");')),
          array('db' => 'vehicu.m3Facturable', 'dt' => $dt++, 'field' => 'm3Facturable'),
          array('db' => 'vehicu.usuario', 'dt' => $dt++, 'field' => 'usuario'),
          array('db' => 'vehicu.fchCreacion', 'dt' => $dt++, 'field' => 'fchCreacion'),          
          array('db' => 'vehicu.idPlaca', 'dt' => $dt++,'field' => 'idPlaca', 'formatter' => function( $d, $row ) {
            $fecha = $_SESSION['fecha'];

              $cadena = "";

              if ($_GET["edicion"] == "Si"){
                $cadena .= "<img src='imagenes/lapiz.png' width='15' height='15' onclick =  \"abrirventana('index.php?controlador=vehiculos&accion=vervehiculo&id=$d') \"  >";
              }
              if ($_GET["admin"] == "Si"){
                $cadena .= "<img  src='imagenes/combustible.png' width='15' height='15' onclick =  \"abrirventana('index.php?controlador=vehiculos&accion=combustiblevehiculo&id=$d')\">";        
              }
              if ($_GET["admin"] == "Si"){
                $cadena .= "<img  src='imagenes/menos.jpg' width='15' height='15' onclick =  \"abrirventanaflotante('index.php?controlador=vehiculos&accion=eliminavehiculo&id=$d')\">";        
              }

              return $cadena;
          } ),
      );

      break;




      case 'listarPersonal':

        $_SESSION['fecha'] = Date("Y-m-d");
        //$estadoDespacho = $_GET["estadoDespacho"];
        // DB table to use
        $table = 'despachopersonal';

        // Table's primary key
        $primaryKey = 'fchDespacho';

        $fchDespacho = $_GET["fchDespacho"];
        $correlativo = $_GET["correlativo"];

        //$where = " docTercero =".$_GET["docTercero"];
        $joinQuery = " FROM ( SELECT fchDespacho, correlativo, trab.idTrabajador, desper.trabFchDespachoFin, tipoRol, concat(nombres,' ', apPaterno, ' ', apMaterno ) AS nombTrabaj, trabHraInicio, trabHraFin, desper.observPersonal  FROM `despachopersonal` AS desper , trabajador AS trab WHERE desper.idTrabajador = trab.idTrabajador  AND  `fchDespacho` = '$fchDespacho' AND `correlativo` = '$correlativo' ) AS t1 ";

        $where = " 1  ";

        $dt = 0;
        $columns = array(
          array('db' => 't1.fchDespacho', 'dt' => $dt++, 'field' => 'fchDespacho'),
          array('db' => 't1.correlativo', 'dt' => $dt++, 'field' => 'correlativo'),
          array('db' => 't1.idTrabajador', 'dt' => $dt++, 'field' => 'idTrabajador'),
          array('db' => 't1.nombTrabaj', 'dt' => $dt++, 'field' => 'nombTrabaj'),
          array('db' => 't1.tipoRol', 'dt' => $dt++, 'field' => 'tipoRol'),
          array('db' => 't1.trabHraInicio', 'dt' => $dt++, 'field' => 'trabHraInicio'),
          array('db' => 't1.trabFchDespachoFin', 'dt' => $dt++, 'field' => 'trabFchDespachoFin'),
          array('db' => 't1.trabHraFin', 'dt' => $dt++, 'field' => 'trabHraFin'),
          array('db' => 't1.observPersonal', 'dt' => $dt++, 'field' => 'observPersonal'),
          array('db' => 't1.idTrabajador', 'dt' => $dt++, 'field' => 'idTrabajador',
            'formatter' => function( $d, $row ) {
              $fecha = $_SESSION['fecha'];
              $cadena = "";

              $cadena .= "<img src='imagenes/lapiz.png' width='13' height='13' class = 'editar' id=$d  fchDespacho = '".$row['fchDespacho']."' correlativo = '".$row['correlativo']."' >";


              if ($_GET["admin"] == "Si" &&  $row["correlativo"] != 'cancelado' )
                $cadena .= "<img src='imagenes/menos.png' width='13' height='13' class = 'eliminar' id=$d  fchDespacho = '".$row['fchDespacho']."' correlativo = '".$row['correlativo']."' >";

            
              return $cadena;
          }),
        );

      break;

      case 'listarGuiasPorte':
        $_SESSION['fecha'] = Date("Y-m-d");
        // DB table to use
        $table = 'despachoguiaporte';

        // Table's primary key
        $primaryKey = 'fchDespacho';

        $fchDespacho = $_GET["fchDespacho"];
        $correlativo = $_GET["correlativo"];

        $where = " fchDespacho = '".$_GET["fchDespacho"]."' AND correlativo = '".$_GET["correlativo"]."'";

        $joinQuery = NULL;
        $dt = 0;

        $columns = array(
        array('db' => 'guiaPorte', 'dt' => $dt++),
        //array('db' => 'guiaPorte', 'dt' => $dt++),
        /*array('db' => 'marca', 'dt' => $dt++),
        array('db' => 'modelo', 'dt' => $dt++),
        array('db' => 'sim', 'dt' => $dt++),
        array('db' => 'imei', 'dt' => $dt++),*/
        array('db' => 'guiaPorte', 'dt' =>$dt++,
          'formatter' => function( $d, $row ) {
              //  return $d;
            return "<img border='0' src='imagenes/lapiz.png' class = 'editar' width='14' height='14' align='left' guiaPorte=$d><img border='0' src='imagenes/menos.png' class = 'eliminar' width='14' height='14' align='left' guiaPorte=$d>";
        }), 
      );
      break;

      case 'listarValTrip':
        $_SESSION['fecha'] = Date("Y-m-d");
        // DB table to use
        $table = 'despachopersonal';

        // Table's primary key
        $primaryKey = 'idTrabajador';

        $fchDespacho = $_GET["fchDespacho"];
        $correlativo = $_GET["correlativo"];

        
        $joinQuery = NULL;

        $joinQuery = " FROM ( SELECT fchDespacho, correlativo, trab.idTrabajador, desper.trabFchDespachoFin, tipoRol, concat(nombres,' ', apPaterno, ' ', apMaterno ) AS nombTrabaj, trabHraInicio, trabHraFin, valorRol, valorAdicional, valorHraExtra, desper.observPersonal  FROM `despachopersonal` AS desper , trabajador AS trab WHERE desper.idTrabajador = trab.idTrabajador  AND  `fchDespacho` = '$fchDespacho' AND `correlativo` = '$correlativo' ) AS t1 ";

        $where = " 1  ";

        $dt = 0;
        $columns = array(
          array('db' => 't1.fchDespacho', 'dt' => $dt++, 'field' => 'fchDespacho'),
          array('db' => 't1.correlativo', 'dt' => $dt++, 'field' => 'correlativo'),
          array('db' => 't1.idTrabajador', 'dt' => $dt++, 'field' => 'idTrabajador'),
          array('db' => 't1.nombTrabaj', 'dt' => $dt++, 'field' => 'nombTrabaj'),
          array('db' => 't1.tipoRol', 'dt' => $dt++, 'field' => 'tipoRol'),
          array('db' => 't1.valorRol', 'dt' => $dt++, 'field' => 'valorRol'),
          array('db' => 't1.valorAdicional', 'dt' => $dt++, 'field' => 'valorAdicional'),
          array('db' => 't1.valorHraExtra', 'dt' => $dt++, 'field' => 'valorHraExtra'),
          //array('db' => 't1.trabHraFin', 'dt' => $dt++, 'field' => 'trabHraFin'),
          array('db' => 't1.idTrabajador', 'dt' => $dt++, 'field' => 'idTrabajador',
            'formatter' => function( $d, $row ) {
              $fecha = $_SESSION['fecha'];
              $cadena = "";

              $cadena .= "<img src='imagenes/lapiz.png' width='13' height='13' class = 'valEditar' id=$d  f = '".$row['fchDespacho']."' c = '".$row['correlativo']."' >";


              if ($row["correlativo"] != 'cancelado' )
                $cadena .= "<img src='imagenes/menos.png' width='13' height='13' class = 'valEliminar' id=$d  fchDespacho = '".$row['fchDespacho']."' correlativo = '".$row['correlativo']."' >";

            
              return $cadena;
          }),
        );
      break;

//    case nroContrato
}

switch ($opcion) {
    case 'cargaCc':
    case 'listarTipoArticulo':
    case 'listarDetallesCobrar':
    case 'movFacturas':
    case 'listarVehiculos':
    case 'listarQuinOtrosConceptos':
    case 'listarLiquidacionesTrabajador':
    case 'despachoPuntos':
    case 'cargaCuentaPdtoOtros':
    case 'listarProgram':
    case 'listarTrabajadores':
    case 'listarPagoTerceroPrincipal':
    case 'listarPagoTerceroAbastOtros':
    case 'listarPagoTerceroMiscelaneos':
    case 'listarDocsIdentidad':
    case 'listarPagoTerceroOrdenes':
    case 'listarPagoTerceroOrdenesTodas':
    case 'listarPagoTerceroPrincipalTodas':
    case 'listarPagoTerceroMiscelaneosTodas':
    case 'listarPagoTerceroAbastOtrosTodas':
    case 'listarDespachos':
    case 'listarPersonal':
    case 'listarGuiasPorte':
    case 'nuevolistarVehiculos':
    case 'listarMercaderia':
    case 'listarChoque':
    case 'listarTelefonoR':
    case 'listarAbastecimiento':
    case 'listarPapeleta':
    case 'listarValTrip':
    case 'listarServicios':
    case 'listarPlacasCheckList':
    case 'listarRespuestasCheckList':
    case 'listarDetalleRespuestasCheckList':
    case 'listarUsuariosOcurrencia':
    case 'listarCotizaciones':
    case 'listarGestionSSTCovid':
    case 'listarDataOcurrencia':
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