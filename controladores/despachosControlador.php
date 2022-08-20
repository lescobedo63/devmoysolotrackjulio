<?php

function listar() {
	//Incluye el modelo que corresponde
	require 'modelos/despachosModelo.php';
	require 'modelos/clientesModelo.php';
  require 'librerias/config.ini.php';
  require 'librerias/varios.php';

  //Añadido por cambio en reporte
  require 'modelos/zonasModelo.php';
  require 'modelos/vehiculosModelo.php';


	//Le pide al modelo todos los items
	//$codigo = isset($_POST['txtCodigo'])?$_POST['txtCodigo']:NULL;

  $_SESSION['tipoCuenta'] = "";
  
  if (isset($_POST['txtCodigo']))	
	  $_SESSION['codigo'] = $_POST['txtCodigo'];
	$codigo = $_SESSION['codigo'];
	
	$anhio = $mes = $dia = $correlativo = NULL;
	if ($codigo != NULL){
    $anhio = strtok($codigo, '-')==0?NULL:strtok($codigo, '-') ;
    $mes = strtok('-');
    $mes = $mes==0?NULL:$mes;
    $dia = strtok('-');
    $dia = $dia==0?NULL:$dia;
    $correlativo = strtok('-');
    $correlativo = $correlativo==0?NULL:$correlativo;  
  }
  //$fchFin = isset($_POST['txtFchFin'])?$_POST['txtFchFin']:NULL;
  
  if (isset($_POST['txtFchFin']))	
	  $_SESSION['fchFin'] = $_POST['txtFchFin'];
	$fchFin = $_SESSION['fchFin'];
  
	//$placa = isset($_POST['txtPlaca'])?$_POST['txtPlaca']:NULL;
	if (isset($_POST['txtPlaca']))	
	  $_SESSION['placa'] = $_POST['txtPlaca'];
	$placa = $_SESSION['placa'];
	
	if (isset($_POST['txtConductor'])){
    $aux = $_POST['txtConductor'];  
    $_SESSION['conductor'] = strtok($aux,'-');
  }
  $conductor = $_SESSION['conductor'];
  
  //$guiaPorte = isset($_POST['txtGuia'])?$_POST['txtGuia']:NULL;
  if (isset($_POST['txtGuia']))	
	  $_SESSION['guiaPorte'] = $_POST['txtGuia'];
	$guiaPorte = $_SESSION['guiaPorte'];
  
  
  if(isset($_POST['txtCliente'])){
    $aux = $_POST['txtCliente'];
    $_SESSION['ruc'] = strtok($aux,"-");
  }  
  $idRuc = $_SESSION['ruc'];
  //$_SESSION['cliente'] = $_SESSION['ruc']; //Para compatibilidad con listado para el cliente
  
  //$cuenta = isset($_POST['txtCuenta'])?$_POST['txtCuenta']:NULL;
  if (isset($_POST['txtCuenta']))	
	  $_SESSION['cuenta'] = $_POST['txtCuenta'];
	$cuenta = $_SESSION['cuenta'];

  if (isset($_POST['txtTipoCuenta'])) 
    $_SESSION['tipoCuenta'] = $_POST['txtTipoCuenta'];
  $tipoCuenta = $_SESSION['tipoCuenta'];

  if (isset($_POST['txtLiquid']))
      $_SESSION['liquid'] = $_POST['txtLiquid'];
  $liquid = $_SESSION['liquid'];

  if (isset($_POST['txtDocLiq']))
      $_SESSION['docLiq'] = $_POST['txtDocLiq'];
  $docLiq = $_SESSION['docLiq'];

	if ($codigo == NULL AND $placa == NULL AND $guiaPorte == NULL AND $cuenta == NULL AND $idRuc == NULL AND $conductor == NULL AND $liquid == null AND $docLiq == null ){
	 $items = buscarTodosLosDespachosDelDiaoNoAtendidos($db);
	 //$itemsSoloPersonal = buscarTodosLosDespachosSoloPersonalDelDiaoNoAtendidos($db);
   $nroItems = count($items);
   //$nroItemsSP = count($itemsSoloPersonal);
	} else {
	 //$guiaPorte = $_POST['txtGuia'];
   //$items = buscarDespachoGuia($db,$guiaPorte);
   // echo "PARAMETROS anhio $anhio, mes $mes, dia $dia, correlativo $correlativo, guiaPorte $guiaPorte, placa $placa,$conductor,$idRuc,$cuenta,$fchFin,$liquid,$docLiq";
   $items = buscarDespachos($db,$anhio,$mes,$dia,$correlativo,$guiaPorte,$placa,$conductor,$idRuc,$cuenta,$fchFin,$liquid,$docLiq, $tipoCuenta);
   
   $nroItems = count($items);
   /*if ($placa != NULL OR $conductor != NULL)
      $advertencia = "No aplicable";
   else
      $itemsSoloPersonal = buscarDespachosSoloPersonal($db,$anhio,$mes,$dia,$correlativo,$idRuc,$cuenta,$fchFin,$liquid,$docLiq);
      $nroItemsSP = isset($itemsSoloPersonal)?count($itemsSoloPersonal):0;
      */
  }

  ///Para probar compatibilidad

  $sedes = buscarSedes($db);
  $arraySedes = array();
  $arraySedes[""] = 'No definido'; 
  foreach ($sedes as $item) {
    $codigo = $item['idSede'];
    $sede   = $item['descripcion'];
    $arraySedes[$codigo] = $sede;
    $_SESSION['arraySedes'] = $arraySedes;
  }

  $ptosDestino =  buscarUbicaciones($db);
  $arrayDestinos = array();
  $arrayDestinos[""] = "No definido"; 
  foreach ($ptosDestino as $item) {
    $codigo  = $item['idUbicacion'];
    $destino = $item['descripcion'];
    $arrayDestinos[$codigo] = $destino;
    $_SESSION['arrayDestinos'] = $arrayDestinos;
  }

  $vehiculosActivos = buscarVehiculos($db," WHERE estado = 'Activo' ");
  $arrayVehiculos = array();
  foreach ($vehiculosActivos as $item) {
    $placa =  $item['idPlaca'];
    $m3 =  $item['dimInteriorAlto']*$item['dimInteriorLargo']*$item['dimInteriorAncho'];
    $arrayVehiculos[$placa] = $m3;
    $_SESSION['arrayVehiculos'] = $arrayVehiculos;
  }



  ////// FIn de para probar 
	$clientes = buscarTodosLosClientes($db,'activo');
	//Pasa a la vista toda la información que se desea representar
  
	require 'vistas/listarDespachos.php';
}

