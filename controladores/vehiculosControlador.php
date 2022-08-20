<?php
function listar(){
  require 'modelos/vehiculosModelo.php';
	$query = ' WHERE 1 ';
  $nroVehi = $placa = $var = $estado = "";
  if (isset($_POST['txtNro']) and $_POST['txtNro'] <>''){
    $nroVehi = $_POST['txtNro'];
    if (strpos ($nroVehi, "$")==0 and strpos ($nroVehi, "*") ==0 and strpos ($nroVehi, "'") ==0 ) $query = $query ." And nroVehiculo 	= '$nroVehi'";
  }
  if (isset($_POST['txtPlaca']) and $_POST['txtPlaca'] <>''){
    /*$var = $_POST['txtPlaca'];
    if (strpos ($var, "$")==0 and strpos ($var, "*") ==0 and strpos ($var, "'") ==0 ) $query = $query ." And idPlaca 	= '$var'";*/
    $placa = $_POST['txtPlaca'];
    if (strpos ($placa, "$")==0 and strpos ($placa, "*") ==0 and strpos ($placa, "'") ==0 ) $query = $query ." And idPlaca 	= '$placa'";
  }    
  if (isset($_POST['txtPropietario']) and $_POST['txtPropietario'] <>''){
    $var = $_POST['txtPropietario'];
    if (strpos ($var, "$")==0 and strpos ($var, "*") ==0) $query = $query ." And propietario 	like '%$var%'";
  }

  if (isset($_POST['txtEstado']) and $_POST['txtEstado'] <>''){
    $estado = $_POST['txtEstado'];
    if (strpos ($estado, "$")==0 and strpos ($estado, "*") ==0 and strpos ($estado, "'") ==0 ) $query = $query ." And estado  = '$estado'";
  }

  $limite = isset($_POST['txtLimite'])?$_POST['txtLimite']:'50';
	
	//Le pide al modelo todos los items
	$items = buscarVehiculos($db,$query,$limite);
	require 'vistas/listarVehiculos.php';
}

function nuevolistarVehiculos(){
  require 'vistas/listarVehiculosDt.php';
}

function nuevovehiculo() {
  require 'modelos/vehiculosModelo.php';
  require 'librerias/config.ini.php';
  $duenos = buscarDuenos($db);
  $gruposRendimiento= buscarGruposRendimiento($db);
  //$entidadPensiones = buscarEntidadPensiones($db);
  require 'vistas/nuevoVehiculo.php';
}

function editavehiculo() {
  $idPlaca = $_GET['id'];
  require 'modelos/vehiculosModelo.php';
  //
  require 'librerias/config.ini.php';
  $duenos = buscarDuenos($db);
  //
    
  $vehiculo = buscarVehiculo($db,$idPlaca);
  //$entidadPensiones = buscarEntidadPensiones($db);
  require 'vistas/editaVehiculo.htm';
}

function eliminavehiculo() {
  $idPlaca = $_GET['id'];
  require 'modelos/vehiculosModelo.php';
  //$vehiculo = buscarVehiculo($db,$idPlaca);
  //$entidadPensiones = buscarEntidadPensiones($db);
  require 'vistas/eliminaVehiculo.htm';
}

function vervehiculo(){
  $idPlaca = $_GET['id'];
  require 'modelos/vehiculosModelo.php';
  require 'librerias/config.ini.php';
  $duenos   = buscarDuenos($db);  
  $vehiculo = buscarVehiculo($db,$idPlaca);
  $licencias = buscarVehiculoLicencias($db,$idPlaca);
  $mantenimientos = buscarVehiculoMantenimientos($db,$idPlaca);
  $gruposRendimiento= buscarGruposRendimiento($db);
  $docsvariosadjun = buscarVehiculoDocsVarios($db,NULL,$idPlaca);
  $eficSolesKm = buscarEficSolesKm($db,"Activo");
  //$entidadPensiones = buscarEntidadPensiones($db);
  require 'vistas/verVehiculo.php';
}

function nuevalicencia(){
  $idPlaca = $_GET['id'];
  require 'modelos/vehiculosModelo.php';
  $licencias = buscarLicenciasVehiculosDefinicion($db, "Activo");
  $licencia = isset($_GET['nombre'])?$_GET['nombre']:'';
  require 'vistas/nuevaLicencia.htm';
}

function editalicencia() {
  require 'modelos/vehiculosModelo.php';
  $usuario = $_SESSION['usuario'];;
  $idPlaca = isset($_GET['idplaca'])?$_GET['idplaca']:$_POST['idPlaca'];
  $fchInicio = isset($_GET['fchinicio'])?$_GET['fchinicio']:$_POST['fchInicio'];
  $nombre  = isset($_GET['nombre'])?$_GET['nombre']:$_POST['nombre'];
  require 'vistas/editaLicencia.htm';
}

