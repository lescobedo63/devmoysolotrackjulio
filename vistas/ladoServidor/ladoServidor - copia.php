<?php

/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how server-side processing can be implemented,
 * and probably shouldn't be used as the basis for a large complex system. It is suitable for simple use cases as for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-side processing requirements of DataTables.
 *
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
//require('../core/librerias/datatables1.10.4/ladoServidor/ssp.class.php');
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
        //array('db' => 'cobIdCliente', 'dt' => 10),
        array('db' => 'docc.cobIdCliente', 'dt' => 12,  'field' => 'cobIdCliente',
            'formatter' => function( $d, $row ) {
              //  return $d;
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
//echo "entre";

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
//echo "entre";

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

        //$where = " 1 ";

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

        // Array of database columns which should be read and sent back to DataTables.
        // The `db` parameter represents the column name in the database, while the `dt`
        // parameter represents the DataTables column identifier. In this case simple
        // indexes

        $columns = array(
            array('db' => 'qdoc.idConcepto', 'dt' => 0, 'field' => 'idConcepto'),
            array('db' => 'tra.idTrabajador', 'dt' => 1, 'field' => 'idTrabajador'),
            array('db' => 'tra.apPaterno', 'dt' => 2, 'field' => 'apPaterno'),
            array('db' => 'tra.apMaterno', 'dt' => 3, 'field' => 'apMaterno'),
            array('db' => 'tra.nombres', 'dt' => 4, 'field' => 'nombres'),
            array('db' => 'qdoc.idItem', 'dt' => 5, 'field' => 'idItem'),
            //array('db' => 'qdoc.monto', 'dt' => 6, 'field' => 'monto'),

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

       

        // Array of database columns which should be read and sent back to DataTables.
        // The `db` parameter represents the column name in the database, while the `dt`
        // parameter represents the DataTables column identifier. In this case simple
        // indexes

        $columns = array(
            array('db' => 'tipoOcurrencia', 'dt' => 0),
            array('db' => 'tipoConcepto', 'dt' => 1),
            array('db' => 'descripcion', 'dt' => 2),
            array('db' => 'estadoOcurrencia', 'dt' => 3),
            array('db' => 'creacUsuario', 'dt' => 4),
            array('db' => 'creacFch', 'dt' => 5),
            array('db' => 'tipoOcurrencia', 'dt' => 6,
            'formatter' => function( $d, $row ) {
            //  return $d;
            return "<img border='0' src='imagenes/lapiz.png' width='13' height='13' align='left' onclick =  \"abrirventanaflotantemediana('index.php?controlador=terceros&accion=editaTipoOcurrenciaTercero&id=$d&token=1nv')\"><img border='0' src='imagenes/menos.png' width='13' height='13' align='left' onclick =  \"abrirventanaflotante('index.php?controlador=terceros&accion=eliminaTipoOcurrenciaTercero&id=$d&token=1nv')\">";
            }),
            
          
        );

        break;
      }


      case 'despachoPuntos': {
        // DB table to use
        $table = 'despachopuntos';
        // Table's primary key
        $primaryKey = 'fchDespacho';
        $where = NULL;
        $fchDespacho = $_GET['fchDespacho'];
        $correlativo = $_GET['correlativo'];
        $where = " despun.fchDespacho = '$fchDespacho' AND despun.correlativo = '$correlativo' ";
        $joinQuery = " FROM despachopuntos despun ";

        //$joinQuery = "   FROM `despachopuntos` despun LEFT JOIN despacho des ON `des`.fchDespacho = despun.fchDespacho AND `des`.correlativo = despun.correlativo ";

        // Array of database columns which should be read and sent back to DataTables.
        // The `db` parameter represents the column name in the database, while the `dt`
        // parameter represents the DataTables column identifier. In this case simple indexes

        $columns = array(
            array('db' => 'despun.correlPunto', 'dt' => 0, 'field' => 'correlPunto'),
            array('db' => 'despun.tipoPunto', 'dt' => 1, 'field' => 'tipoPunto'),
            array('db' => 'despun.nombComprador', 'dt' => 2, 'field' => 'nombComprador'),
            array('db' => 'despun.distrito', 'dt' => 3, 'field' => 'distrito'),
            array('db' => 'despun.nroGuiaPorte', 'dt' => 4, 'field' => 'nroGuiaPorte'),
            array('db' => 'despun.estado', 'dt' => 5, 'field' => 'estado'),
            array('db' => 'despun.correlPunto', 'dt' => 6,'field' => 'correlPunto',
            'formatter' => function( $d, $row ) {
              //return "<span class = 'editar'>$d</span>";
              return "<span class = 'editar'><img border='0' src='imagenes/lapiz.png' width='13' height='13' align='left' ></span><span class = 'eliminar'><img border='0' src='imagenes/menos.png' width='13' height='13' align='left' ></span>";
            }),
          
        );

        break;
      }


      case 'cargaCuentaPdtoOtros': {

        // DB table to use
        $table = 'clientecuentapdtootros';

        // Table's primary key
        $primaryKey = 'idProducto';

        //$where = " 1 ";

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
      // DB table to use
      $table = 'programdespacho';

      // Table's primary key
      $primaryKey = 'id';
    
      /*$where = " `programdespacho`.idCliente = cliente.idRuc ";
      $joinQuery = " FROM (SELECT idRuc, concat(idRuc,'-',nombre) AS dataCliente FROM cliente ) AS cliente, `programdespacho` LEFT JOIN (SELECT programdesppers.idProgram, tipoRol, concat( programdesppers.idTrabajador,'-', nombres,' ', apPaterno,' ', apMaterno ) AS nombCompleto FROM programdesppers, trabajador WHERE programdesppers.idTrabajador = trabajador.idTrabajador  ) AS tripulacion ON tripulacion.idProgram = programdespacho.id  ";

      $groupBy = "id";

      */

      $where = " 1 ";
      $joinQuery = " FROM (SELECT id, fchDespacho, concat(`programdespacho`.idCliente, '-', cliente.nombre ) As idCliente,  `programdespacho`.cuenta,  `programdespacho`.nombProducto, concat(`programdespacho`.correlCuenta, '|', `programdespacho`.idProducto, '|', `programdespacho`.cuenta, '|', `programdespacho`.nombProducto, '|', `programdespacho`.tipoCuenta, '|', `programdespacho`.valorServicio ) As correlCuenta, placa, hraInicioEsperada, movilAsignado, GROUP_CONCAT( if(tipoRol = 'Conductor', nombCompleto,NULL)) AS idConductor,  group_concat(if(tipoRol = 'Auxiliar',  nombCompleto, NULL )  SEPARATOR '\n') AS idAuxiliares, estadoProgram, `programdespacho`.creacUsuario FROM cliente, `programdespacho` LEFT JOIN (SELECT programdesppers.idProgram, tipoRol, concat( programdesppers.idTrabajador,'-', nombres,' ', apPaterno,' ', apMaterno ) AS nombCompleto FROM programdesppers, trabajador WHERE programdesppers.idTrabajador = trabajador.idTrabajador  ) AS tripulacion ON tripulacion.idProgram = programdespacho.id WHERE `programdespacho`.idCliente = cliente.idRuc GROUP BY id ) AS t1   ";




      // Array of database columns which should be read and sent back to DataTables.
      // The `db` parameter represents the column name in the database, while the `dt`
      // parameter represents the DataTables column identifier. In this case simple
      // indexes

      $dt = 0;

      $columns = array(
        array('db' => 't1.id', 'dt' => $dt++, 'field' => 'id'),
        array('db' => 't1.idCliente', 'dt' => $dt++, 'field' => 'idCliente'),
        array('db' => 't1.fchDespacho', 'dt' => $dt++, 'field' => 'fchDespacho'),
      /*  array('db' => 't1.hraInicioEsperada', 'dt' => $dt++, 'field' => 'hraInicioEsperada'),
        array('db' => 't1.movilAsignado', 'dt' => $dt++, 'field' => 'movilAsignado'),
        array('db' => 't1.cuenta', 'dt' => $dt++, 'field' => 'cuenta'),
        array('db' => 't1.nombProducto', 'dt' => $dt++, 'field' => 'nombProducto'),
        array('db' => 't1.placa', 'dt' => $dt++, 'field' => 'placa'),
        array('db' => 't1.idConductor', 'dt' => $dt++, 'field' => 'idConductor'),
        array('db' => 't1.idAuxiliares', 'dt' => $dt++, 'field' => 'idAuxiliares'),
        array('db' => 't1.estadoProgram', 'dt' => $dt++, 'field' => 'estadoProgram'),
        array('db' => 't1.creacUsuario', 'dt' => $dt++, 'field' => 'creacUsuario'),
      //  array('db' => 'doccrel.montoPagado', 'dt' => 11, 'field' => 'montoPagado', 'formatter' => create_function('$d,$row', 'return number_format($d,"2", ".", ",");')),
        //array('db' => 'cobIdCliente', 'dt' => 10),
        array('db' => 't1.estadoProgram', 'dt' => $dt++,  'field' => 'estadoProgram',
            'formatter' => function( $d, $row ) {
              //return $d;
            if ($d == 'Procesado') $cadena = "";
            else $cadena = "<img border='0' src='imagenes/menos.png' class = 'eliminar' width='18' height='18' align='right'><img border='0' src='imagenes/lapiz.png' class = 'editar' width='18' height='18' align='right'>";
            return $cadena;
        }), 

        */
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
        array('db' => 'nombreCompleto', 'dt' => $dt++),
        array('db' => 'nroTelefono', 'dt' => $dt++),
        array('db' => 'eMail', 'dt' => $dt++),
        array('db' => 'estadoTercero', 'dt' => $dt++),
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
        array('db' => 'marca', 'dt' => $dt++),
        array('db' => 'modelo', 'dt' => $dt++),
        array('db' => 'sim', 'dt' => $dt++),
        array('db' => 'imei', 'dt' => $dt++),

        array('db' => 'estado', 'dt' => $dt++),
        array('db' => 'fchAdquisicion', 'dt' => $dt++),
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
    
    {
        echo json_encode(
              SSP2::simple($_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery,$where)
        );
        break;
    }

    case 'rentabPorDespacho':    //no se utiliza
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