function verificar(){
	require 'vistas/listarVerificar.htm';	 
}

function verificarMarcas(){
	$anhio = $_POST['txtAnhioMarca'];
  $aux = $_POST['cmbMesResumenMarca'];
  if(empty($_POST['cmbMesResumenMarca'])){
    $mes = date('m');
    $quin = '1';
  } else {
    $aux = $_POST['cmbMesResumenMarca'];
    $mes = strtok($aux, '-');
    $quin = strtok('-');  
  }
  //echo "LLEGAO PAGADO ".$_POST['cmbPagado'];
  if(!isset($_POST['cmbPagado']) OR $_POST['cmbPagado'] == '-'  )
    $pagado = '-';
  else
    $pagado = $_POST['cmbPagado'];
    
  if(isset($_POST['txtTrabajador'])){
    $aux = $_POST['txtTrabajador'];
    $dni = strtok($aux, '-');
  } else {
    $aux = '';
    $dni = '';
  } 
	require 'modelos/despachosModelo.php';
  $items = buscarDespachosPorMarcaQuincena($db,$anhio,$mes,$quin,$pagado,$dni);  
	require 'vistas/listarQuincenaMarcas.php';	
}


function verificarConfirmadosQuincenaPorCuenta(){
  $anhio = $_POST['txtAnhioMarcaPorCuenta'];
  $aux = $_POST['cmbMesResumenMarcaCuenta'];
  if(empty($_POST['cmbMesResumenMarcaCuenta'])){
   $mes = date('m');
   $quin = '1';
  } else {
   $aux = $_POST['cmbMesResumenMarcaCuenta'];
   $mes = strtok($aux, '-');
   $quin = strtok('-');  
  }  
  if(isset($_POST['txtTrabajador'])){
    $aux = $_POST['txtTrabajador'];
    $dni = strtok($aux, '-');
  } else {
    $aux = '';
    $dni = '';
  }  
  $cliente = isset($_POST['txtCliente'])?$_POST['txtCliente']:'';
  $cuenta = isset($_POST['txtCuenta'])?$_POST['txtCuenta']:'';
  require 'modelos/despachosModelo.php';
	//echo "Año $anhio  Mes $mes  Quincena $quin ";
  $items = buscarDespachosConfirmadosPorCuenta($db,$anhio,$mes,$quin,$cliente,$cuenta);   
	require 'vistas/listarConfirmadosPorCuenta.php';	
}