function eliminalicencia() {
  $idPlaca = $_GET['idplaca'];
  $fchInicio = $_GET['fchinicio'];
  $nombre = $_GET['nombre'];
  require 'vistas/eliminaLicencia.htm';
}

function nuevomantenimiento() {
  require 'librerias/config.ini.php';
  $idPlaca = $_GET['id'];
  $alerta = isset($_GET['alerta'])?$_GET['alerta']:'';
  require 'modelos/vehiculosModelo.php';
  //$vehiculo = buscarVehiculo($db,$idPlaca);
  $tipoMantenimiento = buscarTipoAlerta($db,'Vehiculo');
  //$entidadPensiones = buscarEntidadPensiones($db);
  $ultimoMantenAceite = buscarUltimoManten($db,$idPlaca,$alerta);
  require 'vistas/nuevoMantenimiento.htm';
}

function cambiarAdjunto(){
  $idPlaca = $_GET['idplaca'];
  $fchInicio = $_GET['fchinicio'];
  $nombre = $_GET['nombre'];
  require 'vistas/cambiarAdjuntoLicencia.php';
}

function editamantenimiento() {
  require 'librerias/config.ini.php';
  require 'modelos/vehiculosModelo.php';
  $usuario = $_SESSION['usuario'];;
  $idPlaca = isset($_GET['idplaca'])?$_GET['idplaca']:$_POST['idPlaca'];
  $marcaFin = isset($_GET['marcaFin'])?$_GET['marcaFin']:$_POST['marcaFin'];
  $alerta   = isset($_GET['alerta'])?$_GET['alerta']:$_POST['alerta'];
  $fchAtendido = isset($_POST['txtFchAtendido'])?$_POST['txtFchAtendido']:null;
  $aceiteMotor = (isset($_POST['txtAceiteMotor']) && $_POST['txtAceiteMotor'] != '')?$_POST['txtAceiteMotor']:null;
  $aceiteCaja  = (isset($_POST['txtAceiteCaja']) && $_POST['txtAceiteCaja'] != '')?$_POST['txtAceiteCaja']:null;
  $aceiteCorona = (isset($_POST['txtAceiteCorona']) && $_POST['txtAceiteCorona'] != '')?$_POST['txtAceiteCorona']:null;
  $filtroPetroleo = (isset($_POST['chkFiltroPetroleo']) && $_POST['chkFiltroPetroleo'] == 'Si')?$_POST['chkFiltroPetroleo']:null;
  $filtroAceite = (isset($_POST['chkFiltroAceite']) && $_POST['chkFiltroAceite'] == 'Si')?$_POST['chkFiltroAceite']:null;
  $filtroAire   = (isset($_POST['chkFiltroAire']) && $_POST['chkFiltroAire'] == 'Si')?$_POST['chkFiltroAire']:null;
  $filtroSeparador = (isset($_POST['chkFiltroSeparador']) && $_POST['chkFiltroSeparador'] == 'Si')?$_POST['chkFiltroSeparador']:null;
  $filtroAceiteTr = (isset($_POST['chkFiltroAceiteTr']) && $_POST['chkFiltroAceiteTr'] == 'Si')?$_POST['chkFiltroAceiteTr']:null;
  $cambiosOtros = (isset($_POST['txtOtros']) && $_POST['txtOtros'] != '')?$_POST['txtOtros']:null;
  $atencionMarcaKm = (isset($_POST['txtMarcaAtendido']) && $_POST['txtMarcaAtendido'] != '')?$_POST['txtMarcaAtendido']:$marcaFin;
  $personaResponsable = (isset($_POST['txtPersonaCargo']) && $_POST['txtPersonaCargo'] != '')?$_POST['txtPersonaCargo']:$usuario;

  $costoTotal = (isset($_POST['txtCostoTotal']) && $_POST['txtCostoTotal'] != '')?$_POST['txtCostoTotal']:NULL;
  $idProveedor = (isset($_POST['txtIdProveedor']) && $_POST['txtIdProveedor'] != '')?$_POST['txtIdProveedor']:NULL;
  $tipoDoc = (isset($_POST['cmbTipoDoc']) && $_POST['cmbTipoDoc'] != '-')?$_POST['cmbTipoDoc']:NULL;
  $nroDoc = (isset($_POST['txtNroDoc']) && $_POST['txtNroDoc'] != '')?$_POST['txtNroDoc']:NULL;
  
  //echo "Aceite Motor $aceiteMotor Caja $aceiteCaja Corona $aceiteCorona Filtro Petroleo $filtroPetroleo Aceite $filtroAceite ";
  $ultimoManten = buscarUltimoManten($db,$idPlaca,$alerta);
  require "vistas/editaMantenimiento$alerta.htm";
  
}

function eliminamantenimiento() {
  require 'modelos/vehiculosModelo.php';
  //$usuario = $_SESSION['usuario'];;
  $idPlaca = isset($_GET['idplaca'])?$_GET['idplaca']:$_POST['idPlaca'];
  $marcaFin = isset($_GET['marcaFin'])?$_GET['marcaFin']:$_POST['marcaFin'];
  $alerta  = isset($_GET['alerta'])?$_GET['alerta']:$_POST['alerta'];
  require 'vistas/eliminaMantenimiento.htm';
}

function nuevoTipoAlerta(){
  require 'vistas/nuevoTipoAlerta.htm';
}

function nuevotercero(){
  require 'vistas/nuevoTercero.htm';
}

function combustiblevehiculo() {
  $idPlaca = $_GET['id'];
  require 'modelos/vehiculosModelo.php';
  require 'librerias/config.ini.php';
  
  $datosVehiculo = buscarVehiculo($db,$idPlaca);
  $fecha = (isset($_POST['txtFch']))?$_POST['txtFch']:' curdate() ';                               
  $datosParaCombustible = buscarDatosParaCombustible($db,$idPlaca,'Conductor',$fecha);
  $buscarUnAuxiliarCombustible = buscarDatosParaCombustible($db,$idPlaca,'Auxiliar',$fecha );
  $datosAbastecimientoAnterior = buscarDatosAbastecimientoAnterior($db,$idPlaca);
  $galones = 0;
  if (isset($datosAbastecimientoAnterior)){
    foreach($datosAbastecimientoAnterior as $item){
      $galones = $item['galones'];
      $datosAtencionesAbastecimientoAnterior = buscarAtencionesAbastecimientoAnterior($db,$idPlaca,$item['tiempo']);
    }
  } 
  //$entidadPensiones = buscarEntidadPensiones($db);
  require 'vistas/nuevoCombustible.htm';
}


function eliminacombustibleanterior(){
  $idPlaca = $_GET['id'];
  require 'modelos/vehiculosModelo.php';
  require 'modelos/trabajadoresModelo.php';//Es para utilizar el logAccion
  require 'librerias/config.ini.php';
  
  
  
    eliminaAbastecimientoAnterior($db,$idPlaca);
    logAccion($db,"Se eliminó el último registro de abastecimiento",null,$idPlaca);
    $url= "index.php?controlador=vehiculos&accion=combustiblevehiculo&id=$idPlaca";
    require 'vistas/redireccionar.php';

}



function ingreseperiodo(){
  require 'vistas/ingresePeriodo.htm';
}

function verrendimiento(){
  require 'modelos/vehiculosModelo.php';
  require 'librerias/config.ini.php';  
  $fchIni = $_POST['txtFchIni'];
  $fchFin = $_POST['txtFchFin'];
  $data = calcularRendimientoCombustiblePeriodo($db,$fchIni,$fchFin);
  require 'vistas/reporteRendimientoCombustible.php';
}

function listarAbastecimiento(){
  require 'modelos/vehiculosModelo.php';
  require 'librerias/config.ini.php';

  if (isset($_POST['txtFchAbIni']) && $_POST['txtFchAbIni'] != '')
    $fchIni = $_POST['txtFchAbIni'];
  else
    $fchIni = (isset($_POST['txtFchIni']))?$_POST['txtFchIni']:Date("Y-m-d");

  if (isset($_POST['txtFchAbFin']) && $_POST['txtFchAbFin'] != '')
    $fchFin = $_POST['txtFchAbFin'];
  else
    $fchFin = (isset($_POST['txtFchFin']))?$_POST['txtFchFin']:Date("Y-m-d");
  $grifo = (isset($_POST['txtGrifo']))?$_POST['txtGrifo']:"";
  $nroVale = (isset($_POST['txtNroVale']))?$_POST['txtNroVale']:"";
  $placa = (isset($_POST['txtPlaca']))?$_POST['txtPlaca']:"";
  $servicio = (isset($_POST['txtCuenta']))?$_POST['txtCuenta']:"";
  //$chofer = (isset($_POST['txtChofer']))?$_POST['txtChofer']:"";
  //echo "Fch Ini $fchIni  FchFin $fchFin Grifo $grifo NroVale $nroVale Placa $placa Servicio $servicio ";
  $items = buscarAbastecimiento($db,$fchIni,$fchFin,$grifo,$nroVale,$placa,$servicio);
  
  require 'vistas/listarAbastecimiento.htm';
}