/*
function ver() {
  $dni = $_GET['dni'];
  require 'modelos/conductoresModelo.php';
  $conductor = buscarConductor($db,$dni);
  $hijos = buscarConductorHijos($db,$dni);
  $altasbajas = buscarConductorIngresos($db,$dni);
  $telfAsignados = buscarConductorTelefonos($db,$dni);
  require 'vistas/verConductor.php';
}
*/
function finalizar(){
  require 'modelos/despachosModelo.php';
  require 'modelos/clientesModelo.php';
  $fchDespacho = $_GET['fchdespacho'];
  $correlativo = $_GET['correlativo'];
  $idCliente = $_GET['idCliente'];
  $nombreCliente = $_GET['nombreCliente'];
  $cuenta = $_GET['cuenta'];
  $uso =  $_GET['uso'];
  //$despacho = buscarDespacho($db,$fchDespacho,$correlativo);
  //$clienteCuentas =  buscarClienteCuentas($db,$idCliente);
  //$zonasripley = buscarZonasRipley($db);
  //require 'vistas/finalizaDespacho.htm';
  require 'editaDespacho.php';
}

function ocurrencia(){
  require 'modelos/despachosModelo.php';
  $placa  = $_GET['placa'];
  $fchDespacho = $_GET['fchdespacho'];
  $correlativo = $_GET['correlativo'];
  $cuenta = $_GET['cuenta'];
  $involucrados = buscarTodosInvolucrados($db,$fchDespacho,$correlativo);
  require 'vistas/nuevaOcurrencia.htm';
}

function eliminaDespacho(){
  require 'modelos/despachosModelo.php';
  $fchDespacho = $_GET['fchdespacho'];
  $correlativo = $_GET['correlativo'];
  $cliente = (isset($_GET['idCliente'])?$_GET['idCliente']:"");
  $nombre  = (isset($_GET['nombreCliente'])?$_GET['nombreCliente']:"");
  $cuenta = (isset($_GET['cuenta'])?$_GET['cuenta']:"");
  $placa  = (isset($_GET['placa'])?$_GET['placa']:$_POST['placa']);

  require 'vistas/eliminaDespacho.php';
}

function facturaContratoMes(){
  $cliente = $_GET['cliente'];
  require 'vistas/facturaContratoMes.htm';
}

function eliminaPrestamoPlanilla(){
  $idTrabajador= $_GET['idTrabajador'];
  $tipoItem =    $_GET['tipoItem'];
  $monto =       $_GET['monto'];
  $fchCreacion = $_GET['fchCreacion'];
  $descripcion = $_GET['descripcion'];
  require 'vistas/eliminaPrestamoPlanilla.htm';
}

function cargamasivarosen(){
  $fch = Date("Y-m-d");
  if(isset($_POST['btnEnviar'])){
    require 'modelos/despachosModelo.php';
    $nombre_archivo = $_FILES['userfile']['name'];
    $tipo_archivo = $_FILES['userfile']['type'];
    $tamano_archivo = $_FILES['userfile']['size'];
    $fch = isset($_POST['txtFch'])?$_POST['txtFch']:Date("Y-m-d");
    $nombre_archivo = "./adjuntos/".$nombre_archivo;
    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $nombre_archivo)){
      $abrir = fopen($nombre_archivo,'r+');
      $resultado = subirArchivo($db,$abrir);
      $arrayResult = explode("-",$resultado);
      $items = buscarSubidosRosen($db);
    } else {
      echo "Ocurrió algún error al subir el fichero. No pudo guardarse.";
      //break; //por php 7
    }
  }
  require 'vistas/cargaMasivaRosen.php';
}