function repoHistoricoalertas(){
  require 'modelos/vehiculosModelo.php';
  require 'librerias/config.ini.php';
  $placa = (isset($_POST['cmbPlaca']))?$_POST['cmbPlaca']:"";
  $alerta = (isset($_POST['txtAlerta']))?$_POST['txtAlerta']:"";
  $estado = (isset($_POST['cmbEstado']))?$_POST['cmbEstado']:"";
  $fchCreacion = (isset($_POST['txtFchCreacion']))?$_POST['txtFchCreacion']:"";
  $limite = (isset($_POST['txtLimite']))?$_POST['txtLimite']:"100";
    //$chofer = (isset($_POST['txtChofer']))?$_POST['txtChofer']:"";
  //echo "Fch Ini $fchIni  FchFin $fchFin Grifo $grifo NroVale $nroVale Placa $placa Servicio $servicio ";
  $items = buscarHistoricoalertas($db,$placa,$alerta,$estado,$fchCreacion,$limite);
  
  require 'vistas/repoHistoricoAlertas.php';
}


function verrepoaceite(){
  require 'modelos/vehiculosModelo.php';
  require_once 'librerias/config.ini.php';  

  if (isset($_POST['txtAceiteFchIniBarr']) && $_POST['txtAceiteFchIniBarr'] != '')
    $fchIni = $_POST['txtAceiteFchIniBarr'];
  else
    $fchIni = (isset($_POST['txtAceiteFchIni']))?$_POST['txtAceiteFchIni']:Date("Y-m-d");

  if (isset($_POST['txtAceiteFchFinBarr']) && $_POST['txtAceiteFchFinBarr'] != '')
    $fchFin = $_POST['txtAceiteFchFinBarr'];
  else
    $fchFin = (isset($_POST['txtAceiteFchFin']))?$_POST['txtAceiteFchFin']:Date("Y-m-d");


  $placa = (isset($_POST['txtPlaca']))?$_POST['txtPlaca']:"";
  //echo "$fchIni, $fchFin";
  
  $items = buscarCambiosAceite($db,$fchIni,$fchFin,$placa);
  
  require 'vistas/reporteCambioAceite.htm';
}


function repocombustibledetalles(){
  require 'modelos/vehiculosModelo.php';
  require_once 'librerias/config.ini.php';

  if (isset($_POST['txtFchIniComb']) && $_POST['txtFchIniComb'] != '')
    $fchIni = $_POST['txtFchIniComb'];
  else
    $fchIni = (isset($_POST['txtFchIniComb']))?$_POST['txtFchIniComb']:((isset($_GET['txtFchIniComb']))?$_GET['txtFchIniComb']:Date("Y-m-d"));

  if (isset($_POST['txtFchFinComb']) && $_POST['txtFchFinComb'] != '')
    $fchFin = $_POST['txtFchFinComb'];
  else
    $fchFin = (isset($_POST['txtFchFinComb']))?$_POST['txtFchFinComb']:((isset($_GET['txtFchFinComb']))?$_GET['txtFchFinComb']:Date("Y-m-d"));

  if (isset($_POST['txtPlacaComb']) && $_POST['txtPlacaComb'] != '')
    $placa = $_POST['txtPlacaComb'];
  else
    $placa = (isset($_POST['txtPlacaComb']))?$_POST['txtPlacaComb']:((isset($_GET['txtPlacaComb']))?$_POST['txtPlacaComb']:NULL);



  
  
  
  $abastecimientos = buscarAbastecPeriodos($db,$fchIni,$fchFin,$placa);  
  require 'vistas/reporteCombustibleDetalles.php';
}

function repoMantenimientoForma1(){
  require 'modelos/vehiculosModelo.php';
  require_once 'librerias/config.ini.php';

  if (isset($_POST['txtFchIniM1']) && $_POST['txtFchIniM1']!= '' )
    $fchIni = $_POST['txtFchIniM1'];
  else if (isset($_POST['txtFchIniM2']))
    $fchIni = $_POST['txtFchIniM2'];
  else
    $fchIni = Date("Y-m-d");

  if (isset($_POST['txtFchFinM1']) && $_POST['txtFchFinM1']!= '' )
    $fchFin = $_POST['txtFchFinM1'];
  else if (isset($_POST['txtFchFibM2']))
    $fchFin = $_POST['txtFchFinM2'];
  else
    $fchFin = Date("Y-m-d");

  $placa  = (isset($_POST['txtPlacaM1']))?$_POST['txtPlacaM1']:((isset($_POST['txtPlacaM2']))?$_POST['txtPlacaM2']:NULL);
  $estado = (isset($_POST['cmbEstadoM1']))?$_POST['cmbEstadoM1']:((isset($_POST['cmbEstadoM2']))?$_POST['cmbEstadoM2']:'Todos');
  $mantenimientos = buscarMantenimientoPeriodos($db,$fchIni,$fchFin,$placa,$estado);  
  require 'vistas/reporteMantenimientoForma1.php';
}

function repoMantenimientoForma2(){
  require 'modelos/vehiculosModelo.php';
  require_once 'librerias/config.ini.php';

  if (isset($_POST['txtFchIniM2Barr']) && $_POST['txtFchIniM2Barr'] != '')
    $fchIni = $_POST['txtFchIniM2Barr'];
  else if (isset($_POST['txtFchIniM2']) && $_POST['txtFchIniM2']!= '' )
    $fchIni = $_POST['txtFchIniM2'];
  else if (isset($_POST['txtFchIniM1']) && $_POST['txtFchIniM1']!= ''  )
    $fchIni = $_POST['txtFchIniM1'];
  else
    $fchIni = Date("Y-m-d");

  if (isset($_POST['txtFchFinM2Barr']) && $_POST['txtFchFinM2Barr'] != '')
    $fchFin = $_POST['txtFchFinM2Barr'];
  else if (isset($_POST['txtFchFinM2']) && $_POST['txtFchFinM2']!= '' )
    $fchFin = $_POST['txtFchFinM2'];
  else if (isset($_POST['txtFchFinM1']) && $_POST['txtFchFinM1']!= '' )
    $fchFin = $_POST['txtFchFinM1'];
  else
    $fchFin = Date("Y-m-d");
  $placa  = (isset($_POST['txtPlacaM2']))?$_POST['txtPlacaM2']:((isset($_POST['txtPlacaM1']))?$_POST['txtPlacaM1']:NULL);
  $estado = (isset($_POST['cmbEstadoM2']))?$_POST['cmbEstadoM2']:((isset($_POST['cmbEstadoM1']))?$_POST['cmbEstadoM1']:'Todos');

  $mantenimientos = buscarMantenimientoPeriodos($db,$fchIni,$fchFin,$placa,$estado);  
  require 'vistas/reporteMantenimientoForma2.php';
}

function grafRendimiento(){
  require 'modelos/vehiculosModelo.php';
  require 'librerias/phplot-5.8.0/phplot.php';
  
  $placa1 = $_POST['placa1'];
  $placa2 = $_POST['placa2'];
  $placa3 = $_POST['placa3'];
  $fchIni = '2012-01-01';
  $fchFin = '2012-03-30';
  
  $abast1 = abastecimientos($db,$placa1,$fchIni,$fchFin);
  $abast2 = abastecimientos($db,$placa2,$fchIni,$fchFin);
  $abast3 = abastecimientos($db,$placa3,$fchIni,$fchFin);
  require 'vistas/grafRendimiento.php';
}


function repoRecorrido(){
  require 'modelos/vehiculosModelo.php';
  if (isset($_POST['txtRecorFchIniBarr']) && $_POST['txtRecorFchIniBarr'] != '')
    $fchIni = $_POST['txtRecorFchIniBarr'];
  else
    $fchIni = (isset($_POST['txtFchIni']))?$_POST['txtFchIni']:Date("Y-m-d");

  if (isset($_POST['txtRecorFchFinBarr']) && $_POST['txtRecorFchFinBarr'] != '')
    $fchFin = $_POST['txtRecorFchFinBarr'];
  else
    $fchFin = (isset($_POST['txtFchFin']))?$_POST['txtFchFin']:Date("Y-m-d");

  $placa =  (isset($_POST['txtPlaca']))?trim($_POST['txtPlaca']):NULL;
  $placa2 = null;
  if ($placa != NULL){
    $placa2 = str_replace(", ","','",$placa);
    $placa2 = "'".substr($placa2,0,strlen($placa2)-1)."'";
  }

  $placa2 = str_replace(array('*',';','SHOW'),"",$placa2);

  $cliente =  (isset($_POST['txtCliente']))?trim($_POST['txtCliente']):NULL;
  $cliente2 = strtok($cliente,"-");

  $propietario = (isset($_POST['txtPropietario']))?trim($_POST['txtPropietario']):NULL;

  //echo $placa."\n";
  $items = buscarRecorridos($db,$fchIni,$fchFin,$placa2,$cliente2,$propietario);

  // echo $placa2;


  require 'vistas/reporteRecorrido.php';
}

function grafRendimPromedio(){
  if (isset($_POST['txtFchIniRendPromBarr']) && $_POST['txtFchIniRendPromBarr'] != '')
    $fchIni = $_POST['txtFchIniRendPromBarr'];
  else
    $fchIni = (isset($_POST['txtFchIni']))?$_POST['txtFchIni']:Date("Y-m-d");

  if (isset($_POST['txtFchFinRendPromBarr']) && $_POST['txtFchFinRendPromBarr'] != '')
    $fchFin = $_POST['txtFchFinRendPromBarr'];
  else
    $fchFin = (isset($_POST['txtFchFin']))?$_POST['txtFchFin']:Date("Y-m-d");

  echo $fchIni."  ".$fchFin;

  require 'vistas/grafRendimPromedio.php';
}