function repoDiasTrabajados(){
  require 'modelos/despachosModelo.php';
  if (isset($_POST['txtFchIniDias']) && $_POST['txtFchIniDias'] != '')
    $fchIni = $_POST['txtFchIniDias'];
  else if ($_POST['txtFchIniDiasBarr'] != '')
    $fchIni = $_POST['txtFchIniDiasBarr'];
  else
    $fchIni = null;

  if (isset($_POST['txtFchFinDias']) && $_POST['txtFchFinDias'] != '')
    $fchFin = $_POST['txtFchFinDias'];
  else if ($_POST['txtFchFinDiasBarr'] != '')
    $fchFin = $_POST['txtFchFinDiasBarr'];
  else
    $fchFin = null;


  $auxTrabaj = isset($_POST['cmbTrabajador'])?$_POST['cmbTrabajador']:'';
  $arrTrabaj = explode("-",$auxTrabaj);
  $dni = $arrTrabaj[0];
  $oper = isset($_POST['cmbOper'])?$_POST['cmbOper']:"";
  $dias = isset($_POST['txtDias'])?$_POST['txtDias']:"";
  $categ = isset($_POST['txtCateg'])?$_POST['txtCateg']:"";

  $items = buscarDiasTrabajados($db,$fchIni,$fchFin,$dni,$oper,$dias,$categ);
  require 'vistas/listarDiasTrab.php';
}

function verCargaMasivaDespachos(){
  require 'modelos/despachosModelo.php';
  require 'modelos/clientesModelo.php';
  $cli = isset($_SESSION['idCliente'])?$_SESSION['idCliente']:"NoHayCliente";
  $nombCli = isset($_SESSION['nombCliente'])?$_SESSION['nombCliente']:"NoHayCliente";
  if ($cli != 'NoHayCliente'){
    $resultado = verificarCargaDespachos($db,$cli);
    $arrayResult = explode("-",$resultado);
  }
  $registrosCarga = buscarRegistrosCargaDespachos($db,'carga');
  require 'vistas/cargaMasivaDespachos.php';
}

function cargaMasivaDespachos(){

  require 'modelos/despachosModelo.php';
  require 'modelos/clientesModelo.php';

  $usuario = $_SESSION['usuario'];
  //require 'modelos/aplicacionModelo.php';
  if(isset($_POST['btnEnviar'])){
    if ($_POST['cmbCargaAnterior'] == 'Eliminar'){
      $instruccion = "DROP TABLE `_cargadespachos$usuario`";
      $nro = $db->exec($instruccion);
      $cont = 1;//Esto es para saber en que número inicia el contador
    } else {
      $nroRegistros = cantidadRegistrosCarga($db,"_cargadespachos$usuario"); 
      $cont = $nroRegistros + 1;
    }

    //Preparar la estructura dentro de la bds
    $instruccion =  "CREATE TABLE IF NOT EXISTS `_cargadespachos$usuario` (
      `nro` int(10) unsigned NOT NULL,
      `fchDespacho` date NOT NULL,
      `hraDespacho` time NOT NULL,
      `cuenta` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
      `placa` char(7) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
      `tempConductor` char(30) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
      `tempAux01` char(30) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
      `tempAux02` char(30) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
      `tempAux03` char(30) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
      `tempAux04` char(30) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
      `tempAux05` char(30) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
      `tempAuxReten` char(30) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
      `kmInicio` char(30) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
      `idCliente` char(11) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
      `conductor` char(30) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
      `aux01` char(11) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
      `aux02` char(11) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
      `aux03` char(11) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
      `aux04` char(11) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
      `aux05` char(11) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
      `auxReten`  char(11) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
      `obsFch` char(20) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
      `obsCuenta` char(20) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
      `obsPlaca` char(20) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
      `obsConductor` char(20) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
      `obsAux01` char(20) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
      `obsAux02` char(20) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
      `obsAux03` char(20) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
      `obsAux04` char(20) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
      `obsAux05` char(20) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
      `obsAuxReten` char(20) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
      `obsKmInicio` char(20) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
    $nro = $db->exec($instruccion);
    

    $nombre_archivo = $_FILES['userfile']['name'];
    $tipo_archivo = $_FILES['userfile']['type'];
    $tamano_archivo = $_FILES['userfile']['size'];
    //$fch = isset($_POST['txtFch'])?$_POST['txtFch']:Date("Y-m-d");
    //$responsable = $_POST['cmbRespons'];
    $nombre_archivo = "./adjuntos/".$nombre_archivo;
    //vaciarTablaCargaRendicion($db);
    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $nombre_archivo)){
      /** Error reporting */
      error_reporting(E_ALL);
      ini_set('display_errors', TRUE);
      ini_set('display_startup_errors', TRUE);
      date_default_timezone_set('Europe/London');
      define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
      date_default_timezone_set('America/Lima');
      /** Include PHPExcel_IOFactory */
      require_once  'librerias/classesPHPExcel/PHPExcel/IOFactory.php';
      $objReader = PHPExcel_IOFactory::createReader('Excel2007');
      $objReader->setReadDataOnly(true);
      $objPHPExcel = $objReader->load($nombre_archivo);
      $objWorksheet = $objPHPExcel->getActiveSheet();
      $linea = 'Primera';
      

      $cadenaInicio = "INSERT INTO `_cargadespachos$usuario` (`nro`, `fchDespacho`, `hraDespacho`, `cuenta`, `placa`, `tempConductor`, `tempAux01`, `tempAux02`, `tempAux03`, `tempAux04`, `tempAux05`, `tempAuxReten`,`kmInicio`, `idCliente`) VALUES ";
      $registros = $cadenaInicio;
      foreach ($objWorksheet->getRowIterator() as $row) {
        if ($linea == 'Primera'){
          $cellIterator = $row->getCellIterator();
          $cellIterator->setIterateOnlyExistingCells(false); // This loops all cells,
          $sirve = 'No';
          $cli = "";
          foreach ($cellIterator as $cell) {
            if ($sirve == "No" || $cli != ""){
              $sirve = "Si";
              continue;
            }
            $cli = utf8_decode($cell->getValue());
            $auxCliente = buscarCliente($db,$cli);
            foreach ($auxCliente as $itemCli) {
              $nombCli = $itemCli['nombre'];
            }
          }

          $linea = 'Segunda';
          continue;
        } else if ($linea == 'Segunda'){
          $linea = 'Tercera';
          continue;
        } else if ($linea == 'Tercera'){
          $linea = 'Demas';
          $data = "(";
        } else {
          $data = ",(";
        }
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false); // This loops all cells,
        $primerCampo = 'Si';
        
        foreach ($cellIterator as $cell) {
          $unDato = utf8_decode($cell->getValue());
          if ($primerCampo == 'Si'){
            $primerCampo = 'Segundo';
            $unDato =  PHPExcel_Style_NumberFormat::toFormattedString($unDato, "YYYY-mm-dd");  //FORMAT_DATE_TIME4 
            $data .= "'$cont','$unDato'";  
            $cont++;            
          } else {
            if ($primerCampo == 'Segundo'){
              $primerCampo = 'No';
              $unDato =  PHPExcel_Style_NumberFormat::toFormattedString($unDato, 'H:i:s');
            }
            $data .= ",'$unDato'";
          }          
        }
        if ($data != '(' && $data != ',('){
          //$data .= ")";
          //$registros .= "$data,'$responsable')";
          $registros .= "$data,'$cli')";
          if ($cont % 5 == 0){
            $registros .= ";";
            //echo $registros;
            insertarCargaDespachos($db,$registros); //Es una rutina generica
            $registros = $cadenaInicio;
            //$cont = 1;
            $linea = "Tercera";
          } 
        }
      }
      $registros .= ";";
      //echo $registros;
      insertarCargaDespachos($db,$registros); //Es una rutina generica
      //$_SESSION['resultado'] = completarCargaDespachos($db,$cli);
      completarCargaDespachos($db);
      $tbl = "_cargadespachos$usuario";
      verificaPredictivo($db,$tbl);
      $resultado = actualizaComentariosCargaDespachos($db,$cli);

      $arrayResult = explode("-",$resultado);
      
      $_SESSION['idCliente'] = $cli;
      $_SESSION['nombCliente'] =$nombCli;

    } else {
      echo "Ocurrió algún error al subir el fichero. No pudo guardarse.";
      //break; //por php 7
    }
  }
  echo "Espere unos momentos para ser redirigido<br><br>";
  $url = "index.php?controlador=despachos&accion=verCargaMasivaDespachos";
  require 'vistas/redireccionar.php';
 //Aqui se tiene que redirigir
}