function graficaPorGrupo(){
  require 'modelos/vehiculosModelo.php';
  require 'vistas/grafRendimientoPorGrupo.php';

}


function graficaPorGrupoNoSeUsa(){

  $fchIni = $_GET['fchIni'];
  $fchFin = $_GET['fchFin'];
/*
  $placas = buscarPlacas($db,$fchIni,$fchFin);
  
  foreach($placas as $unaPlaca){
    $placa =  $unaPlaca['idPlaca'];
    $_SESSION[$placa]='No'; 
  }
  $arrayPlacasTodas =array();
  $cont = 0;
  foreach($placas as $unaPlaca){
    $placa =  $unaPlaca['idPlaca'];
    $concatenado =  $unaPlaca['concatenado'];
    $arrayPlacasTodas[] = $placa;
    if (isset($_SESSION[$placa]) && $_SESSION[$placa]=='No') continue;
    $arrayPlacas[] = $concatenado;
    $abast = abastecimientosPromedioMes($db,$placa,$fchIni,$fchFin);
    foreach($abast as $item) {
      $cant = $item['cantidad'];
      $fch =  $item['fcha'];
      //echo "FECHA $fch  CANTIDAD $cant  CONTADOR $cont";
      if ($item['promedio'] >= 5 && $item['promedio'] <= 200 )
         $data[$fch][$cont] = $item['promedio'];
      else 
         $data[$fch][$cont] = '';
      $ejeX[$fch]=$fch;
     // echo $ejeX[$fch];
    }
    $cont++;
  }
*/
  $arrPlacas =   array("A12-455" ,"B12-345" ,"C12-455" ,"D12-455" ,"E12-455");
  $arrFechas =   array("2015-01","2015-02","2015-03","2015-04","2015-05","2015-06","2015-07","2015-08");

  $arrValores =  array(
    array(32.56, 14.25, 15.25, 85.14, 80.56, 71.25, 25.12, 19.14),
    array(18.56, 21.25, 77.25, 45.14, 19.56, 35.25, 81.12, 14.14),
    array(45.56, 11.25, 35.25, 80.14, 40.56, 42.25, 35.12, 17.14),
    array(56.00, 34.25, 25.40, 14.50, 50.56, 60.25, 22.12, 29.14),
    array(13.56, 18.25, 25.25, 75.14, 81.56, 61.25, 45.12, 84.14),
    array(35.56, 41.25, 42.52, 35.14, 80.56, 71.25, 25.12, 19.14),
    array(23.56, 50.25, 15.50, 84.14, 60.31, 25.34, 12.35, 34.14),
    array(42.56, 13.25, 16.95, 34.24, 82.56, 71.25, 52.15, 31.14),
    

    );

  $arrAuxPrueba =array("labels" => $arrFechas, "valores" => $arrValores, "etiquetas" => $arrPlacas);
  

  /*echo '<pre>';
        print_r($arrAuxPrueba);
        echo '</pre>';
        
        exit;*/
  echo json_encode($arrAuxPrueba);

}

function nuevodocumentoadjunto(){
  require 'modelos/vehiculosModelo.php';
  $idPlaca = $_GET['id'];
  require 'vistas/nuevoDocAdjuntoVehiculo.php';
}

function editadocumentoadjunto(){
  require 'modelos/vehiculosModelo.php';
  $id = $_REQUEST['id'];
  $datos = buscarVehiculoDocsVarios($db,$id);
  require 'vistas/editaDocAdjuntoVehiculo.php';
}

function eliminadocumentoadjunto(){
  require 'modelos/trabajadoresModelo.php';
  require 'modelos/vehiculosModelo.php';
  $id = $_REQUEST['id'];
  $datos = buscarVehiculoDocsVarios($db,$id);
  require 'vistas/eliminaDocAdjuntoVehiculo.php';
}

function cambiarAdjuntoDocumento(){
  require 'modelos/vehiculosModelo.php';
  $id = $_REQUEST['id'];
  $datos = buscarVehiculoDocsVarios($db,$id);
  require 'vistas/cambiarAdjuntoDocumentoVehiculo.php';
}

function verCargaMasivaAbastecimiento(){
  require 'vistas/cargaMasivaAbastecimiento.php';
}