function editarDocDetalles(){
  require 'modelos/despachosModelo.php';
  $idCliente = $_POST['idCliente'];
  $nroDoc = $_POST['nroDoc'];
  $tipoDoc = $_POST['tipoDoc'];
  $nroDetalles = $_POST['totItems'];

  for ($i=1; $i<= $nroDetalles ; $i++) {
    $cant = $_POST["txtCant$i"];
    $descrip = $_POST["txtDescrip$i"];
    $pUnit = $_POST["txtPUnit$i"];
    $pTot =  $_POST["txtPTot$i"];
    $checkPUnit =  $_POST["chkMostrar$i"];
    if ($pTot == "")
      if ($cant != "")
        $pTot = $pUnit*$cant;
      else
        $pTot = $pUnit;

    if ($pUnit == "")
      if ($cant != "")
        $pUnit = $pTot/$cant;
      else
        $pUnit = $pTot;    
    actualizaDocDetalle($db,$nroDoc,$tipoDoc,$i,$cant,$descrip,$pUnit,$checkPUnit);
    //echo "$i ".$_POST["txtCant$i"]." ".$_POST["txtDescrip$i"]." ".$_POST["txtPUnit$i"]."<br>";
  }
  $url = "index.php?controlador=clientes&accion=cobrar&idCliente=$idCliente";
  require 'vistas/redireccionar.php';
}

function editarObserv(){
  require 'modelos/despachosModelo.php';
  $idCliente = $_POST['idCliente'];
  //$nroDoc = $_POST['nroDoc'];
  //$tipoDoc = $_POST['tipoDoc'];
  $nroItems = $_POST['totItems'];

  for ($i=1; $i<= $nroItems ; $i++) {
    $fchDespacho = $_POST["fchDespacho$i"];
    $corr = $_POST["corr$i"];
    $observ = $_POST["txtObserv$i"];
    echo "$fchDespacho $corr $observ ";   
    actualizaObserv($db,$fchDespacho,$corr,$observ);
    //echo "$i ".$_POST["txtCant$i"]." ".$_POST["txtDescrip$i"]." ".$_POST["txtPUnit$i"]."<br>";
  }
  $url = "index.php?controlador=clientes&accion=cobrar&idCliente=$idCliente";
  require 'vistas/redireccionar.php';
}

function almacenarCargaMasivaDespachos(){
  require 'modelos/despachosModelo.php';
  $idCliente = $_REQUEST['id'];    
  echo renombrarTablaDespacho($db,$idCliente);
  $_SESSION['idCliente'] = "NoHayCliente";
  $_SESSION['nombCliente'] = "NoHayCliente";
  $url = "index.php?controlador=despachos&accion=verCargaMasivaDespachos";
  require 'vistas/redireccionar.php';
}

function administrarCargaMasivaDespachos(){
  require 'modelos/despachosModelo.php';
  require 'modelos/clientesModelo.php';
  $revisarItems = NULL;
  if (isset($_GET['id'])){
    $idTbl = $_GET['id'];
    $_SESSION['idCarga'] = $_GET['auxCarga'];
    $_SESSION['idClienteCarga'] = $_GET['aux'];
    $revisarItems = buscarItemsCarga($db,$idTbl);
  }
  $ultimasCargas= buscarUltimasCargasDespachos($db);
  require 'vistas/administrarCargaDespachos.php';
}


function predictivoCarga(){
  require 'modelos/despachosModelo.php';  
  $id = $_GET['id'];
  $auxCarga = $_GET['auxCarga'];

  verificaPredictivo($db,$id);
  $url = "index.php?controlador=despachos&accion=administrarCargaMasivaDespachos&id=$id&auxCarga=$auxCarga&aux=".$_SESSION['idClienteCarga'];
  require 'vistas/redireccionar.php';
}

function generarCargaDespachos(){
  require 'librerias/config.ini.php';
  require 'modelos/clientesModelo.php';
  require 'modelos/despachosModelo.php';  
  $id = $_GET['id'];
  echo procesaNuevosDespachos($db,$porcIgv,$id);
  $url = "index.php?controlador=despachos&accion=administrarCargaMasivaDespachos";
 // $url = "index.php?controlador=despachos&accion=administrarCargaMasivaDespachos&id=$id&aux=".$_SESSION['idClienteCarga'];
  require 'vistas/redireccionar.php';

}

function eliminaCargaMasiva(){
  require 'modelos/despachosModelo.php';
  $id = $_GET['id'];
  $idCarga = $_GET['auxCarga'];

  echo eliminaTablaDeCargaDespachos($db,$id,$idCarga);
  $url = "index.php?controlador=despachos&accion=administrarCargaMasivaDespachos";
  require 'vistas/redireccionar.php';

}

function diasTrabPorQuincena(){
  require 'librerias/varios.php';
  require 'modelos/despachosModelo.php';

  $auxFchIni = restaDias(120);
  $fchIni =  isset($_POST['txtFchIni'])?$_POST['txtFchIni']:substr($auxFchIni, 0,8)."14";
  $fchFin =  isset($_POST['txtFchFin'])?$_POST['txtFchFin']:substr(Date("Y-m-d"), 0,8)."14";

  $_SESSION['fchIni'] = $fchIni;
  $_SESSION['fchFin'] = $fchFin;

  $itemTodasQuincenas = buscarQuincenas($db);
  $itemQuincenas = buscarQuincenas($db,$fchIni,$fchFin);
  $items = buscarDiasTrabPorQuincena($db, $fchIni, $fchFin, $itemQuincenas);

  /*echo "<pre>";
  print_r($items);
  echo "</pre>";*/
  require 'vistas/repoDiasTrabPorQuincena.php';

}

function programacionprev(){
  require 'barramenu.php';
  $estado = "programacion";
  require 'vistas/movilListarDespachosTripulacion.php';
}

function nuevoDespacho(){
  //require 'librerias/varios.php';
  require 'librerias/config.ini.php';
  require 'modelos/despachosModelo.php';
  require 'modelos/trabajadoresModelo.php';
  require 'modelos/clientesModelo.php';
  require 'modelos/vehiculosModelo.php';

  $cliente = $_POST['cmbCliente'];
  $idCliente = strtok($cliente, ':');
  //$nombreCliente = strtok(':'); si lo deseo usar luego
  $fchDespacho = $_POST['txtFchDespacho'];
  $hraDespacho = $_POST['txtHraDespacho'];
  $verificaDespacho = buscarExisteOtroDespacho($db,$idCliente,$fchDespacho,$hraDespacho);
  foreach ($verificaDespacho as $item){
    $cantidad = $item['cantidad'];
  }

  $clienteCuentas = buscarClientesCuentas02($db, $idCliente, NULL, 'Activo');  
  $auxReten = buscarTodosLosAuxiliaresConAlerta($db,$fchDespacho,$hraDespacho);
  $vehiculos = buscarTodosLosVehiculosActivos($db, $bloquearVehiculos);

  /*
  echo "<pre>";
  print_r($clienteCuentas);
  echo "</pre>";
  */
  require 'vistas/nuevoDespacho.php';
}