function cargaMasivaAbastecimiento(){
  $fch = Date("Y-m-d");
  $deboValidar = "No"; //"No"; //"Si";
  function validaHoras($time){
    $time = $time.":00";
    $time = substr($time, 0, 8);
    $pattern="/^([0-1][0-9]|[2][0-3])[:]([0-5][0-9])[:]([0-5][0-9])$/";
    if(preg_match($pattern,$time)) return true;
    return false;
  }

  require 'librerias/config.ini.php';
  require 'modelos/vehiculosModelo.php';
  require 'modelos/trabajadoresModelo.php';
  $usuario = $_SESSION['usuario'];
  $_SESSION["arrDataCombust"] = array();
  $_SESSION["todoCorrecto"] = "Si";

  $nombre_archivo = $_FILES['nombArchivo']['name'];
  $tipo_archivo = $_FILES['nombArchivo']['type'];
  $tamano_archivo = $_FILES['nombArchivo']['size'];
  //$fch = isset($_POST['txtFch'])?$_POST['txtFch']:Date("Y-m-d");
  //$responsable = $_POST['cmbRespons'];
  $nombre_archivo = "./adjuntos/".$nombre_archivo;
  //vaciarTablaCargaRendicion($db);
  if (move_uploaded_file($_FILES['nombArchivo']['tmp_name'], $nombre_archivo)){
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
    $tipoRol = "Conductor";
    $estado = "Activo";
    $considerarPropio = "Si";
    $estadoVehiculo = "Activo";

    if($deboValidar == "Si") $dataConductores = buscarTrabajadoresOpciones($db, $tipoRol, $estado);
    else $dataConductores = buscarTrabajadoresOpciones($db, $tipoRol);

    if($deboValidar == "Si") $dataPlacas = buscarVehiculosOpciones($db, $considerarPropio ,$estadoVehiculo);
    else  $dataPlacas = buscarVehiculosOpciones($db, $considerarPropio);
    //$dataPlacas = buscarVehiculosOpciones($db, $considerarPropio );

    $arrConductores = array();
    foreach ($dataConductores as $key => $value) {
      $arrConductores[$value["idTrabajador"]] = $value["idTrabajador"];
    }
    $arrPlacas = array();
    $arrPlacasEfic = array();
    foreach ($dataPlacas as $key => $value) {
      $arrPlacas[$value["idPlaca"]] = $value["kmAnterior"];
      $arrPlacasEfic[$value["idPlaca"]] = $value["valorEsperado"];
    }
  }

  $fila = -1;
  foreach ($objWorksheet->getRowIterator() as $row) {
    $fila++;
    $_SESSION["arrDataCombust"][$fila][13] = 0;
    $_SESSION["arrDataCombust"][$fila][14] = 0;
    $_SESSION["arrDataCombust"][$fila][15] = "";
    $_SESSION["arrDataCombust"][$fila][16] = 0;

    $_SESSION["arrDataCombust"][$fila][20] = "normal";//Color columna fecha
    $_SESSION["arrDataCombust"][$fila][21] = "normal";//Color columna hora
    $_SESSION["arrDataCombust"][$fila][22] = "normal";//Color columna placa
    $_SESSION["arrDataCombust"][$fila][23] = "normal";//Color columna dni
    $_SESSION["arrDataCombust"][$fila][30] = "normal";//Color columna recorrido


    $cellIterator = $row->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false); // This loops all cells,
    $primerCampo = 'Si';
    $columna = 0;      
    foreach ($cellIterator as $cell) {
      if ($columna == 0)
        $unDato =  PHPExcel_Style_NumberFormat::toFormattedString($cell->getValue(), "YYYY-mm-dd");
      else if ($columna == 1)
        $unDato =  PHPExcel_Style_NumberFormat::toFormattedString($cell->getValue(), "hh:i:s");
      else 
        $unDato = utf8_decode($cell->getValue());
      
      $_SESSION["arrDataCombust"][$fila][$columna] = $unDato;
      if($unDato == ""){
        $_SESSION["arrDataCombust"][$fila][15] .= "Faltan datos.";
        $_SESSION["todoCorrecto"] = "No";
      }

      if($fila != 0 && $columna == 2){
        if (!array_key_exists($unDato, $arrPlacas)){//Placa
          $_SESSION["arrDataCombust"][$fila][15] .= "Placa no existe o no está disponible.";
          $_SESSION["todoCorrecto"] = "No";
          $_SESSION["arrDataCombust"][$fila][22] = "rojo";
          $auxKm = -1;
        } else {
          $auxKm = $arrPlacas[$unDato];
          $auxPlaca = $unDato;
          $_SESSION["arrDataCombust"][$fila][13] = $arrPlacas[$unDato];
          $_SESSION["arrDataCombust"][$fila][16] = $arrPlacasEfic[$unDato];

        }
      } else if ($fila != 0 && $columna == 3 && !in_array($unDato, $arrConductores)){
        if($deboValidar == "Si"){
          $_SESSION["arrDataCombust"][$fila][15] .= "DNI del conductor no existe o no está disponible.";
          $_SESSION["todoCorrecto"] = "No";
          $_SESSION["arrDataCombust"][$fila][23] = "rojo";
        }
      } else if($fila != 0 && $columna == 10){//Km
        if($auxKm == -1) {
          $_SESSION["arrDataCombust"][$fila][13] = -1;
        } else {
          //echo "un dato $unDato,  aucPlaca $auxPlaca,  arrPlacas".$arrPlacas[$auxPlaca];
          if($arrPlacas[$auxPlaca] == "") $arrPlacas[$auxPlaca] = 0;
          if($deboValidar == "Si"){
            if(1*$unDato <  1*$arrPlacas[$auxPlaca] && $arrPlacas[$auxPlaca] != NULL  ){
              $_SESSION["arrDataCombust"][$fila][15] .= "Error en el kilometraje, es menor o igual al anterior ingresado.";
              $_SESSION["todoCorrecto"] = "No";
              $_SESSION["arrDataCombust"][$fila][30] = "rojo";
            } else if (1*$unDato - 1*$arrPlacas[$auxPlaca] > $difKmMaxEntreAbastecimientos && $arrPlacas[$auxPlaca] != NULL  ){
              $_SESSION["arrDataCombust"][$fila][15] .= "Error en el kilometraje, hay una diferencia mayor a los $difKmMaxEntreAbastecimientos admitidos.";
              $_SESSION["todoCorrecto"] = "No";
              $_SESSION["arrDataCombust"][$fila][30] = "rojo";
            } 
          }

          if ( $arrPlacas[$auxPlaca] != NULL ){
            if(1*$unDato == "" || 1*$unDato == 0) $unDato =  $arrPlacas[$auxPlaca]; //Si no viene kilometraje
            $_SESSION["arrDataCombust"][$fila][13] = 1*$unDato - 1*$arrPlacas[$auxPlaca];
            $_SESSION["arrDataCombust"][$fila][14] = $arrPlacas[$auxPlaca];
            $arrPlacas[$auxPlaca] = $unDato;       
          } else {
            $_SESSION["arrDataCombust"][$fila][13] = 100;//Es valorpor default
            $_SESSION["arrDataCombust"][$fila][14] = $arrPlacas[$auxPlaca];
            $arrPlacas[$auxPlaca] = $unDato;
          }      
        }
      } else if($fila == 0 && $columna == 0 ) {
        $_SESSION["arrDataCombust"][$fila][13] = "Recorrido";
        $_SESSION["arrDataCombust"][$fila][14] = "Km anterior";
        $_SESSION["arrDataCombust"][$fila][15] .= "Observaciones";
        $_SESSION["arrDataCombust"][$fila][16] = "Efic Esperada";
      } else if($fila != 0 && $columna == 0){
        if($fch < $unDato){
          $_SESSION["arrDataCombust"][$fila][15] .= "La fecha de abastecimiento no puede ser mayor a la actual.";
          $_SESSION["todoCorrecto"] = "No";
          $_SESSION["arrDataCombust"][$fila][20] = "rojo";

        } else {
          $fecha1= new DateTime($unDato);//Fecha menor
          $fecha2= new DateTime($fch);//Fecha mayor
          $diff = $fecha1->diff($fecha2);
          $days = $diff->format("%a");
          if($days > $limFchAbast && $deboValidar == 'Si'){
            $_SESSION["arrDataCombust"][$fila][15] .= "No puede registrar fechas con $limFchAbast días de antiguedad.";
            $_SESSION["todoCorrecto"] = "No";
            $_SESSION["arrDataCombust"][$fila][20] = "rojo";
          }
        }
      } else if($fila != 0 && $columna == 1 && !validaHoras($unDato) ){
        $_SESSION["arrDataCombust"][$fila][15] .= "La hora no es válida.";
        $_SESSION["todoCorrecto"] = "No";
        $_SESSION["arrDataCombust"][$fila][21] = "rojo";
      };
      $columna++;
    }
  }

  $_SESSION["nroFilaAbast"] = $fila;
  //$_SESSION["todoCorrecto"] = "Si";
  echo "Espere unos momentos para ser redirigido<br><br>";
  $url = "index.php?controlador=vehiculos&accion=verCargaMasivaAbastecimiento";
  require 'vistas/redireccionar.php';
}

function almacenarCargaMasivaAbastecimiento(){
  require 'modelos/vehiculosModelo.php';
  echo cargarAbastecimientos($db);
  unset($_SESSION['todoCorrecto']);
  $url = "index.php?controlador=vehiculos&accion=verCargaMasivaAbastecimiento";
  require 'vistas/redireccionar.php';
  
}

function listarEficEsperadaSolesKm(){
  require 'vistas/repoVehiculoEficSolesKm.php';
}


function agregar()
{
	echo 'Aqui incluiremos nuestro formulario para insertar items';

}
?>