function editaDespacho(){
  require 'librerias/varios.php';
  require 'librerias/config.ini.php';
  require 'modelos/despachosModelo.php';
  require 'modelos/trabajadoresModelo.php';
  require 'modelos/clientesModelo.php';
  require 'modelos/vehiculosModelo.php';
  require 'modelos/zonasModelo.php';

  $kmIni = $kmIniCliente = $kmFin = $kmFinCliente = 0;
  $hraFinCli = '00:00:00';
  $lugarFinCliente = null; 
  $fchDespacho = $_GET['fchDespacho'];
  $correlativo = $_GET['correlativo'];

  $tipoDestino = "";

  //Verifica los Pagos
  $auxVerificar = buscarDespachoDetallesTipoRol($db,$fchDespacho,$correlativo,'Auxiliar');
  $paraPagarPlanilla = 'No';
  //$confirmado = 'No';
  foreach($auxVerificar as $aux){
    /*
    if ($aux['pagado']== 'Si' OR $aux['pagado']== 'Md' ) $paraPagarPlanilla = 'Si';
    if ($aux['pagado']== 'Si') $confirmado = 'Si';
    */
    if ($aux['pagado']== 'Md' && $paraPagarPlanilla == 'No' ) $paraPagarPlanilla = 'Md';
    else if ($aux['pagado'] == 'Si') $paraPagarPlanilla = 'Si';

  }

  $auxVerificar = buscarDespachoDetallesTipoRol($db,$fchDespacho,$correlativo,'Conductor');
  $paraPagarPlanilla = 'No';
  foreach($auxVerificar as $aux){
    if ($aux['pagado']== 'Md' && $paraPagarPlanilla == 'No' ) $paraPagarPlanilla = 'Md';
    else if ($aux['pagado'] == 'Si') $paraPagarPlanilla = 'Si';
  }

  //fin de ...
  $uso =  $_GET['uso'];
  $datosDespacho = buscarInfoDespacho($db,$fchDespacho,$correlativo);
  $datosTripulacion = buscarTodosInvolucrados($db,$fchDespacho, $correlativo);

  $guiasPorte = buscarGuiasPorte($db,$fchDespacho,$correlativo);
  $guiasP = '';
  foreach($guiasPorte as $dato){
    $guiasP =  $guiasP." ".$dato['guiaPorte'];
  }
  $guiasP = trim($guiasP);

  foreach ($datosDespacho as $item) {
    $idCliente = $item['idCliente'];
    $nombreCliente = $item['nombre'];
    $correlCuenta = $item['correlCuenta'];
    $tipoServicioPago = $item['tipoServicioPago'];
    $valorServicio = $item['valorServicio'];
    $valorServicioHraExtra = $item['costoHraExtra'];
    $topeServicioHraNormal = $item['topeServicioHraNormal'];
    $kmEsperado =  $item['recorridoEsperado'];
    $valorAuxAdicional =  $item['valorAuxAdicional'];
    $tipoDocCobrar =   $item['tipoDocCobrar'];
    $hraInicio = $item['hraInicio'];
    $hraInicioBase = $item['hraInicioBase'];
    $hraFinCliente = $item['hraFinCliente'];
    $usaReten =  $item['usaReten'];
    $fchDespachoFin  = $item['fchDespachoFin'];
    $fchDespachoFinCli  = $item['fchDespachoFinCli'];
    $usuarioGrabaFin = $item['usuarioGrabaFin'];
    $terceroConPersonalMoy =  $item['terceroConPersonalMoy'];
    $idProducto =  $item['idProducto'];
    $placa =  $item['placa'];
    $ptoOrigen =  $item['ptoOrigen'];
    $idSedeOrigen =  $item['idSedeOrigen'];
    $kmIni =  $item['kmInicio'];
    $kmIniCli = $item['kmInicioCliente'];
    $kmFinCli = $item['kmFinCliente'];
    $kmFin =  $item['kmFin'];
    $lugarFinCliente = $item['lugarFinCliente'];
    $observacion =  $item['observacion'];
    $guiaCliente =  $item['guiaCliente'];
    $guiaTrTercero =  $item['guiaTrTercero'];
    $docPagoTercero =  $item['docPagoTercero'];
    $tipoDocCobrar =   $item['tipoDocCobrar'];
    $docCobranzaCobrar =   $item['docCobranzaCobrar'];
    $modoCreacion =   $item['modoCreacion'];
  }

  if($idSedeOrigen != "")  $valorAuxPtoOrigen = $idSedeOrigen."|".$ptoOrigen; else $valorAuxPtoOrigen = "-";

  if ($fchDespachoFin == '0000-00-00') $fchDespachoFin = $fchDespacho;
  $kmFinAnt = $kmFinCliAnt = 0;
  $clienteCuentas = buscarClientesCuentas02($db, $idCliente); // buscarDataPdtos($db, $idCliente, 'Activo');
  $vehiculos = buscarTodosLosVehiculosActivos($db, $bloquearVehiculos);

  $conductores = buscarTodosLosConductores($db);
  $puntosOrigen = buscarSedes($db);

  $dataUltDespRegistrado = buscarDespachoAnt($db, $fchDespacho, $hraInicio, $placa);

  $auxReten = buscarTodosLosAuxiliaresConAlerta($db,$fchDespacho,$hraInicio);
  
  foreach ($dataUltDespRegistrado as $key => $value) {
    $kmFinAnt = $value['kmFin'];
    $kmFinCliAnt = $value['kmFinCliente'];
  }

  if ($tipoServicioPago == 'SoloPersonal'){
    $kmFinAnt = $kmFinCliAnt = 0;

  } else {
    if ($kmFinAnt == NULL) $kmFinAnt = $kmFinCliAnt;
  }
    $losAuxiliares = $cadJquery = "";

  $esReten = ""; 
  foreach ($datosTripulacion as $key => $value) {
    if ($value['tipoRol'] == 'Conductor' ){
      $idConductor = $value['idTrabajador'];
      $nombConductor = $value['nombreCompleto'];
    } elseif ($value['tipoRol'] == 'Auxiliar') {
      if ($value['esReten'] != 'Si') {
        $losAuxiliares .= $value['idTrabajador']."-".$value['nombreCompleto']."\n" ;
      } else {
        $esReten = $value['idTrabajador'];
      }
    }
  }
  require 'vistas/editaDespacho.php';
}

function listar2(){
  require 'vistas/listarDespachosDt.php';
}


?>
